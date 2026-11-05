<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
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

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('customer.dashboard');
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

        $oldSessionId = Session::getId();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Send luxury welcome email
        try {
            Mail::to($user->email)->send(new WelcomeCollectorMail($user));
        } catch (Exception $mailEx) {
            Log::warning("Welcome email failed: " . $mailEx->getMessage());
        }

        // Merge guest cart
        $this->cartService->mergeGuestCart($oldSessionId, $user);

        return redirect()->route('customer.dashboard')->with('success', 'Welcome to ATOZ GADGETZ! Your account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been safely signed out.');
    }
}
