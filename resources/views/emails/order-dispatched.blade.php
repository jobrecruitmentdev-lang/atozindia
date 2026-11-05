<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatched With Care — ATOZ GADGETZ</title>
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
                                White-Glove Dispatch Notice
                            </div>
                        </td>
                    </tr>

                    <!-- Dispatch Hero -->
                    <tr>
                        <td style="padding: 36px 40px 24px 40px; text-align: center;">
                            <div style="display: inline-block; padding: 6px 14px; background-color: #1e1b4b; border: 1px solid #3730a3; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #a5b4fc; margin-bottom: 16px;">
                                &bull; In Secured Transit
                            </div>
                            <h1 style="margin: 0 0 10px 0; font-size: 26px; font-weight: 800; letter-spacing: -0.02em; color: #ffffff;">
                                Your Instrument Is In Motion
                            </h1>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #9ca3af;">
                                Order <strong style="color: #ffffff; font-family: monospace;">#{{ $order->order_number }}</strong> has cleared our final laboratory acoustic and optical calibration checks and has been handed to our armored courier partner.
                            </p>
                        </td>
                    </tr>

                    <!-- Tracking Card -->
                    <tr>
                        <td style="padding: 0 40px 28px 40px;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #1a1b1e; border: 1px solid #2e3036; border-radius: 14px; padding: 20px;">
                                <tr>
                                    <td style="padding-bottom: 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: #a1a1aa;">
                                        Airway Bill & Carrier Coordinates
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="font-size: 12px; color: #71717a;">Logistics Partner:</div>
                                        <div style="font-size: 14px; font-weight: 700; color: #ffffff; margin-top: 2px;">
                                            {{ $shipment?->carrier ?? 'Blue Dart Apex Armored Express' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 14px;">
                                        <div style="font-size: 12px; color: #71717a;">Tracking Docket / AWB:</div>
                                        <div style="font-size: 16px; font-weight: 800; font-family: monospace; color: #60a5fa; margin-top: 2px;">
                                            {{ $shipment?->tracking_number ?? ('BD-' . rand(70000000, 79999999)) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 14px;">
                                        <div style="font-size: 12px; color: #71717a;">Estimated Arrival Window:</div>
                                        <div style="font-size: 13px; font-weight: 700; color: #4ade80; margin-top: 2px;">
                                            {{ now()->addDays(2)->format('l, d F Y') }} (Priority White-Glove Slot)
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Instructions -->
                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <div style="background-color: #111214; border: 1px dashed #27272a; border-radius: 12px; padding: 18px; font-size: 11px; line-height: 1.6; color: #a1a1aa;">
                                <strong style="color: #ffffff; display: block; margin-bottom: 4px;">White-Glove Acceptance Advisory:</strong>
                                Please verify the tamper-evident holographic atelier security seal before accepting the consignment. You are entitled to an unboxing inspection in the presence of our courier executive.
                            </div>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="{{ route('order.invoice', $order->order_number) }}" target="_blank" style="display: inline-block; background-color: #ffffff; color: #000000; font-size: 12px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; padding: 14px 32px; border-radius: 9999px; box-shadow: 0 4px 14px rgba(255,255,255,0.2);">
                                Download Official Tax Invoice (PDF) &rarr;
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 28px 40px; text-align: center; font-size: 10px; color: #52525b; line-height: 1.6; background-color: #0d0e10; border-top: 1px solid #222326;">
                            ATOZ GADGETZ PRIVATE LIMITED &bull; Toll-Free Concierge: 1800 200 9988<br>
                            &copy; {{ date('Y') }} ATOZ GADGETZ. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
