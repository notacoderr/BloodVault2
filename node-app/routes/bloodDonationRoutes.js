import { Router } from 'express';
import { sendDonationStatusUpdate } from '../services/notificationService.js';

export default function createBloodDonationRoutes({ models, authHelpers, io }) {
  const router = Router();
  const { BloodDonation, User } = models;
  const { authenticate, scopedFilter, ensureOwnershipOrAdmin } = authHelpers;

  router.use(authenticate);

  router.get('/', async (req, res) => {
    try {
      const where = scopedFilter(req);
      if (req.query.status) {
        where.status = req.query.status;
      }

      const donations = await BloodDonation.findAll({
        where,
        include: [
          {
            model: User,
            as: 'user',
            attributes: ['id', 'name', 'email', 'bloodtype']
          }
        ],
        order: [['donationDate', 'DESC']]
      });

      res.json(donations.map((donation) => donation.toJSON()));
    } catch (error) {
      console.error('Failed to fetch blood donations', error);
      res.status(500).json({ message: 'Failed to fetch blood donations' });
    }
  });

  router.get('/:id', async (req, res) => {
    try {
      const donation = await BloodDonation.findByPk(req.params.id, {
        include: [
          { model: User, as: 'user', attributes: ['id', 'name', 'email', 'bloodtype'] }
        ]
      });

      if (!donation) {
        return res.status(404).json({ message: 'Donation not found' });
      }

      if (!ensureOwnershipOrAdmin(req, donation.userId)) {
        return res.status(403).json({ message: 'Forbidden' });
      }

      res.json(donation.toJSON());
    } catch (error) {
      console.error('Failed to fetch blood donation', error);
      res.status(500).json({ message: 'Failed to fetch blood donation' });
    }
  });

  router.post('/', async (req, res) => {
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

  router.patch('/:id/status', async (req, res) => {
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

  return router;
}
