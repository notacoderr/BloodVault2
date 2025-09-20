<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\BloodDonation;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send blood request status update email.
     */
    public static function sendRequestStatusUpdate($requestId, $newStatus, $adminNotes = '')
    {
        try {
            $bloodRequest = BloodRequest::with('user')->findOrFail($requestId);
            $user = $bloodRequest->user;
            
            // Check if user exists and has email
            if (!$user || !$user->email) {
                Log::error("User or email not found for blood request ID: {$requestId}");
                return false;
            }
            
            $subject = "Blood Request Status Update - {$newStatus}";
            $data = [
                'user_name' => $user->name,
                'request_id' => $requestId,
                'blood_type' => $bloodRequest->blood_type,
                'units_needed' => $bloodRequest->units_needed,
                'old_status' => $bloodRequest->status,
                'new_status' => $newStatus,
                'admin_notes' => $adminNotes,
                'request_date' => $bloodRequest->request_date,
            ];
            
            Mail::send('emails.request-status-update', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Request status update email sent to {$user->email} for request ID: {$requestId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send request status update email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send blood donation status update email.
     */
    public static function sendDonationStatusUpdate($donationId, $newStatus, $notes = '')
    {
        try {
            $bloodDonation = BloodDonation::with('user')->findOrFail($donationId);
            $user = $bloodDonation->user;
            
            $subject = "Blood Donation Status Update - {$newStatus}";
            $data = [
                'user_name' => $user->name,
                'donation_id' => $donationId,
                'blood_type' => $bloodDonation->blood_type,
                'donation_date' => $bloodDonation->donation_date,
                'old_status' => $bloodDonation->status,
                'new_status' => $newStatus,
                'notes' => $notes,
            ];
            
            Mail::send('emails.donation-status-update', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Donation status update email sent to {$user->email} for donation ID: {$donationId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send donation status update email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send appointment confirmation email.
     */
    public static function sendAppointmentConfirmation($appointmentId)
    {
        try {
            $appointment = Appointment::with('user')->findOrFail($appointmentId);
            $user = $appointment->user;
            
            $subject = "Appointment Confirmed - {$appointment->appointment_type}";
            $data = [
                'user_name' => $user->name,
                'appointment_id' => $appointmentId,
                'appointment_type' => $appointment->appointment_type,
                'appointment_date' => $appointment->appointment_date,
                'time_slot' => $appointment->time_slot,
                'notes' => $appointment->notes,
            ];
            
            Mail::send('emails.appointment-confirmation', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Appointment confirmation email sent to {$user->email} for appointment ID: {$appointmentId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send appointment confirmation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send appointment rejection email.
     */
    public static function sendAppointmentRejection($appointmentId, $adminNotes = '')
    {
        try {
            $appointment = Appointment::with('user')->findOrFail($appointmentId);
            $user = $appointment->user;
            
            $subject = "Appointment Rejected";
            $data = [
                'user_name' => $user->name,
                'appointment_id' => $appointmentId,
                'appointment_type' => $appointment->appointment_type,
                'appointment_date' => $appointment->appointment_date,
                'time_slot' => $appointment->time_slot,
                'admin_notes' => $adminNotes,
            ];
            
            Mail::send('emails.appointment-rejected', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Appointment rejection email sent to {$user->email} for appointment ID: {$appointmentId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send appointment rejection email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send appointment status update email.
     */
    public static function sendAppointmentStatusUpdate($appointmentId, $newStatus, $adminNotes = '')
    {
        try {
            $appointment = Appointment::with('user')->findOrFail($appointmentId);
            $user = $appointment->user;
            
            $subject = "Appointment Status Update - " . ucfirst($newStatus);
            $data = [
                'user_name' => $user->name,
                'appointment_id' => $appointmentId,
                'appointment_type' => $appointment->appointment_type,
                'appointment_date' => $appointment->appointment_date,
                'time_slot' => $appointment->time_slot,
                'old_status' => $appointment->status,
                'new_status' => $newStatus,
                'admin_notes' => $adminNotes,
            ];
            
            Mail::send('emails.appointment-status-update', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Appointment status update email sent to {$user->email} for appointment ID: {$appointmentId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send appointment status update email: " . $e->getMessage());
            return false;
        }
    }
}
