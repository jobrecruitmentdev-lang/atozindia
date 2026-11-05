<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquisition Confirmed — ATOZ GADGETZ</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0c0d0e; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #e5e7eb; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0c0d0e; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%; background-color: #141517; border: 1px solid #27272a; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 44px 40px 32px 40px; border-bottom: 1px solid #222326; background: radial-gradient(circle at 50% 0%, #1e2024, #141517);">
                            <div style="font-size: 20px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; color: #ffffff;">
                                ATOZ GADGETZ
                            </div>
                            <div style="font-size: 10px; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; color: #a1a1aa; margin-top: 6px;">
                                Atelier of Precision Technology
                            </div>
                        </td>
                    </tr>

                    <!-- Order Confirmation Hero -->
                    <tr>
                        <td style="padding: 36px 40px 24px 40px; text-align: center;">
                            <div style="display: inline-block; padding: 6px 14px; background-color: #1c2b21; border: 1px solid #234c31; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #4ade80; margin-bottom: 16px;">
                                &#10003; Reservation Confirmed
                            </div>
                            <h1 style="margin: 0 0 10px 0; font-size: 26px; font-weight: 800; letter-spacing: -0.02em; color: #ffffff;">
                                Thank You For Your Acquisition
                            </h1>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #9ca3af;">
                                We have recorded your acquisition under Order <strong style="color: #ffffff; font-family: monospace;">#{{ $order->order_number }}</strong>. Our atelier technicians have commenced the white-glove inspection protocol prior to packaging.
                            </p>
                        </td>
                    </tr>

                    <!-- Order Summary Box -->
                    <tr>
                        <td style="padding: 0 40px 24px 40px;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #1a1b1e; border: 1px solid #27272a; border-radius: 12px; padding: 16px;">
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Order Date</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 12px; font-weight: 600; color: #ffffff;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Payment Method</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 12px; font-weight: 600; color: #ffffff; text-transform: uppercase;">{{ $order->payment_method }} ({{ $order->payment_status }})</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Fulfillment Status</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 12px; font-weight: 600; color: #4ade80; text-transform: uppercase;">{{ $order->fulfillment_status }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Line Items -->
                    <tr>
                        <td style="padding: 0 40px 28px 40px;">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #a1a1aa; margin-bottom: 14px; border-bottom: 1px solid #27272a; padding-bottom: 8px;">
                                Reserved Instruments ({{ $order->items->count() }})
                            </div>
                            
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #222326;">
                                            <div style="font-size: 13px; font-weight: 700; color: #ffffff;">{{ $item->product_name }}</div>
                                            <div style="font-size: 11px; color: #71717a; margin-top: 2px; font-family: monospace;">
                                                SKU: {{ $item->sku }} @if($item->variant_title) &bull; {{ $item->variant_title }} @endif
                                            </div>
                                            <div style="font-size: 11px; color: #a1a1aa; margin-top: 4px;">
                                                Qty: {{ $item->quantity }} &times; ₹{{ number_format($item->unit_price, 2) }}
                                            </div>
                                        </td>
                                        <td align="right" style="padding: 12px 0; border-bottom: 1px solid #222326; vertical-align: middle; font-family: monospace; font-size: 13px; font-weight: 700; color: #ffffff;">
                                            ₹{{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <!-- Financial Summary -->
                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #9ca3af;">Subtotal</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #ffffff; font-family: monospace;">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_total > 0)
                                    <tr>
                                        <td style="padding: 4px 0; font-size: 12px; color: #f87171;">Discount Applied</td>
                                        <td align="right" style="padding: 4px 0; font-size: 12px; color: #f87171; font-family: monospace;">-₹{{ number_format($order->discount_total, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #9ca3af;">White-Glove Insured Delivery</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #4ade80; font-weight: 600;">COMPLIMENTARY</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-size: 12px; color: #9ca3af;">GST (18% inclusive)</td>
                                    <td align="right" style="padding: 4px 0; font-size: 12px; color: #ffffff; font-family: monospace;">₹{{ number_format($order->tax_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 0 0 0; border-top: 1px solid #27272a; font-size: 14px; font-weight: 800; color: #ffffff;">Total Settled</td>
                                    <td align="right" style="padding: 14px 0 0 0; border-top: 1px solid #27272a; font-size: 18px; font-weight: 800; color: #ffffff; font-family: monospace;">₹{{ number_format($order->grand_total, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="{{ route('order.invoice', $order->order_number) }}" target="_blank" style="display: inline-block; background-color: #ffffff; color: #000000; font-size: 12px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; padding: 14px 32px; border-radius: 9999px; box-shadow: 0 4px 14px rgba(255,255,255,0.2);">
                                View & Download Official GST Invoice &rarr;
                            </a>
                        </td>
                    </tr>

                    <!-- Destination & Logistics -->
                    <tr>
                        <td style="padding: 24px 40px; background-color: #111214; border-top: 1px solid #222326; font-size: 11px; line-height: 1.6; color: #71717a;">
                            <div style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #a1a1aa; margin-bottom: 6px;">
                                Delivery Destination
                            </div>
                            @if($order->shippingAddress)
                                <div style="color: #d4d4d8;">
                                    <strong>{{ $order->shippingAddress->full_name }}</strong><br>
                                    {{ $order->shippingAddress->address_line_1 }}@if($order->shippingAddress->address_line_2), {{ $order->shippingAddress->address_line_2 }}@endif<br>
                                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} &mdash; {{ $order->shippingAddress->postal_code }}<br>
                                    Phone: {{ $order->shippingAddress->phone }}
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 28px 40px; text-align: center; font-size: 10px; color: #52525b; line-height: 1.6; background-color: #0d0e10;">
                            ATOZ GADGETZ PRIVATE LIMITED &bull; Plot No. 42, Sector 18, Udyog Vihar Phase IV, Gurugram, HR 122015<br>
                            GSTIN: 06AABCA1234F1Z5 &bull; Toll-Free Concierge: 1800 200 9988 &bull; Email: concierge@atozgadgetz.com<br>
                            &copy; {{ date('Y') }} ATOZ GADGETZ. All rights reserved. Precision Crafted Instruments.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
