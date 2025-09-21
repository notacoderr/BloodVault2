import { Router } from 'express';

export default function createInventoryRoutes({ models, authHelpers }) {
  const router = Router();
  const { BloodBank, User } = models;
  const { authenticate, isAdminRequest } = authHelpers;

  router.use(authenticate);

  router.get('/', async (req, res) => {
    try {
      let queryBuilder = BloodBank;
      if (req.query.scope) {
        const scopes = req.query.scope
          .split(',')
          .map((scope) => scope.trim())
          .filter(Boolean);
        if (scopes.length > 0) {
          queryBuilder = BloodBank.scope(scopes);
        }
      }

      const where = {};
      if (!isAdminRequest(req)) {
        where.status = 1;
      }
      if (req.query.bloodType) {
        where.bloodType = req.query.bloodType;
      }

      const inventory = await queryBuilder.findAll({
        where,
        include: [
          { model: User, as: 'donor', attributes: ['id', 'name', 'bloodtype'] }
        ],
        order: [['expirationDate', 'ASC']]
      });

      res.json(inventory.map((item) => item.toJSON()));
    } catch (error) {
      console.error('Failed to fetch inventory', error);
      res.status(500).json({ message: 'Failed to fetch inventory' });
    }
  });

  return router;
}
