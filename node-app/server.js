import http from 'node:http';
import express from 'express';
import dotenv from 'dotenv';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { Server as SocketIOServer } from 'socket.io';
import Agenda from 'agenda';

import sequelize from './config/database.js';
import { initModels, getModels } from './models/index.js';
import {
  sendRequestStatusUpdate,
  sendDonationStatusUpdate,
  sendAppointmentConfirmation,
  sendAppointmentRejection,
  sendAppointmentStatusUpdate
} from './services/notificationService.js';
import { sendMail } from './services/emailService.js';

dotenv.config();

const app = express();
const server = http.createServer(app);
const io = new SocketIOServer(server, {
  cors: {
    origin: '*'
  }
});

app.use(express.json());

const JWT_SECRET = process.env.JWT_SECRET || 'change-me';
const PORT = Number(process.env.PORT || 4000);

initModels();
const models = getModels();
const { User, BloodRequest, BloodDonation, Appointment, BloodBank } = models;

const agenda = new Agenda({
  db: { address: process.env.MONGO_URL || 'mongodb://127.0.0.1/bloodvault-jobs' },
  processEvery: process.env.AGENDA_PROCESS_EVERY || '30 seconds'
});

agenda.define('send-email-verification', async (job) => {
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

agenda.define('broadcast-blood-availability', async () => {
  const available = await BloodBank.scope('available').findAll({
    attributes: ['id', 'bloodType', 'quantity', 'expirationDate']
  });
  io.emit('blood-bank:availability', available.map((record) => record.toJSON()));
});

agenda.define('remind-upcoming-appointments', async () => {
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

function authenticate(req, res, next) {
  const header = req.headers.authorization;
  if (!header) {
    return res.status(401).json({ message: 'Missing Authorization header' });
  }

  const token = header.replace(/^Bearer\s+/i, '');

  try {
    const payload = jwt.verify(token, JWT_SECRET);
    req.user = payload;
    next();
  } catch (error) {
    res.status(401).json({ message: 'Invalid token' });
  }
}

app.get('/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.post('/auth/register', async (req, res) => {
  try {
    const { email, password, name, usertype } = req.body;
    const existing = await User.findOne({ where: { email } });
    if (existing) {
      return res.status(409).json({ message: 'Email already in use' });
    }

    const hashedPassword = await bcrypt.hash(password, 10);
    const user = await User.create({ email, password: hashedPassword, name, usertype });
    const token = jwt.sign({ sub: user.id, role: user.usertype }, JWT_SECRET, { expiresIn: '1h' });

    res.status(201).json({ id: user.id, email: user.email, token });
  } catch (error) {
    console.error('Registration failed', error);
    res.status(500).json({ message: 'Registration failed' });
  }
});

app.post('/auth/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    const user = await User.unscoped().findOne({ where: { email } });
    if (!user) {
      return res.status(401).json({ message: 'Invalid credentials' });
    }

    const valid = await bcrypt.compare(password, user.password);
    if (!valid) {
      return res.status(401).json({ message: 'Invalid credentials' });
    }

    const token = jwt.sign({ sub: user.id, role: user.usertype }, JWT_SECRET, { expiresIn: '1h' });
    res.json({ token, user: { id: user.id, email: user.email, name: user.name, usertype: user.usertype } });
  } catch (error) {
    console.error('Login failed', error);
    res.status(500).json({ message: 'Login failed' });
  }
});

app.post('/users/:id/request-email-verification', authenticate, async (req, res) => {
  try {
    const targetId = Number(req.params.id);
    if (req.user.sub !== targetId && req.user.role !== 'admin') {
      return res.status(403).json({ message: 'Forbidden' });
    }

    const user = await User.findByPk(targetId);
    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    const token = await user.generateEmailVerificationToken();
    await agenda.now('send-email-verification', { userId: user.id, token });

    res.json({ message: 'Verification email scheduled' });
  } catch (error) {
    console.error('Failed to queue verification email', error);
    res.status(500).json({ message: 'Failed to queue verification email' });
  }
});

app.post('/users/verify-email', async (req, res) => {
  try {
    const { token } = req.body;
    if (!token) {
      return res.status(400).json({ message: 'Token is required' });
    }

    const user = await User.findOne({ where: { emailVerificationToken: token } });
    if (!user) {
      return res.status(400).json({ message: 'Invalid token' });
    }

    await user.markEmailAsVerified();
    res.json({ message: 'Email verified' });
  } catch (error) {
    console.error('Email verification failed', error);
    res.status(500).json({ message: 'Email verification failed' });
  }
});

app.get('/users/:id/donation-eligibility', authenticate, async (req, res) => {
  try {
    const userId = Number(req.params.id);
    if (req.user.sub !== userId && req.user.role !== 'admin') {
      return res.status(403).json({ message: 'Forbidden' });
    }
    const canDonate = await BloodDonation.canUserDonate(userId);
    const nextEligibleDate = await BloodDonation.getNextEligibleDate(userId);
    const remainingCooldown = await BloodDonation.getRemainingCooldownDays(userId);

    res.json({ canDonate, nextEligibleDate, remainingCooldown });
  } catch (error) {
    console.error('Failed to calculate donation eligibility', error);
    res.status(500).json({ message: 'Failed to calculate donation eligibility' });
  }
});

app.post('/blood-requests', authenticate, async (req, res) => {
  try {
    const payload = {
      userId: req.body.userId || req.user.sub,
      bloodType: req.body.bloodType,
      unitsNeeded: req.body.unitsNeeded,
      urgency: req.body.urgency || 'medium',
      reason: req.body.reason,
      hospital: req.body.hospital,
      contactPerson: req.body.contactPerson,
      contactNumber: req.body.contactNumber,
      requestDate: req.body.requestDate ? new Date(req.body.requestDate) : new Date(),
      status: 'pending'
    };

    const bloodRequest = await BloodRequest.create(payload);
    io.emit('blood-request:created', bloodRequest.toJSON());
    res.status(201).json(bloodRequest);
  } catch (error) {
    console.error('Failed to create blood request', error);
    res.status(500).json({ message: 'Failed to create blood request' });
  }
});

app.patch('/blood-requests/:id/status', authenticate, async (req, res) => {
  try {
    const bloodRequest = await BloodRequest.findByPk(req.params.id, {
      include: [{ model: User, as: 'user' }]
    });

    if (!bloodRequest) {
      return res.status(404).json({ message: 'Request not found' });
    }

    if (req.body.status) {
      bloodRequest.status = req.body.status;
    }
    if (typeof req.body.bloodAvailable === 'boolean') {
      bloodRequest.bloodAvailable = req.body.bloodAvailable;
    }
    if (typeof req.body.allocatedUnits === 'number') {
      bloodRequest.allocatedUnits = req.body.allocatedUnits;
    }
    if (req.body.adminNotes) {
      bloodRequest.adminNotes = req.body.adminNotes;
    }

    await bloodRequest.save();
    await sendRequestStatusUpdate(bloodRequest.id, bloodRequest.status, req.body.adminNotes);

    io.emit('blood-request:updated', bloodRequest.toJSON());
    res.json(bloodRequest);
  } catch (error) {
    console.error('Failed to update blood request', error);
    res.status(500).json({ message: 'Failed to update blood request' });
  }
});

app.post('/blood-donations', authenticate, async (req, res) => {
  try {
    const donation = await BloodDonation.create({
      userId: req.body.userId || req.user.sub,
      donorName: req.body.donorName,
      donorEmail: req.body.donorEmail,
      bloodType: req.body.bloodType,
      donationDate: req.body.donationDate ? new Date(req.body.donationDate) : new Date(),
      quantity: req.body.quantity || 1,
      screeningStatus: req.body.screeningStatus,
      screeningAnswers: req.body.screeningAnswers,
      notes: req.body.notes
    });

    io.emit('blood-donation:created', donation.toJSON());
    res.status(201).json(donation);
  } catch (error) {
    console.error('Failed to create blood donation', error);
    res.status(500).json({ message: 'Failed to create blood donation' });
  }
});

app.patch('/blood-donations/:id/status', authenticate, async (req, res) => {
  try {
    const donation = await BloodDonation.findByPk(req.params.id, {
      include: [{ model: User, as: 'user' }]
    });

    if (!donation) {
      return res.status(404).json({ message: 'Donation not found' });
    }

    if (req.body.status) {
      donation.status = req.body.status;
    }
    if (req.body.screeningStatus) {
      donation.screeningStatus = req.body.screeningStatus;
    }
    if (req.body.adminNotes) {
      donation.adminNotes = req.body.adminNotes;
    }

    await donation.save();
    await sendDonationStatusUpdate(donation.id, donation.status, req.body.adminNotes);

    io.emit('blood-donation:updated', donation.toJSON());
    res.json(donation);
  } catch (error) {
    console.error('Failed to update donation', error);
    res.status(500).json({ message: 'Failed to update donation' });
  }
});

app.post('/appointments', authenticate, async (req, res) => {
  try {
    const appointment = await Appointment.create({
      userId: req.body.userId || req.user.sub,
      appointmentType: req.body.appointmentType,
      bloodType: req.body.bloodType,
      appointmentDate: req.body.appointmentDate ? new Date(req.body.appointmentDate) : new Date(),
      timeSlot: req.body.timeSlot,
      notes: req.body.notes
    });

    io.emit('appointment:created', appointment.toJSON());
    res.status(201).json(appointment);
  } catch (error) {
    console.error('Failed to create appointment', error);
    res.status(500).json({ message: 'Failed to create appointment' });
  }
});

app.patch('/appointments/:id/status', authenticate, async (req, res) => {
  try {
    const appointment = await Appointment.findByPk(req.params.id, {
      include: [{ model: User, as: 'user' }]
    });

    if (!appointment) {
      return res.status(404).json({ message: 'Appointment not found' });
    }

    if (req.body.status) {
      appointment.status = req.body.status;
    }
    if (req.body.adminNotes) {
      appointment.adminNotes = req.body.adminNotes;
    }

    await appointment.save();

    if (appointment.status === 'confirmed') {
      await sendAppointmentConfirmation(appointment.id);
    } else if (appointment.status === 'cancelled') {
      await sendAppointmentRejection(appointment.id, req.body.adminNotes);
    } else {
      await sendAppointmentStatusUpdate(appointment.id, appointment.status, req.body.adminNotes);
    }

    io.emit('appointment:updated', appointment.toJSON());
    res.json(appointment);
  } catch (error) {
    console.error('Failed to update appointment', error);
    res.status(500).json({ message: 'Failed to update appointment' });
  }
});

io.on('connection', (socket) => {
  console.log('Socket connected', socket.id);
  socket.on('disconnect', () => console.log('Socket disconnected', socket.id));
});

async function bootstrap() {
  try {
    await sequelize.authenticate();
    await agenda.start();
    await agenda.every('1 hour', 'broadcast-blood-availability');
    await agenda.every('1 day', 'remind-upcoming-appointments');

    server.listen(PORT, () => {
      console.log(`BloodVault Node API listening on port ${PORT}`);
    });
  } catch (error) {
    console.error('Failed to start application', error);
    process.exit(1);
  }
}

bootstrap();

process.on('SIGINT', async () => {
  console.log('Shutting down gracefully...');
  await agenda.stop();
  server.close(() => process.exit(0));
});

export { app, server, io };
