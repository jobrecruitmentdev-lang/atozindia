<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ATOZ GADGETZ</title>
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
                                Collector Membership Circle
                            </div>
                        </td>
                    </tr>

                    <!-- Welcome Hero -->
                    <tr>
                        <td style="padding: 36px 40px 24px 40px; text-align: center;">
                            <div style="display: inline-block; padding: 6px 14px; background-color: #27272a; border: 1px solid #3f3f46; border-radius: 9999px; font-size: 10px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #f4f4f5; margin-bottom: 16px;">
                                Atelier Access Granted
                            </div>
                            <h1 style="margin: 0 0 12px 0; font-size: 26px; font-weight: 800; letter-spacing: -0.02em; color: #ffffff;">
                                Welcome, {{ $user->name }}
                            </h1>
                            <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #9ca3af;">
                                You have officially entered the circle of connoisseurs who demand uncompromising precision, refined minimalist architecture, and unadorned acoustic, horological, and desk instruments.
                            </p>
                        </td>
                    </tr>

                    <!-- Privileges -->
                    <tr>
                        <td style="padding: 0 40px 28px 40px;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #1a1b1e; border: 1px solid #27272a; border-radius: 14px; padding: 20px;">
                                <tr>
                                    <td style="padding-bottom: 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: #a1a1aa;">
                                        Your Collector Privileges
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 12px; color: #e4e4e7; line-height: 1.5;">
                                        <strong style="color: #ffffff;">&bull; 2-Year International Atelier Warranty:</strong> Every numbered piece includes factory-backed repair or replacement coverage.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 12px; color: #e4e4e7; line-height: 1.5;">
                                        <strong style="color: #ffffff;">&bull; Complimentary White-Glove Dispatch:</strong> Fully insured courier transit with unboxing and inspection protocol.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 12px; color: #e4e4e7; line-height: 1.5;">
                                        <strong style="color: #ffffff;">&bull; Dedicated Atelier Concierge:</strong> Direct access to technical engineers for calibration, sizing, and bespoke inquiries.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="{{ route('shop.index') }}" style="display: inline-block; background-color: #ffffff; color: #000000; font-size: 12px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; padding: 14px 32px; border-radius: 9999px; box-shadow: 0 4px 14px rgba(255,255,255,0.2);">
                                Explore Curated Catalogue &rarr;
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 28px 40px; text-align: center; font-size: 10px; color: #52525b; line-height: 1.6; background-color: #0d0e10; border-top: 1px solid #222326;">
                            ATOZ GADGETZ PRIVATE LIMITED &bull; Toll-Free Concierge: 1800 200 9988 &bull; concierge@atozgadgetz.com<br>
                            &copy; {{ date('Y') }} ATOZ GADGETZ. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
