<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailVerificationMail;
use App\Mail\WelcomeMail;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show()
    {
        // If user is authenticated but not verified, show verification notice
        if (Auth::check() && !Auth::user()->isEmailVerified()) {
            return view('auth.verify');
        }
        
        // If user is not authenticated, redirect to login
        return redirect()->route('login')->with('error', 'Please log in to verify your email.');
    }

    /**
     * Show the email verification notice for unauthenticated users.
     */
    public function showNotice()
    {
        return view('auth.verify-notice');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid verification token.');
        }

        if ($user->isEmailVerified()) {
            return redirect()->route('login')->with('info', 'Email already verified. You can now login.');
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        // Send welcome email
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // Log error but don't fail verification
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('status', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if ($user->isEmailVerified()) {
            return back()->with('info', 'Email already verified.');
        }

        // Generate new verification token
        $token = $user->generateEmailVerificationToken();

        // Send verification email
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
            return back()->with('status', 'Verification email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }

    /**
     * Show resend verification form for unauthenticated users.
     */
    public function showResendForm()
    {
        return view('auth.resend-verification');
    }

    /**
     * Send verification email after registration.
     */
    public static function sendVerificationEmail(User $user)
    {
        $token = $user->generateEmailVerificationToken();

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
            return false;
        }
    }
}
