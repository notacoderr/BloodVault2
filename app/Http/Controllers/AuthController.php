<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        
        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->withInput();
        }

        // Check password (assuming passwords are stored as plain text in original system)
        if ($user->password !== $credentials['password']) {
            return back()->withErrors(['password' => 'These credentials do not match our records.'])->withInput();
        }

        // Check if email is verified
        if (!$user->isEmailVerified()) {
            return back()->withErrors(['email' => 'Please verify your email address before logging in. Check your email for the verification link.'])->withInput();
        }

        // Log in the user
        Auth::login($user);
        
        // Store user info in session
        session(['user' => $user->toArray()]);
        session(['role' => $user->usertype]);

        return $this->redirectBasedOnRole();
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:30|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'dob' => 'nullable|string|max:10',
            'sex' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'contact' => 'nullable|string|max:11',
            'bloodtype' => 'nullable|string|max:4',
            'usertype' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Store as plain text for compatibility
                'dob' => $request->dob,
                'sex' => $request->sex,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'contact' => $request->contact,
                'bloodtype' => $request->bloodtype,
                'usertype' => $request->usertype,
            ]);

            // Send email verification
            try {
                \App\Http\Controllers\EmailVerificationController::sendVerificationEmail($user);
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
            }

            // DO NOT log in the user after registration
            // User must verify email before accessing the system
            
            // Redirect to verification notice for unauthenticated users
            return redirect()->route('verification.notice.unauthenticated');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Show password reset form.
     */
    public function showPasswordResetForm()
    {
        // Get email from session or flash data
        $email = session('recovery_email') ?? old('email');
        
        return view('auth.reset-password', compact('email'));
    }

    /**
     * Handle password reset request.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.'])->withInput();
        }

        // Check if user is verified
        if (!$user->isEmailVerified()) {
            return back()->withErrors(['email' => 'Please verify your email address first before resetting your password.'])->withInput();
        }

        // Verify this is the same email from the recovery process
        $recoveryEmail = session('recovery_email');
        
        // For debugging - log the session values
        \Log::info('Password reset attempt', [
            'requested_email' => $request->email,
            'session_recovery_email' => $recoveryEmail,
            'session_id' => session()->getId(),
            'all_session_data' => session()->all()
        ]);
        
        // If there's a recovery session, verify the email matches
        if ($recoveryEmail && $recoveryEmail !== $request->email) {
            \Log::warning('Password reset email mismatch', [
                'requested_email' => $request->email,
                'session_email' => $recoveryEmail
            ]);
            return back()->withErrors(['email' => 'Email mismatch with recovery session. Please start the recovery process again.'])->withInput();
        }
        
        // Log the reset attempt for security
        \Log::info('Password reset completed', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'had_recovery_session' => !empty($recoveryEmail)
        ]);

        // Update the password (in production, you'd hash this)
        $user->update(['password' => $request->password]);

        // Clear the recovery session
        session()->forget(['recovery_token', 'recovery_email']);

        return redirect('/login')->with('status', 'Password reset successfully! You can now login with your new password.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard');
            } else {
                return redirect('/user/dashboard');
            }
        }
        
        return redirect('/');
    }

    /**
     * Show the recover account form.
     */
    public function showRecoverForm()
    {
        return view('auth.recover');
    }

    /**
     * Handle account recovery request.
     */
    public function recoverAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.'])->withInput();
        }

        // Check if user is verified
        if (!$user->isEmailVerified()) {
            return back()->withErrors(['email' => 'Please verify your email address first before recovering your password.'])->withInput();
        }

        // Generate recovery token
        $token = Str::random(64);
        
        // Store token in session for now (in production, you'd use email verification)
        session(['recovery_token' => $token, 'recovery_email' => $request->email]);
        
        // Force session save
        session()->save();
        
        // For debugging - log the session values
        \Log::info('Account recovery initiated', [
            'email' => $request->email,
            'token' => $token,
            'session_recovery_email' => session('recovery_email'),
            'session_id' => session()->getId()
        ]);

        return redirect('/reset-password')
            ->with('status', 'Account recovery initiated successfully! You can now set a new password.')
            ->with('recovery_email', $request->email);
    }

}
