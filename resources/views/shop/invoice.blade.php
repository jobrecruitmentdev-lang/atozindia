<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice #INV-{{ str_replace('AG-', '', $order->order_number) }} — ATOZ GADGETZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        *, ::after, ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f5f5f7;
            color: #111827;
            -webkit-font-smoothing: antialiased;
            padding: 40px 20px;
        }
        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 48px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .header-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 32px;
            border-bottom: 2px solid #111827;
            padding-bottom: 28px;
            margin-bottom: 32px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #000000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 2px;
        }
        .company-meta {
            font-size: 12px;
            line-height: 1.6;
            color: #4b5563;
            margin-top: 14px;
        }
        .invoice-meta-box {
            background-color: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            text-align: right;
        }
        .invoice-badge {
            display: inline-block;
            background: #000000;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .meta-line {
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 4px;
        }
        .meta-line strong {
            color: #111827;
        }
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
            padding: 20px;
            background-color: #fafafa;
            border: 1px solid #f3f4f6;
            border-radius: 14px;
        }
        .address-col-title {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 10px;
        }
        .address-content {
            font-size: 12px;
            line-height: 1.65;
            color: #374151;
        }
        .address-content strong {
            color: #111827;
            font-size: 13px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        table.items-table th {
            background: #111827;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 14px;
            text-align: left;
        }
        table.items-table th.text-right,
        table.items-table td.text-right {
            text-align: right;
        }
        table.items-table th.text-center,
        table.items-table td.text-center {
            text-align: center;
        }
        table.items-table td {
            padding: 14px;
            font-size: 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .item-name {
            font-weight: 700;
            color: #111827;
            font-size: 13px;
        }
        .item-spec {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }
        .calculation-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 32px;
            border-top: 1px solid #e5e7eb;
            padding-top: 24px;
            margin-bottom: 32px;
        }
        .words-section {
            background-color: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
        }
        .words-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #9ca3af;
            margin-bottom: 6px;
        }
        .words-val {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            line-height: 1.4;
        }
        .tax-breakdown-table {
            width: 100%;
            margin-top: 14px;
            font-size: 11px;
            border-collapse: collapse;
        }
        .tax-breakdown-table td {
            padding: 4px 0;
            color: #4b5563;
        }
        .tax-breakdown-table td.amount {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .summary-table {
            width: 100%;
            font-size: 12px;
        }
        .summary-table tr td {
            padding: 6px 0;
            color: #4b5563;
        }
        .summary-table tr td.amount {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .summary-table tr.grand-total-row td {
            border-top: 2px solid #111827;
            padding-top: 12px;
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }
        .summary-table tr.grand-total-row td.amount {
            font-size: 18px;
            color: #000000;
        }
        .legal-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            font-size: 11px;
            color: #6b7280;
            line-height: 1.6;
        }
        .signature-box {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: flex-end;
        }
        .digital-seal {
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 10px 16px;
            display: inline-block;
            background: #fafafa;
            text-align: center;
            margin-bottom: 8px;
        }
        .seal-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #059669;
            font-weight: 700;
        }
        .seal-sub {
            font-size: 10px;
            color: #374151;
            font-weight: 600;
        }

        /* Action Bar for Screen Viewing */
        .action-bar {
            max-width: 900px;
            margin: 0 auto 24px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-print {
            background-color: #000000;
            color: #ffffff;
            border: 1px solid #000000;
        }
        .btn-print:hover {
            background-color: #27272a;
        }
        .btn-back {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-back:hover {
            background-color: #f3f4f6;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .invoice-wrapper {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 18mm;
            }
        }
    </style>
</head>
<body>

    <!-- Print / Download Top Bar -->
    <div class="action-bar no-print">
        <a href="{{ url()->previous() ?: route('checkout.confirmation', $order->order_number) }}" class="btn-action btn-back">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Return to Order</span>
        </a>

        <div style="display: flex; gap: 12px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Official Invoice (A4)</span>
            </button>
        </div>
    </div>

    <!-- Main Tax Invoice Card -->
    <div class="invoice-wrapper">

        <!-- Header -->
        <div class="header-grid">
            <div>
                <div class="brand-title">
                    <span>ATOZ GADGETZ</span>
                </div>
                <div class="brand-subtitle">Atelier of Precision Technology & Luxury Instruments</div>
                
                <div class="company-meta">
                    <strong>ATOZ GADGETZ PRIVATE LIMITED</strong><br>
                    Plot No. 42, Sector 18, Udyog Vihar Phase IV<br>
                    Gurugram, Haryana &mdash; 122015, India<br>
                    <strong>GSTIN:</strong> 06AABCA1234F1Z5 &bull; <strong>CIN:</strong> U74999HR2023PTC109876<br>
                    <strong>Email:</strong> concierge@atozgadgetz.com &bull; <strong>Toll-Free:</strong> 1800 200 9988
                </div>
            </div>

            <div class="invoice-meta-box">
                <span class="invoice-badge">Tax Invoice &bull; Original for Recipient</span>
                
                <div class="meta-line">Invoice No: <strong class="mono">INV/2026-27/{{ substr($order->order_number, -8) }}</strong></div>
                <div class="meta-line">Invoice Date: <strong>{{ $order->created_at->format('d F Y') }}</strong></div>
                <div class="meta-line">Order Reference: <strong class="mono">#{{ $order->order_number }}</strong></div>
                <div class="meta-line">Payment Method: <strong style="text-transform: uppercase;">{{ $order->payment_method }} ({{ $order->payment_status }})</strong></div>
                <div class="meta-line">Place of Supply: <strong>{{ $order->shippingAddress?->state ?? 'Haryana' }} (06)</strong></div>
                <div class="meta-line">Reverse Charge Applicable: <strong>No</strong></div>
            </div>
        </div>

        <!-- Address Grid -->
        <div class="address-grid">
            <div>
                <div class="address-col-title">Billed To (Customer Details)</div>
                <div class="address-content">
                    @php $billing = $order->billingAddress ?: $order->shippingAddress; @endphp
                    @if($billing)
                        <strong>{{ $billing->full_name }}</strong><br>
                        {{ $billing->address_line_1 }}<br>
                        @if($billing->address_line_2) {{ $billing->address_line_2 }}<br> @endif
                        {{ $billing->city }}, {{ $billing->state }} &mdash; {{ $billing->postal_code }}<br>
                        {{ $billing->country ?? 'India' }}<br>
                        <strong>Phone:</strong> {{ $billing->phone }} &bull; <strong>Email:</strong> {{ $order->customer_email }}
                    @else
                        <strong>{{ $order->customer_email }}</strong><br>
                        Phone: {{ $order->customer_phone }}
                    @endif
                </div>
            </div>

            <div>
                <div class="address-col-title">Shipped / Delivered To</div>
                <div class="address-content">
                    @if($order->shippingAddress)
                        <strong>{{ $order->shippingAddress->full_name }}</strong><br>
                        {{ $order->shippingAddress->address_line_1 }}<br>
                        @if($order->shippingAddress->address_line_2) {{ $order->shippingAddress->address_line_2 }}<br> @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} &mdash; {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country ?? 'India' }}<br>
                        <strong>Phone:</strong> {{ $order->shippingAddress->phone }}
                    @else
                        <em>Same as Billing Destination</em>
                    @endif
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th>Description of Goods</th>
                    <th class="text-center" style="width: 90px;">HSN / SAC</th>
                    <th class="text-center" style="width: 50px;">Qty</th>
                    <th class="text-right" style="width: 110px;">Unit Rate</th>
                    <th class="text-right" style="width: 110px;">Taxable Value</th>
                    <th class="text-right" style="width: 80px;">GST</th>
                    <th class="text-right" style="width: 120px;">Amount (INR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                    @php
                        // Derive taxable base assuming price is tax-inclusive at 18% standard electronic GST
                        $taxRate = 18.0;
                        $taxableUnitPrice = round($item->unit_price / (1 + ($taxRate / 100)), 2);
                        $itemTaxableTotal = round($item->subtotal / (1 + ($taxRate / 100)), 2);
                        $itemGstAmount = $item->subtotal - $itemTaxableTotal;
                    @endphp
                    <tr>
                        <td class="text-center mono">{{ $idx + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            <div class="item-spec mono">
                                SKU: {{ $item->sku }}
                                @if($item->variant_title) &bull; {{ $item->variant_title }} @endif
                            </div>
                        </td>
                        <td class="text-center mono">{{ $item->hsn_code }}</td>
                        <td class="text-center mono">{{ $item->quantity }}</td>
                        <td class="text-right mono">₹{{ number_format($taxableUnitPrice, 2) }}</td>
                        <td class="text-right mono">₹{{ number_format($itemTaxableTotal, 2) }}</td>
                        <td class="text-right mono">18%</td>
                        <td class="text-right mono" style="font-weight: 700;">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Calculations & Tax Breakdown -->
        <div class="calculation-grid">
            <div>
                <div class="words-section">
                    <div class="words-title">Total In Words</div>
                    <div class="words-val">{{ \App\Helpers\CurrencyHelper::inWords($order->grand_total) }}</div>
                </div>

                @php
                    $isHaryana = strtolower($order->shippingAddress?->state ?? 'haryana') === 'haryana';
                    $totalTax = $order->tax_total > 0 ? $order->tax_total : round($order->subtotal - ($order->subtotal / 1.18), 2);
                    $halfTax = round($totalTax / 2, 2);
                @endphp

                <table class="tax-breakdown-table">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 4px 0; text-align: left; font-size: 10px; text-transform: uppercase; color: #9ca3af;">GST Component</th>
                            <th style="padding: 4px 0; text-align: center; font-size: 10px; text-transform: uppercase; color: #9ca3af;">Rate</th>
                            <th style="padding: 4px 0; text-align: right; font-size: 10px; text-transform: uppercase; color: #9ca3af;">Tax Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($isHaryana)
                            <tr>
                                <td>Central GST (CGST)</td>
                                <td style="text-align: center;" class="mono">9.0%</td>
                                <td class="amount mono">₹{{ number_format($halfTax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>State GST (SGST - Haryana)</td>
                                <td style="text-align: center;" class="mono">9.0%</td>
                                <td class="amount mono">₹{{ number_format($halfTax, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>Integrated GST (IGST - Inter-State)</td>
                                <td style="text-align: center;" class="mono">18.0%</td>
                                <td class="amount mono">₹{{ number_format($totalTax, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div>
                <table class="summary-table">
                    <tr>
                        <td>Gross Taxable Value</td>
                        <td class="amount mono">₹{{ number_format($order->subtotal - $totalTax, 2) }}</td>
                    </tr>
                    @if($order->discount_total > 0)
                        <tr>
                            <td style="color: #dc2626;">Privilege Coupon / Atelier Concession</td>
                            <td class="amount mono" style="color: #dc2626;">-₹{{ number_format($order->discount_total, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Total Taxes (GST 18%)</td>
                        <td class="amount mono">₹{{ number_format($totalTax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>White-Glove Insured Dispatch</td>
                        <td class="amount mono">
                            @if($order->shipping_total > 0)
                                ₹{{ number_format($order->shipping_total, 2) }}
                            @else
                                <span style="color: #059669; font-weight: 700;">FREE (COMPLIMENTARY)</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>Total Invoice Value (INR)</td>
                        <td class="amount mono">₹{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Legal Notice & Signatory -->
        <div class="legal-footer">
            <div>
                <strong>Terms of Acquisition & Warranty:</strong><br>
                1. All instruments carry a 2-Year International Atelier Manufacturer Warranty against internal mechanical and electronic craftsmanship defects.<br>
                2. Goods once unsealed are eligible for return only within our 14-day White-Glove Inspection Window.<br>
                3. Subject exclusively to the jurisdiction of the Courts at Gurugram, Haryana.
            </div>

            <div class="signature-box">
                <div class="digital-seal">
                    <div class="seal-title">&#10003; Cryptographically Certified</div>
                    <div class="seal-sub">ATOZ GADGETZ Concierge Atelier</div>
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #111827;">For ATOZ GADGETZ PRIVATE LIMITED</div>
                <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">Authorized Signatory / Tax Officer</div>
            </div>
        </div>

    </div>

</body>
</html>
