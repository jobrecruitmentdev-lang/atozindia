<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // High Security: Anti-Brute Force Throttling (Max 5 attempts, 15-min lockout)
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AuditLog::log('brute_force_blocked', 'Admin', null, null, [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'lockout_seconds' => $seconds,
            ]);

            throw ValidationException::withMessages([
                'email' => "Security Lockout: Too many failed authentication attempts. Access from this IP is temporarily suspended for {$seconds} seconds.",
            ]);
        }

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();
            if ($admin->status !== 'active') {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Your administrator account has been deactivated.']);
            }

            $admin->last_login_at = now();
            $admin->last_login_ip = $request->ip();
            $admin->save();

            AuditLog::log('login', 'Admin', $admin->id);

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($throttleKey, 900);
        AuditLog::log('failed_login_attempt', 'Admin', null, null, [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'attempts' => RateLimiter::attempts($throttleKey),
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            AuditLog::log('logout', 'Admin', $admin->id);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been securely signed out.');
    }
}
