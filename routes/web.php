<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\BloodDonationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. 
|
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/recover', [AuthController::class, 'showRecoverForm'])->name('recover');
Route::post('/recover', [AuthController::class, 'recoverAccount']);
Route::get('/reset-password', [AuthController::class, 'showPasswordResetForm'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Temporary debug route for session testing
Route::get('/debug-session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'recovery_email' => session('recovery_email'),
        'recovery_token' => session('recovery_token'),
        'all_session' => session()->all()
    ]);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification Routes
Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify-notice', [EmailVerificationController::class, 'showNotice'])->name('verification.notice.unauthenticated');
Route::get('/email/resend-verification', [EmailVerificationController::class, 'showResendForm'])->name('verification.resend.form');
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend');

// Protected User Routes
Route::middleware(['auth'])->group(function () {
    
    // User Dashboard & Profile
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
    
    // User Blood Requests
    Route::get('/user/my-requests', [UserController::class, 'myRequests'])->name('user.my-requests');
    Route::get('/user/request/{id}', [UserController::class, 'requestDetails'])->name('user.request-details');
    Route::post('/user/request/{id}/cancel', [UserController::class, 'cancelRequest'])->name('user.cancel-request');
    
    // User Blood Donations
    Route::get('/user/my-donations', [UserController::class, 'myDonations'])->name('user.my-donations');
    Route::get('/user/donation/{id}', [UserController::class, 'donationDetails'])->name('user.donation-details');
    
    // User Appointments
    Route::get('/user/my-appointments', [UserController::class, 'myAppointments'])->name('user.my-appointments');
Route::get('/user/dashboard/status-counts', [UserController::class, 'getDashboardStatusCounts'])->name('user.dashboard.status-counts');
    Route::get('/user/dashboard/donation-cooldown', [UserController::class, 'getDonationCooldown'])->name('user.dashboard.donation-cooldown');
    Route::get('/user/appointment/{id}', [UserController::class, 'appointmentDetails'])->name('user.appointment-details');
    Route::post('/user/appointment/{id}/cancel', [UserController::class, 'cancelAppointment'])->name('user.cancel-appointment');
    
    // Blood Request Management
    Route::get('/blood-request/create', [BloodRequestController::class, 'create'])->name('blood-request.create');
    Route::post('/blood-request', [BloodRequestController::class, 'store'])->name('blood-request.store');
    Route::get('/blood-request/{id}', [BloodRequestController::class, 'show'])->name('blood-request.show');
    Route::get('/blood-request/{id}/edit', [BloodRequestController::class, 'edit'])->name('blood-request.edit');
    Route::put('/blood-request/{id}', [BloodRequestController::class, 'update'])->name('blood-request.update');
    Route::post('/blood-request/{id}/cancel', [BloodRequestController::class, 'cancel'])->name('blood-request.cancel');
    Route::get('/blood-request/check-availability', [BloodRequestController::class, 'checkAvailability'])->name('blood-request.check-availability');
    Route::get('/blood-request/stats', [BloodRequestController::class, 'getStats'])->name('blood-request.stats');
    
    // Blood Donation Management
    Route::get('/blood-donation/create', [BloodDonationController::class, 'create'])->name('blood-donation.create');
    Route::post('/blood-donation', [BloodDonationController::class, 'store'])->name('blood-donation.store');
    Route::get('/blood-donation/{id}', [BloodDonationController::class, 'show'])->name('blood-donation.show');
    Route::get('/blood-donation/{id}/edit', [BloodDonationController::class, 'edit'])->name('blood-donation.edit');
    Route::put('/blood-donation/{id}', [BloodDonationController::class, 'update'])->name('blood-donation.update');
    Route::post('/blood-donation/{id}/cancel', [BloodDonationController::class, 'cancel'])->name('blood-donation.cancel');
    Route::get('/blood-donation/screening', [BloodDonationController::class, 'showScreening'])->name('blood-donation.screening');
    Route::post('/blood-donation/screening', [BloodDonationController::class, 'submitScreening'])->name('blood-donation.screening.submit');
    Route::get('/blood-donation/stats', [BloodDonationController::class, 'getStats'])->name('blood-donation.stats');
    
    // Appointment Management
    Route::get('/appointment/create', [AppointmentController::class, 'create'])->name('appointment.create');
    Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/{id}', [AppointmentController::class, 'show'])->name('appointment.show');
    Route::get('/appointment/{id}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');
    Route::put('/appointment/{id}', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::post('/appointment/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
    Route::post('/appointment/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointment.reschedule');
    Route::get('/appointment/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointment.available-slots');
    Route::get('/appointment/stats', [AppointmentController::class, 'getStats'])->name('appointment.stats');
    Route::get('/appointment/calendar', [AppointmentController::class, 'calendar'])->name('appointment.calendar');
    Route::get('/appointment/month/{year}/{month}', [AppointmentController::class, 'getMonthAppointments'])->name('appointment.month');
    
    // AJAX Routes for Real-time Updates
    Route::get('/user/dashboard-stats', [UserController::class, 'getDashboardStats'])->name('user.dashboard-stats');
});

// Protected Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/stats', [AdminController::class, 'getDashboardStats'])->name('admin.stats');
    
    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/stats', [AdminController::class, 'getUserStats'])->name('admin.users.stats');
    Route::get('/admin/user/{user}', [AdminController::class, 'userDetails'])->name('admin.user-details');
    Route::get('/admin/user/{user}/edit', [AdminController::class, 'editUser'])->name('admin.user.edit');
    Route::put('/admin/user/{user}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::post('/admin/user/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.user-status');
    Route::delete('/admin/user/{user}', [AdminController::class, 'deleteUser'])->name('admin.user-delete');
    Route::get('/admin/users/export', [AdminController::class, 'exportUsers'])->name('admin.users.export');
    
    // Blood Request Management
    Route::get('/admin/blood-requests', [AdminController::class, 'bloodRequests'])->name('admin.blood-requests');
    Route::get('/admin/blood-request/{id}', [AdminController::class, 'bloodRequestDetails'])->name('admin.blood-request-details');
    Route::get('/admin/blood-request/{id}/edit', [AdminController::class, 'editBloodRequest'])->name('admin.blood-requests.edit');
    Route::put('/admin/blood-request/{id}', [AdminController::class, 'updateBloodRequest'])->name('admin.blood-requests.update');
    Route::post('/admin/blood-request/{id}/status', [AdminController::class, 'updateRequestStatus'])->name('admin.blood-request-status');
    Route::get('/admin/blood-request/{id}/completion-eligibility', [AdminController::class, 'checkRequestCompletionEligibility'])->name('admin.blood-request.completion-eligibility');
    Route::get('/admin/blood-requests/export', [AdminController::class, 'exportRequests'])->name('admin.blood-requests.export');
Route::get('/admin/test-inventory-reduction/{id}', [AdminController::class, 'testInventoryReduction'])->name('admin.test-inventory-reduction');
Route::get('/admin/test-simple/{id}', [AdminController::class, 'testSimple'])->name('admin.test-simple');
Route::get('/admin/blood-requests/status-counts', [AdminController::class, 'getBloodRequestStatusCounts'])->name('admin.blood-requests.status-counts');
Route::get('/admin/blood-donations/status-counts', [AdminController::class, 'getBloodDonationStatusCounts'])->name('admin.blood-donations.status-counts');
    
    // Blood Donation Management
    Route::get('/admin/blood-donations', [AdminController::class, 'bloodDonations'])->name('admin.blood-donations');
    Route::get('/admin/blood-donation/{id}', [AdminController::class, 'bloodDonationDetails'])->name('admin.blood-donation-details');
    Route::post('/admin/blood-donation/{id}/status', [AdminController::class, 'updateDonationStatus'])->name('admin.blood-donation-status');
    Route::get('/admin/blood-donations/export', [AdminController::class, 'exportDonations'])->name('admin.blood-donations.export');
    
    // Appointment Management
    Route::get('/admin/appointment/{id}', [AdminController::class, 'appointmentDetails'])->name('admin.appointment-details');
    Route::get('/admin/appointment/{id}/edit', [AdminController::class, 'editAppointment'])->name('admin.appointments.edit');
    Route::put('/admin/appointment/{id}', [AdminController::class, 'updateAppointment'])->name('admin.appointments.update');
    Route::post('/admin/appointment/{id}/status', [AdminController::class, 'updateAppointmentStatus'])->name('admin.appointment-status');
    Route::post('/admin/appointment/{id}/reschedule', [AdminController::class, 'rescheduleAppointment'])->name('admin.appointment.reschedule');
    Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
    Route::get('/admin/appointments/export', [AdminController::class, 'exportAppointments'])->name('admin.appointments.export');
    Route::get('/admin/appointments/stats', [AdminController::class, 'getAppointmentStats'])->name('admin.appointments.stats');
    

    
    // Blood Inventory Management
    Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
Route::get('/admin/inventory/list', [InventoryController::class, 'list'])->name('admin.inventory.list');
    Route::get('/admin/inventory/stats', [InventoryController::class, 'getStats'])->name('admin.inventory.stats');
    Route::get('/admin/inventory/expiring', [InventoryController::class, 'expiring'])->name('admin.inventory.expiring');
    Route::get('/admin/inventory/expired', [InventoryController::class, 'expired'])->name('admin.inventory.expired');
    Route::get('/admin/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('admin.inventory.low-stock');
    Route::get('/admin/inventory/type/{bloodType}', [InventoryController::class, 'byBloodType'])->name('admin.inventory.by-type');
    Route::get('/admin/inventory/{id}', [InventoryController::class, 'show'])->name('admin.inventory.show');
    Route::post('/admin/inventory/{id}/status', [InventoryController::class, 'updateStatus'])->name('admin.inventory.status');
    Route::post('/admin/inventory/remove-expired', [InventoryController::class, 'removeExpired'])->name('admin.inventory.remove-expired');
    Route::delete('/admin/inventory/{id}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
    Route::post('/admin/inventory/report', [InventoryController::class, 'generateReport'])->name('admin.inventory.report');
    Route::get('/admin/inventory/export', [InventoryController::class, 'export'])->name('admin.inventory.export');
    
    // Email Management
    Route::get('/admin/email/test', [EmailController::class, 'testEmail'])->name('admin.email.test');
    Route::post('/admin/email/test', [EmailController::class, 'testEmail']);
    Route::get('/admin/email/bulk', [EmailController::class, 'sendBulkEmail'])->name('admin.email.bulk');
    Route::post('/admin/email/bulk', [EmailController::class, 'sendBulkEmail']);
    
    // Blood Donation Processing
    Route::post('/admin/blood-donation/{id}/process', [BloodDonationController::class, 'processDonation'])->name('admin.blood-donation.process');
});

// Fallback Route
Route::fallback(function () {
    return view('errors.404');
});
