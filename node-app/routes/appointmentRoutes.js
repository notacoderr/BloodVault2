import { Router } from 'express';
import { Op } from 'sequelize';
import {
  sendAppointmentConfirmation,
  sendAppointmentRejection,
  sendAppointmentStatusUpdate
} from '../services/notificationService.js';

export default function createAppointmentRoutes({ models, authHelpers, io }) {
  const router = Router();
  const { Appointment, User } = models;
  const { authenticate, scopedFilter, ensureOwnershipOrAdmin } = authHelpers;

  router.use(authenticate);

  router.get('/', async (req, res) => {
    try {
      const where = scopedFilter(req);
      if (req.query.status) {
        where.status = req.query.status;
      }

      const range = {};
      if (req.query.from) {
        const fromDate = new Date(req.query.from);
        if (!Number.isNaN(fromDate.getTime())) {
          range[Op.gte] = fromDate;
        }
      }
      if (req.query.to) {
        const toDate = new Date(req.query.to);
        if (!Number.isNaN(toDate.getTime())) {
          range[Op.lte] = toDate;
        }
      }
      if (Object.keys(range).length > 0) {
        where.appointmentDate = { ...(where.appointmentDate || {}), ...range };
      }

      const appointments = await Appointment.findAll({
        where,
        include: [
          { model: User, as: 'user', attributes: ['id', 'name', 'email', 'bloodtype'] }
        ],
        order: [['appointmentDate', 'DESC']]
      });

      res.json(appointments.map((appointment) => appointment.toJSON()));
    } catch (error) {
      console.error('Failed to fetch appointments', error);
      res.status(500).json({ message: 'Failed to fetch appointments' });
    }
  });

  router.get('/:id', async (req, res) => {
    try {
      const appointment = await Appointment.findByPk(req.params.id, {
        include: [
          { model: User, as: 'user', attributes: ['id', 'name', 'email', 'bloodtype'] }
        ]
      });

      if (!appointment) {
        return res.status(404).json({ message: 'Appointment not found' });
      }

      if (!ensureOwnershipOrAdmin(req, appointment.userId)) {
        return res.status(403).json({ message: 'Forbidden' });
      }

      res.json(appointment.toJSON());
    } catch (error) {
      console.error('Failed to fetch appointment', error);
      res.status(500).json({ message: 'Failed to fetch appointment' });
    }
  });

  router.post('/', async (req, res) => {
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

  router.patch('/:id/status', async (req, res) => {
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

  return router;
}
