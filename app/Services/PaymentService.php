<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Initialize payment record for an order.
     */
    public function initiatePayment(Order $order, string $provider = 'cod', ?array $extraPayload = null): Payment
    {
        $ref = 'PAY-' . strtoupper(Str::random(12));

        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_reference' => $ref,
            'provider' => $provider,
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'status' => $provider === 'cod' ? 'authorized' : 'pending',
            'payload_json' => $extraPayload,
        ]);

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount' => $order->total_amount,
            'status' => $provider === 'cod' ? 'authorized' : 'pending',
            'gateway_reference' => $ref,
            'response_message' => $provider === 'cod' ? 'Cash on Delivery authorized for order dispatch' : 'Payment initialized',
            'created_at' => now(),
        ]);

        return $payment;
    }

    /**
     * Record payment capture.
     */
    public function recordCapture(Payment $payment, string $gatewayRef, ?array $payload = null): void
    {
        $payment->status = 'captured';
        $payment->payload_json = array_merge($payment->payload_json ?? [], $payload ?? []);
        $payment->save();

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount' => $payment->amount,
            'status' => 'captured',
            'gateway_reference' => $gatewayRef,
            'response_message' => 'Funds captured successfully',
            'created_at' => now(),
        ]);

        // Update order payment status
        $payment->order->payment_status = 'paid';
        $payment->order->save();
    }
}
