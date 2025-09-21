import express from 'express';
import bodyParser from 'body-parser';
import dotenv from 'dotenv';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createServer } from 'node:http';
import { Server as SocketIOServer } from 'socket.io';

import { connectDB } from './config/database.js';
import { initModels } from './models/index.js';
import createAuthRoutes from './routes/authRoutes.js';
import createUserRoutes from './routes/userRoutes.js';
import createBloodRequestRoutes from './routes/bloodRequestRoutes.js';
import createBloodDonationRoutes from './routes/bloodDonationRoutes.js';
import createAppointmentRoutes from './routes/appointmentRoutes.js';
import createInventoryRoutes from './routes/inventoryRoutes.js';
import createDashboardRoutes from './routes/dashboardRoutes.js';
import createAuthHelpers from './utils/auth.js';
import { createScheduler } from './utils/scheduler.js';
import { sendMail } from './services/emailService.js';

dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const pagesDir = path.join(__dirname, 'pages');

function toBoolean(value) {
  if (value == null) {
    return false;
  }
  const normalised = value.toString().trim().toLowerCase();
  return ['1', 'true', 'yes', 'on'].includes(normalised);
}

const app = express();
const server = createServer(app);
const io = new SocketIOServer(server, {
  cors: {
    origin: '*'
  }
});

const PORT = Number(process.env.PORT || 4000);
const JWT_SECRET = process.env.JWT_SECRET || 'change-me';

const allowedOrigins = process.env.APP_ORIGIN
  ? process.env.APP_ORIGIN.split(',').map((origin) => origin.trim()).filter(Boolean)
  : null;

app.use((req, res, next) => {
  const origin = req.headers.origin;
  if (!origin || !allowedOrigins || allowedOrigins.length === 0 || allowedOrigins.includes(origin)) {
    res.setHeader('Access-Control-Allow-Origin', origin || '*');
  }
  res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PATCH,PUT,DELETE,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
  if (req.method === 'OPTIONS') {
    res.status(204).end();
    return;
  }
  next();
});

app.use(bodyParser.json({ limit: '1mb' }));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(pagesDir));

const scheduler = createScheduler({
  mongoUrl: process.env.MONGO_URL || 'mongodb://127.0.0.1/bloodvault-jobs',
  processEvery: process.env.AGENDA_PROCESS_EVERY || '30 seconds',
  disabled: toBoolean(process.env.AGENDA_DISABLED)
});

const models = initModels();
const { User, BloodRequest, BloodDonation, Appointment, BloodBank } = models;
const authHelpers = createAuthHelpers({ jwtSecret: JWT_SECRET });

scheduler.define('send-email-verification', async (job) => {
  const { userId, token } = job.attrs.data || {};
  if (!userId || !token) {
    return;
  }

  const user = await User.findByPk(userId);
  if (!user || !user.email) {
    return;
  }

  const verificationUrl = `${process.env.APP_URL || 'http://localhost:3000'}/verify-email?token=${token}`;
  await sendMail({
    to: user.email,
    subject: 'Verify your BloodVault email address',
    text: `Hello ${user.name || 'there'},\n\nPlease verify your email address by visiting ${verificationUrl}.\n\nThank you!`
  });
});

scheduler.define('broadcast-blood-availability', async () => {
  const available = await BloodBank.scope('available').findAll({
    attributes: ['id', 'bloodType', 'quantity', 'expirationDate']
  });
  io.emit('blood-bank:availability', available.map((record) => record.toJSON()));
});

scheduler.define('remind-upcoming-appointments', async () => {
  const upcoming = await Appointment.scope('upcoming').findAll({
    limit: 10,
    include: [{ model: User, as: 'user', attributes: ['email', 'name'] }]
  });

  await Promise.all(
    upcoming.map(async (appointment) => {
      if (!appointment.user?.email) {
        return;
      }

      await sendMail({
        to: appointment.user.email,
        subject: 'Upcoming BloodVault appointment reminder',
        text: `Hello ${appointment.user.name || 'there'},\n\n` +
          `This is a reminder for your ${appointment.appointmentType} appointment on ` +
          `${appointment.appointmentDate?.toISOString?.() || appointment.appointmentDate}.`
      });
    })
  );
});

app.get('/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.use('/auth', createAuthRoutes({ models, jwtSecret: JWT_SECRET }));
app.use('/users', createUserRoutes({ models, authHelpers, scheduler }));
app.use('/blood-requests', createBloodRequestRoutes({ models, authHelpers, io }));
app.use('/blood-donations', createBloodDonationRoutes({ models, authHelpers, io }));
app.use('/appointments', createAppointmentRoutes({ models, authHelpers, io }));
app.use('/inventory', createInventoryRoutes({ models, authHelpers }));
app.use('/dashboard', createDashboardRoutes({ models, authHelpers }));

const API_PREFIXES = [
  '/auth',
  '/users',
  '/blood-requests',
  '/blood-donations',
  '/appointments',
  '/inventory',
  '/dashboard',
  '/health',
  '/socket.io'
];

app.get('*', (req, res, next) => {
  if (req.method !== 'GET') {
    return next();
  }
  if (API_PREFIXES.some((prefix) => req.path.startsWith(prefix))) {
    return next();
  }
  res.sendFile(path.join(pagesDir, 'index.html'));
});

io.on('connection', (socket) => {
  console.log('Socket connected', socket.id);
  socket.on('disconnect', () => console.log('Socket disconnected', socket.id));
});

const MAX_DB_RETRIES = 5;

export const startServer = async () => {
  let attempt = 0;

  while (attempt < MAX_DB_RETRIES) {
    try {
      console.log(`Attempting to connect to the database (Attempt ${attempt + 1}/${MAX_DB_RETRIES})...`);
      await connectDB();
      console.log('Database connection successful.');

      const schedulerStarted = await scheduler.start();
      if (schedulerStarted) {
        await scheduler.every('1 hour', 'broadcast-blood-availability');
        await scheduler.every('1 day', 'remind-upcoming-appointments');
      }

      await new Promise((resolve) => {
        server.listen(PORT, '0.0.0.0', () => {
          console.log(`BloodVault Node API listening on port ${PORT}`);
          resolve();
        });
      });

      return server;
    } catch (error) {
      console.error(`Failed to start application on attempt ${attempt + 1}:`, error);
      attempt += 1;
      await scheduler.stop();
      if (attempt >= MAX_DB_RETRIES) {
        console.error('All attempts to start the server have failed. Exiting.');
        process.exit(1);
      }
      const delay = Math.pow(2, attempt) * 1000;
      console.log(`Retrying in ${delay / 1000} seconds...`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }

  return null;
};

process.on('SIGINT', async () => {
  console.log('Shutting down gracefully...');
  await scheduler.stop();
  server.close(() => process.exit(0));
});

startServer();

export { app, server, io };
