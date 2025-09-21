import { Router } from 'express';
import { Op } from 'sequelize';

export default function createDashboardRoutes({ models, authHelpers }) {
  const router = Router();
  const { BloodRequest, BloodDonation, Appointment, BloodBank, User } = models;
  const { authenticate, isAdminRequest } = authHelpers;

  router.use(authenticate);

  router.get('/summary', async (req, res) => {
    try {
      let userIdFilter;
      if (isAdminRequest(req) && req.query.userId) {
        const requestedId = Number(req.query.userId);
        userIdFilter = !Number.isNaN(requestedId) ? { userId: requestedId } : {};
      } else {
        userIdFilter = isAdminRequest(req) ? {} : { userId: req.user.sub };
      }

      const requestWhere = { ...userIdFilter };
      const donationWhere = { ...userIdFilter };
      const appointmentWhere = { ...userIdFilter };
      const upcomingWhere = {
        ...appointmentWhere,
        appointmentDate: { [Op.gt]: new Date() }
      };

      const [
        totalRequests,
        pendingRequests,
        totalDonations,
        completedDonations,
        upcomingAppointmentsCount,
        availableUnits,
        recentRequests,
        recentDonations,
        upcomingAppointments
      ] = await Promise.all([
        BloodRequest.count({ where: requestWhere }),
        BloodRequest.count({ where: { ...requestWhere, status: 'pending' } }),
        BloodDonation.count({ where: donationWhere }),
        BloodDonation.count({ where: { ...donationWhere, status: 'completed' } }),
        Appointment.count({ where: upcomingWhere }),
        BloodBank.sum('quantity', { where: { status: 1 } }),
        BloodRequest.findAll({
          where: requestWhere,
          include: [
            { model: User, as: 'user', attributes: ['id', 'name', 'bloodtype'] }
          ],
          order: [['requestDate', 'DESC']],
          limit: 5
        }),
        BloodDonation.findAll({
          where: donationWhere,
          include: [
            { model: User, as: 'user', attributes: ['id', 'name', 'bloodtype'] }
          ],
          order: [['donationDate', 'DESC']],
          limit: 5
        }),
        Appointment.findAll({
          where: upcomingWhere,
          include: [
            { model: User, as: 'user', attributes: ['id', 'name', 'bloodtype'] }
          ],
          order: [['appointmentDate', 'ASC']],
          limit: 5
        })
      ]);

      res.json({
        totals: {
          requests: totalRequests,
          pendingRequests,
          donations: totalDonations,
          completedDonations,
          upcomingAppointments: upcomingAppointmentsCount,
          availableUnits: Number(availableUnits ?? 0)
        },
        recentRequests: recentRequests.map((request) => request.toJSON()),
        recentDonations: recentDonations.map((donation) => donation.toJSON()),
        upcomingAppointments: upcomingAppointments.map((appointment) => appointment.toJSON())
      });
    } catch (error) {
      console.error('Failed to build dashboard summary', error);
      res.status(500).json({ message: 'Failed to build dashboard summary' });
    }
  });

  return router;
}
