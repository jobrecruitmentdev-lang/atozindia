<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Mail\WelcomeCollectorMail;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Exception;

class CustomerAuthController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showLoginForm(Request $request)
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
        }

        if ($request->query('redirect') === 'checkout') {
            session()->put('url.intended', route('checkout.index'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');
        $oldSessionId = Session::getId();

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();

            if ($user->status !== 'active') {
                Auth::guard('web')->logout();
                return back()->withErrors(['email' => 'Your account is suspended. Please contact customer support.']);
            }

            $request->session()->regenerate();

            // Merge guest cart into user account cart
            $this->cartService->mergeGuestCart($oldSessionId, $user);

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email address or password.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(Request $request)
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
        }

        if ($request->query('redirect') === 'checkout') {
            session()->put('url.intended', route('checkout.index'));
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Generate cryptographically secure 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store pending registration payload in session with 10-minute expiry
        session([
            'pending_registration' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
            ],
            'registration_otp' => $otp,
            'registration_otp_expires_at' => now()->addMinutes(10),
            'registration_otp_attempts' => 0,
            'registration_otp_last_sent' => now(),
        ]);

        // Send 6-digit OTP email
        try {
            Mail::to($data['email'])->send(new RegistrationOtpMail($otp, $data['name']));
        } catch (Exception $mailEx) {
            Log::warning("Registration OTP mail failed: " . $mailEx->getMessage());
        }

        return redirect()->route('register.otp')->with('success', 'A 6-digit authentication code has been dispatched to your email.');
    }

    public function showOtpForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
        }

        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->with('info', 'Please complete the registration form first.');
        }

        return view('auth.verify-registration-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if (!session()->has('pending_registration') || !session()->has('registration_otp')) {
            return redirect()->route('register')->with('error', 'Registration session expired. Please sign up again.');
        }

        $expiresAt = session('registration_otp_expires_at');
        if ($expiresAt && now()->isAfter($expiresAt)) {
            return back()->withErrors(['otp' => 'The authentication code has expired. Please request a new code.']);
        }

        $attempts = (int) session('registration_otp_attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['pending_registration', 'registration_otp', 'registration_otp_expires_at', 'registration_otp_attempts']);
            return redirect()->route('register')->withErrors(['email' => 'Maximum verification attempts exceeded. Please register again.']);
        }

        $expectedOtp = (string) session('registration_otp');
        $inputOtp = trim($request->otp);

        if ($inputOtp !== $expectedOtp) {
            session(['registration_otp_attempts' => $attempts + 1]);
            return back()->withErrors(['otp' => 'Invalid 6-digit authentication code. Please re-enter.']);
        }

        // OTP is valid! Create the verified user
        $pending = session('pending_registration');
        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'],
            'password' => $pending['password'],
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $oldSessionId = Session::getId();
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Clear registration session keys
        session()->forget([
            'pending_registration',
            'registration_otp',
            'registration_otp_expires_at',
            'registration_otp_attempts',
            'registration_otp_last_sent'
        ]);

        // Send welcome collector email
        try {
            Mail::to($user->email)->send(new WelcomeCollectorMail($user));
        } catch (Exception $mailEx) {
            Log::warning("Welcome email failed: " . $mailEx->getMessage());
        }

        // Merge guest cart
        $this->cartService->mergeGuestCart($oldSessionId, $user);

        return redirect()->intended(route('customer.dashboard'))->with('success', 'Email verified successfully! Welcome to ATOZ GADGETZ.');
    }

    public function resendOtp()
    {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register');
        }

        $lastSent = session('registration_otp_last_sent');
        if ($lastSent && now()->diffInSeconds($lastSent) < 30) {
            return back()->with('info', 'Please wait 30 seconds before requesting another code.');
        }

        $otp = (string) random_int(100000, 999999);
        $pending = session('pending_registration');

        session([
            'registration_otp' => $otp,
            'registration_otp_expires_at' => now()->addMinutes(10),
            'registration_otp_last_sent' => now(),
        ]);

        try {
            Mail::to($pending['email'])->send(new RegistrationOtpMail($otp, $pending['name']));
        } catch (Exception $mailEx) {
            Log::warning("Resend Registration OTP mail failed: " . $mailEx->getMessage());
        }

        return back()->with('success', 'A fresh 6-digit authentication code has been dispatched.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been safely signed out.');
    }
}
