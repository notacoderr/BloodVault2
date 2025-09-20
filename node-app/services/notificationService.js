import { getModels } from '../models/index.js';
import { sendMail } from './emailService.js';

export async function sendRequestStatusUpdate(requestId, newStatus, adminNotes = '') {
  const { BloodRequest, User } = getModels();
  try {
    const bloodRequest = await BloodRequest.findByPk(requestId, {
      include: [{ model: User, as: 'user' }]
    });

    if (!bloodRequest || !bloodRequest.user || !bloodRequest.user.email) {
      console.error(`User or email not found for blood request ID: ${requestId}`);
      return false;
    }

    const subject = `Blood Request Status Update - ${newStatus}`;
    const message = `Hello ${bloodRequest.user.name || 'donor'},\n\n` +
      `Your blood request (${bloodRequest.bloodType}) for ${bloodRequest.unitsNeeded} unit(s) ` +
      `submitted on ${bloodRequest.requestDate?.toISOString?.() || bloodRequest.requestDate} ` +
      `has changed status from ${bloodRequest.status} to ${newStatus}.\n\n` +
      (adminNotes ? `Admin notes: ${adminNotes}\n\n` : '') +
      'Thank you for using BloodVault.';

    await sendMail({ to: bloodRequest.user.email, subject, text: message });
    console.info(`Request status update email sent to ${bloodRequest.user.email} for request ID: ${requestId}`);
    return true;
  } catch (error) {
    console.error('Failed to send request status update email:', error);
    return false;
  }
}

export async function sendDonationStatusUpdate(donationId, newStatus, notes = '') {
  const { BloodDonation, User } = getModels();
  try {
    const bloodDonation = await BloodDonation.findByPk(donationId, {
      include: [{ model: User, as: 'user' }]
    });

    if (!bloodDonation || !bloodDonation.user || !bloodDonation.user.email) {
      console.error(`User or email not found for blood donation ID: ${donationId}`);
      return false;
    }

    const subject = `Blood Donation Status Update - ${newStatus}`;
    const message = `Hello ${bloodDonation.user.name || 'donor'},\n\n` +
      `Your blood donation (${bloodDonation.bloodType}) scheduled on ` +
      `${bloodDonation.donationDate?.toISOString?.() || bloodDonation.donationDate} ` +
      `has changed status from ${bloodDonation.status} to ${newStatus}.\n\n` +
      (notes ? `Notes: ${notes}\n\n` : '') +
      'Thank you for supporting our community.';

    await sendMail({ to: bloodDonation.user.email, subject, text: message });
    console.info(`Donation status update email sent to ${bloodDonation.user.email} for donation ID: ${donationId}`);
    return true;
  } catch (error) {
    console.error('Failed to send donation status update email:', error);
    return false;
  }
}

export async function sendAppointmentConfirmation(appointmentId) {
  return sendAppointmentNotification(appointmentId, 'Appointment Confirmed', (appointment) =>
    `Hello ${appointment.user?.name || 'donor'},\n\n` +
    `Your appointment for ${appointment.appointmentType} on ` +
    `${appointment.appointmentDate?.toISOString?.() || appointment.appointmentDate}` +
    `${appointment.timeSlot ? ` at ${appointment.timeSlot}` : ''} has been confirmed.\n\n` +
    (appointment.notes ? `Notes: ${appointment.notes}\n\n` : '') +
    'Thank you!'
  );
}

export async function sendAppointmentRejection(appointmentId, adminNotes = '') {
  return sendAppointmentNotification(appointmentId, 'Appointment Rejected', (appointment) =>
    `Hello ${appointment.user?.name || 'donor'},\n\n` +
    `Unfortunately your appointment for ${appointment.appointmentType} on ` +
    `${appointment.appointmentDate?.toISOString?.() || appointment.appointmentDate}` +
    `${appointment.timeSlot ? ` at ${appointment.timeSlot}` : ''} was rejected.\n\n` +
    (adminNotes ? `Admin notes: ${adminNotes}\n\n` : '') +
    'Please reach out to reschedule.'
  );
}

export async function sendAppointmentStatusUpdate(appointmentId, newStatus, adminNotes = '') {
  return sendAppointmentNotification(appointmentId, `Appointment Status Update - ${newStatus}`, (appointment) =>
    `Hello ${appointment.user?.name || 'donor'},\n\n` +
    `Your appointment for ${appointment.appointmentType} on ` +
    `${appointment.appointmentDate?.toISOString?.() || appointment.appointmentDate}` +
    `${appointment.timeSlot ? ` at ${appointment.timeSlot}` : ''} has changed status ` +
    `from ${appointment.status} to ${newStatus}.\n\n` +
    (adminNotes ? `Admin notes: ${adminNotes}\n\n` : '') +
    'Thank you!'
  );
}

async function sendAppointmentNotification(appointmentId, subjectPrefix, renderer) {
  const { Appointment, User } = getModels();
  try {
    const appointment = await Appointment.findByPk(appointmentId, {
      include: [{ model: User, as: 'user' }]
    });

    if (!appointment || !appointment.user || !appointment.user.email) {
      console.error(`User or email not found for appointment ID: ${appointmentId}`);
      return false;
    }

    const subject = `${subjectPrefix}${appointment.appointmentType ? ` - ${appointment.appointmentType}` : ''}`;
    const message = renderer(appointment);

    await sendMail({ to: appointment.user.email, subject, text: message });
    console.info(`${subjectPrefix} email sent to ${appointment.user.email} for appointment ID: ${appointmentId}`);
    return true;
  } catch (error) {
    console.error(`Failed to send appointment email for ID ${appointmentId}:`, error);
    return false;
  }
}

export default {
  sendRequestStatusUpdate,
  sendDonationStatusUpdate,
  sendAppointmentConfirmation,
  sendAppointmentRejection,
  sendAppointmentStatusUpdate
};
