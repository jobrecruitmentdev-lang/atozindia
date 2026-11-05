<?php

namespace App\Http\Controllers;

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

    public function process(Request $request)
    {
        $data = $request->validate([
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

            return redirect()->route('checkout.confirmation', $order->order_number);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['checkout_error' => $e->getMessage()]);
        }
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::with(['items', 'shippingAddress', 'billingAddress', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }

    public function invoice(string $orderNumber)
    {
        $order = Order::with(['items.product.categories', 'items.variant', 'shippingAddress', 'billingAddress', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.invoice', compact('order'));
    }
}
