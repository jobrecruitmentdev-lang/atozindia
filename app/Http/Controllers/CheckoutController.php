<?php

namespace App\Http\Controllers;

use App\Mail\OrderAuthorizationOtpMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(CartService $cartService, OrderService $orderService, PaymentService $paymentService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display the checkout terminal.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('info', 'Your shopping bag is empty.');
        }

        $totals = $this->cartService->getCartTotals($cart);
        $savedAddresses = $user ? $user->addresses : collect();

        return view('shop.checkout', compact('cart', 'totals', 'savedAddresses'));
    }

    /**
     * Validate checkout input and dispatch 6-digit Order Authorization OTP.
     */
    public function sendOrderOtp(Request $request)
    {
        $data = $this->validateCheckoutData($request);

        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);

        if ($cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your shopping bag is empty.'
            ], 422);
        }

        // Validate real-time stock
        foreach ($cart->items as $item) {
            if ($item->variant) {
                $stock = $item->variant->stock_quantity;
                if (!$item->variant->is_in_stock && $stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Item '{$item->product->name}' ({$item->variant->sku}) is out of stock."
                    ], 422);
                }
            }
        }

        $totals = $this->cartService->getCartTotals($cart);
        $shippingSummary = trim(($data['shipping']['city'] ?? '') . ', ' . ($data['shipping']['state'] ?? ''));

        // Generate cryptographically secure 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store checkout state in session
        session([
            'pending_order_data' => $data,
            'order_otp' => $otp,
            'order_otp_expires_at' => now()->addMinutes(10),
            'order_otp_attempts' => 0,
            'order_otp_last_sent' => now(),
            'order_otp_verified' => false,
        ]);

        // Send luxury 6-digit authorization email
        try {
            Mail::to($user->email)->send(new OrderAuthorizationOtpMail(
                $otp,
                $user->name,
                $totals['total'],
                $totals['items_count'],
                $shippingSummary
            ));
        } catch (Exception $mailEx) {
            Log::warning("Order authorization OTP mail delivery failed: " . $mailEx->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'A 6-digit authorization code has been dispatched to ' . $user->email,
            'email' => $user->email,
        ]);
    }

    /**
     * Verify the 6-digit Order Authorization OTP and finalize the order.
     */
    public function verifyOrderOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!session()->has('pending_order_data') || !session()->has('order_otp')) {
            return response()->json([
                'success' => false,
                'message' => 'Order authorization session expired. Please re-enter your details.'
            ], 422);
        }

        $expiresAt = session('order_otp_expires_at');
        if ($expiresAt && now()->isAfter($expiresAt)) {
            return response()->json([
                'success' => false,
                'message' => 'The authorization code has expired. Please request a fresh code.'
            ], 422);
        }

        $attempts = (int) session('order_otp_attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['pending_order_data', 'order_otp', 'order_otp_expires_at', 'order_otp_attempts', 'order_otp_last_sent', 'order_otp_verified']);
            return response()->json([
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please restart checkout.'
            ], 422);
        }

        $expectedOtp = (string) session('order_otp');
        $inputOtp = trim($request->otp);

        if ($inputOtp !== $expectedOtp) {
            session(['order_otp_attempts' => $attempts + 1]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid 6-digit code. Please verify the code received in your email.'
            ], 422);
        }

        // OTP verified! Finalize the order
        try {
            $user = auth()->user();
            $data = session('pending_order_data');

            $order = $this->orderService->createOrder($data, $user);

            // Record initial payment record via Payment abstraction
            $this->paymentService->initiatePayment($order, $order->payment_method);

            // Send luxury order confirmation email
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            } catch (Exception $mailEx) {
                Log::warning("Order confirmation email failed: " . $mailEx->getMessage());
            }

            // Clear session state
            session()->forget([
                'pending_order_data',
                'order_otp',
                'order_otp_expires_at',
                'order_otp_attempts',
                'order_otp_last_sent',
                'order_otp_verified'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Acquisition authorized successfully.',
                'redirect_url' => route('checkout.confirmation', $order->order_number),
            ]);
        } catch (Exception $e) {
            Log::error("Order finalization error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend 6-digit Order Authorization OTP.
     */
    public function resendOrderOtp(Request $request)
    {
        if (!session()->has('pending_order_data')) {
            return response()->json([
                'success' => false,
                'message' => 'No pending order session found.'
            ], 422);
        }

        $lastSent = session('order_otp_last_sent');
        if ($lastSent && now()->diffInSeconds($lastSent) < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 30 seconds before requesting another code.'
            ], 429);
        }

        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);
        $totals = $this->cartService->getCartTotals($cart);
        $pendingData = session('pending_order_data');
        $shippingSummary = trim(($pendingData['shipping']['city'] ?? '') . ', ' . ($pendingData['shipping']['state'] ?? ''));

        $otp = (string) random_int(100000, 999999);

        session([
            'order_otp' => $otp,
            'order_otp_expires_at' => now()->addMinutes(10),
            'order_otp_last_sent' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new OrderAuthorizationOtpMail(
                $otp,
                $user->name,
                $totals['total'],
                $totals['items_count'],
                $shippingSummary
            ));
        } catch (Exception $mailEx) {
            Log::warning("Resend order OTP mail error: " . $mailEx->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'A fresh 6-digit authorization code has been dispatched.'
        ]);
    }

    /**
     * Fallback standard form submission (requires valid OTP).
     */
    public function process(Request $request)
    {
        $data = $this->validateCheckoutData($request);

        // Security check: Must supply valid OTP or already verified
        $expectedOtp = (string) session('order_otp');
        $inputOtp = $request->input('order_otp');

        if (!session('order_otp_verified') && (!$inputOtp || trim($inputOtp) !== $expectedOtp)) {
            return back()->withInput()->withErrors([
                'checkout_error' => 'Security verification failed: A valid 6-digit authorization code sent to your email is mandatory.'
            ]);
        }

        try {
            $user = auth()->user();
            $order = $this->orderService->createOrder($data, $user);

            // Record initial payment record via Payment abstraction
            $this->paymentService->initiatePayment($order, $order->payment_method);

            // Send luxury order confirmation email
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            } catch (Exception $mailEx) {
                Log::warning("Order confirmation email failed: " . $mailEx->getMessage());
            }

            session()->forget([
                'pending_order_data',
                'order_otp',
                'order_otp_expires_at',
                'order_otp_attempts',
                'order_otp_last_sent',
                'order_otp_verified'
            ]);

            return redirect()->route('checkout.confirmation', $order->order_number);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['checkout_error' => $e->getMessage()]);
        }
    }

    /**
     * Show order confirmation screen.
     */
    public function confirmation(string $orderNumber)
    {
        $order = Order::with(['items', 'shippingAddress', 'billingAddress', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }

    /**
     * Show GST compliant tax invoice.
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::with(['items.product.categories', 'items.variant', 'shippingAddress', 'billingAddress', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.invoice', compact('order'));
    }

    /**
     * Common validation rules for checkout.
     */
    protected function validateCheckoutData(Request $request): array
    {
        return $request->validate([
            'email' => 'required|email|max:191',
            'phone' => 'required|string|max:32',
            'payment_method' => 'required|in:cod,wire',
            'same_as_shipping' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',

            // Shipping Address
            'shipping.first_name' => 'required|string|max:100',
            'shipping.last_name' => 'nullable|string|max:100',
            'shipping.company' => 'nullable|string|max:150',
            'shipping.address_line_1' => 'required|string|max:255',
            'shipping.address_line_2' => 'nullable|string|max:255',
            'shipping.landmark' => 'nullable|string|max:150',
            'shipping.city' => 'required|string|max:100',
            'shipping.state' => 'required|string|max:100',
            'shipping.postal_code' => 'required|string|max:20',
            'shipping.country' => 'nullable|string|max:10',

            // Billing Address (if different)
            'billing.first_name' => 'nullable|string|max:100',
            'billing.last_name' => 'nullable|string|max:100',
            'billing.address_line_1' => 'nullable|string|max:255',
            'billing.city' => 'nullable|string|max:100',
            'billing.state' => 'nullable|string|max:100',
            'billing.postal_code' => 'nullable|string|max:20',
        ]);
    }
}
