import { Router } from 'express';
import { sendRequestStatusUpdate } from '../services/notificationService.js';

export default function createBloodRequestRoutes({ models, authHelpers, io }) {
  const router = Router();
  const { BloodRequest, User } = models;
  const { authenticate, scopedFilter, ensureOwnershipOrAdmin, isAdminRequest } = authHelpers;

  router.use(authenticate);

  router.get('/', async (req, res) => {
    try {
      const where = scopedFilter(req);
      if (isAdminRequest(req) && req.query.userId) {
        const requestedId = Number(req.query.userId);
        if (!Number.isNaN(requestedId)) {
          where.userId = requestedId;
        }
      }
      if (req.query.status) {
        where.status = req.query.status;
      }
      if (req.query.urgency) {
        where.urgency = req.query.urgency;
      }

      const requests = await BloodRequest.findAll({
        where,
        include: [
          {
            model: User,
            as: 'user',
            attributes: ['id', 'name', 'email', 'bloodtype', 'contact', 'city']
          }
        ],
        order: [['requestDate', 'DESC']]
      });

      res.json(requests.map((request) => request.toJSON()));
    } catch (error) {
      console.error('Failed to fetch blood requests', error);
      res.status(500).json({ message: 'Failed to fetch blood requests' });
    }
  });

  router.get('/:id', async (req, res) => {
    try {
      const bloodRequest = await BloodRequest.findByPk(req.params.id, {
        include: [
          {
            model: User,
            as: 'user',
            attributes: ['id', 'name', 'email', 'bloodtype', 'contact', 'city']
          }
        ]
      });

      if (!bloodRequest) {
        return res.status(404).json({ message: 'Request not found' });
      }

      if (!ensureOwnershipOrAdmin(req, bloodRequest.userId)) {
        return res.status(403).json({ message: 'Forbidden' });
      }

      res.json(bloodRequest.toJSON());
    } catch (error) {
      console.error('Failed to fetch blood request', error);
      res.status(500).json({ message: 'Failed to fetch blood request' });
    }
  });

  router.post('/', async (req, res) => {
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

  router.patch('/:id/status', async (req, res) => {
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

  return router;
}
