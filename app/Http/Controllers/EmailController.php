<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodRequest;
use App\Models\BloodDonation;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Send blood request status update email.
     */
    public function sendRequestStatusUpdate($requestId, $newStatus, $adminNotes = '')
    {
        try {
            $bloodRequest = BloodRequest::with('user')->findOrFail($requestId);
            $user = $bloodRequest->user;
            
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
    public function sendDonationStatusUpdate($donationId, $newStatus, $notes = '')
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
    public function sendAppointmentConfirmation($appointmentId)
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
     * Send appointment cancellation email.
     */
    public function sendAppointmentCancellation($appointmentId, $reason = '')
    {
        try {
            $appointment = Appointment::with('user')->findOrFail($appointmentId);
            $user = $appointment->user;
            
            $subject = "Appointment Cancelled - {$appointment->appointment_type}";
            $data = [
                'user_name' => $user->name,
                'appointment_id' => $appointmentId,
                'appointment_type' => $appointment->appointment_type,
                'appointment_date' => $appointment->appointment_date,
                'time_slot' => $appointment->time_slot,
                'cancellation_reason' => $reason,
            ];
            
            Mail::send('emails.appointment-cancellation', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Appointment cancellation email sent to {$user->email} for appointment ID: {$appointmentId}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send appointment cancellation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send appointment rejection email.
     */
    public function sendAppointmentRejection($appointmentId, $adminNotes = '')
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
    public function sendAppointmentStatusUpdate($appointmentId, $newStatus, $adminNotes = '')
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

    /**
     * Send blood availability notification.
     */
    public function sendBloodAvailabilityNotification($bloodType, $availableUnits)
    {
        try {
            // Get users who have pending requests for this blood type
            $pendingRequests = BloodRequest::with('user')
                ->where('blood_type', $bloodType)
                ->where('status', 'pending')
                ->where('blood_available', false)
                ->get();
            
            foreach ($pendingRequests as $request) {
                $user = $request->user;
                
                $subject = "Blood Available - {$bloodType}";
                $data = [
                    'user_name' => $user->name,
                    'blood_type' => $bloodType,
                    'available_units' => $availableUnits,
                    'request_id' => $request->id,
                    'units_needed' => $request->units_needed,
                ];
                
                Mail::send('emails.blood-available', $data, function ($message) use ($user, $subject) {
                    $message->to($user->email, $user->name)
                            ->subject($subject);
                });
                
                // Update request to show blood is available
                $request->update(['blood_available' => true]);
            }
            
            Log::info("Blood availability notifications sent for blood type: {$bloodType}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send blood availability notifications: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send low stock alert to admins.
     */
    public function sendLowStockAlert($bloodType, $availableUnits)
    {
        try {
            $admins = User::where('usertype', 'admin')->get();
            
            $subject = "Low Stock Alert - {$bloodType}";
            $data = [
                'blood_type' => $bloodType,
                'available_units' => $availableUnits,
                'alert_level' => $availableUnits <= 2 ? 'CRITICAL' : 'LOW',
            ];
            
            foreach ($admins as $admin) {
                Mail::send('emails.low-stock-alert', $data, function ($message) use ($admin, $subject) {
                    $message->to($admin->email, $admin->name)
                            ->subject($subject);
                });
            }
            
            Log::info("Low stock alert sent to admins for blood type: {$bloodType}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send low stock alert: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send expiring blood notification.
     */
    public function sendExpiringBloodNotification($bloodType, $expiringUnits, $expirationDate)
    {
        try {
            $admins = User::where('usertype', 'admin')->get();
            
            $subject = "Blood Expiring Soon - {$bloodType}";
            $data = [
                'blood_type' => $bloodType,
                'expiring_units' => $expiringUnits,
                'expiration_date' => $expirationDate,
                'days_until_expiry' => now()->diffInDays($expirationDate),
            ];
            
            foreach ($admins as $admin) {
                Mail::send('emails.expiring-blood', $data, function ($message) use ($admin, $subject) {
                    $message->to($admin->email, $admin->name)
                            ->subject($subject);
                });
            }
            
            Log::info("Expiring blood notification sent to admins for blood type: {$bloodType}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send expiring blood notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email to new users.
     */
    public function sendWelcomeEmail($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $subject = "Welcome to Life Vault Blood Bank";
            $data = [
                'user_name' => $user->name,
                'email' => $user->email,
                'usertype' => $user->usertype,
            ];
            
            Mail::send('emails.welcome', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Welcome email sent to {$user->email}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordResetEmail($email, $resetToken)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return false;
            }
            
            $subject = "Password Reset Request - Life Vault";
            $data = [
                'user_name' => $user->name,
                'reset_token' => $resetToken,
                'reset_url' => url("/reset-password?token={$resetToken}&email={$email}"),
            ];
            
            Mail::send('emails.password-reset', $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });
            
            Log::info("Password reset email sent to {$user->email}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk email to users.
     */
    public function sendBulkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'user_group' => 'required|in:all,donors,requesters,admins',
            'blood_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $query = User::query();
            
            // Filter users based on group
            switch ($request->user_group) {
                case 'donors':
                    $query->where('usertype', 'donor');
                    break;
                case 'requesters':
                    $query->where('usertype', 'requester');
                    break;
                case 'admins':
                    $query->where('usertype', 'admin');
                    break;
            }
            
            // Filter by blood type if specified
            if ($request->blood_type) {
                $query->where('bloodtype', $request->blood_type);
            }
            
            $users = $query->get();
            $sentCount = 0;
            
            foreach ($users as $user) {
                $data = [
                    'user_name' => $user->name,
                    'message' => $request->message,
                ];
                
                Mail::send('emails.bulk-message', $data, function ($message) use ($user, $request) {
                    $message->to($user->email, $user->name)
                            ->subject($request->subject);
                });
                
                $sentCount++;
            }
            
            Log::info("Bulk email sent to {$sentCount} users");
            return back()->with('status', "Bulk email sent successfully to {$sentCount} users!");
            
        } catch (\Exception $e) {
            Log::error("Failed to send bulk email: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send bulk email. Please try again.']);
        }
    }

    /**
     * Test email functionality.
     */
    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $subject = "Test Email - Life Vault Blood Bank";
            $data = [
                'test_message' => 'This is a test email to verify the email system is working correctly.',
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ];
            
            Mail::send('emails.test', $data, function ($message) use ($request, $subject) {
                $message->to($request->test_email)
                        ->subject($subject);
            });
            
            Log::info("Test email sent to {$request->test_email}");
            return back()->with('status', 'Test email sent successfully!');
            
        } catch (\Exception $e) {
            Log::error("Failed to send test email: " . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }
}
