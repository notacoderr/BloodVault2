import { Router } from 'express';

export default function createUserRoutes({ models, authHelpers, scheduler }) {
  const router = Router();
  const { User, BloodDonation } = models;
  const { authenticate, ensureOwnershipOrAdmin } = authHelpers;

  router.post('/verify-email', async (req, res) => {
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

  router.use(authenticate);

  router.get('/me', async (req, res) => {
    try {
      const user = await User.findByPk(req.user.sub);
      if (!user) {
        return res.status(404).json({ message: 'User not found' });
      }

      res.json(user.toJSON());
    } catch (error) {
      console.error('Failed to fetch current user', error);
      res.status(500).json({ message: 'Failed to fetch current user' });
    }
  });

  router.post('/:id/request-email-verification', async (req, res) => {
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
      await scheduler.now('send-email-verification', { userId: user.id, token });

      res.json({ message: 'Verification email scheduled' });
    } catch (error) {
      console.error('Failed to queue verification email', error);
      res.status(500).json({ message: 'Failed to queue verification email' });
    }
  });

  router.get('/:id/donation-eligibility', async (req, res) => {
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

  router.get('/:id', async (req, res) => {
    try {
      const targetId = Number(req.params.id);
      if (!ensureOwnershipOrAdmin(req, targetId)) {
        return res.status(403).json({ message: 'Forbidden' });
      }

      const user = await User.findByPk(targetId);
      if (!user) {
        return res.status(404).json({ message: 'User not found' });
      }

      res.json(user.toJSON());
    } catch (error) {
      console.error('Failed to fetch user', error);
      res.status(500).json({ message: 'Failed to fetch user' });
    }
  });

  return router;
}
