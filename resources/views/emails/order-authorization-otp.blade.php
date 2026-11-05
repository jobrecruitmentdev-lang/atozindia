<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquisition Authorization Code — ATOZ GADGETZ</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0c0d0e; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #e5e7eb; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0c0d0e; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="560" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; width: 100%; background-color: #141517; border: 1px solid #27272a; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 36px 28px 36px; border-bottom: 1px solid #222326; background: radial-gradient(circle at 50% 0%, #1e2024, #141517);">
                            <div style="font-size: 19px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; color: #ffffff;">
                                ATOZ GADGETZ
                            </div>
                            <div style="font-size: 10px; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; color: #a1a1aa; margin-top: 6px;">
                                Acquisition Authorization Protocol
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 40px 20px 40px; text-align: center;">
                            <div style="display: inline-block; padding: 6px 14px; background-color: #1e1b4b; border: 1px solid #3730a3; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #a5b4fc; margin-bottom: 16px;">
                                Order Confirmation 2FA
                            </div>
                            <h1 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 800; letter-spacing: -0.02em; color: #ffffff;">
                                Authorize Your Acquisition
                            </h1>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #9ca3af;">
                                Dear <strong style="color: #ffffff;">{{ $userName }}</strong>, an acquisition request has been submitted from your verified collector account. Enter the authorization code below on your checkout terminal to finalize the order.
                            </p>
                        </td>
                    </tr>

                    <!-- Order Summary Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #1a1b1e; border: 1px solid #27272a; border-radius: 12px; padding: 16px;">
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Instruments Reserved</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 12px; font-weight: 600; color: #ffffff;">{{ $itemsCount }} piece(s)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Total Payable</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 15px; font-weight: 800; color: #ffffff; font-family: monospace;">₹{{ number_format($orderTotal, 2) }}</td>
                                </tr>
                                @if($shippingSummary)
                                <tr>
                                    <td style="padding: 6px 10px; font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 0.08em;">Destination</td>
                                    <td align="right" style="padding: 6px 10px; font-size: 11px; color: #a1a1aa;">{{ $shippingSummary }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- 6-Digit OTP Box -->
                    <tr>
                        <td align="center" style="padding: 10px 40px 24px 40px;">
                            <table role="presentation" border="0" cellspacing="0" cellpadding="0" style="background-color: #1a1b1e; border: 1px solid #3f3f46; border-radius: 16px; padding: 18px 32px;">
                                <tr>
                                    <td align="center" style="font-family: 'JetBrains Mono', Courier, monospace; font-size: 38px; font-weight: 800; letter-spacing: 0.35em; color: #60a5fa;">
                                        {{ $otp }}
                                    </td>
                                </tr>
                            </table>
                            <div style="font-size: 11px; color: #71717a; margin-top: 12px; font-family: monospace;">
                                Passcode expires in <strong style="color: #e4e4e7;">5 minutes</strong>
                            </div>
                        </td>
                    </tr>

                    <!-- Security Notice -->
                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <div style="background-color: #111214; border: 1px dashed #27272a; border-radius: 12px; padding: 16px; font-size: 11px; line-height: 1.6; color: #a1a1aa; text-align: left;">
                                <strong style="color: #ffffff; display: block; margin-bottom: 2px;">Fraud Prevention Notice:</strong>
                                Never disclose this 6-digit passcode to anyone. Submitting this code confirms your financial obligation for Order dispatch.
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px; text-align: center; font-size: 10px; color: #52525b; line-height: 1.6; background-color: #0d0e10; border-top: 1px solid #222326;">
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
