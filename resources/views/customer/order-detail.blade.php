@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' — ATOZ GADGETZ')
@section('meta_description', 'Detailed acquisition invoice and delivery tracking docket.')

@section('content')
<div class="max-w-[1000px] mx-auto px-6 sm:px-10 py-12 space-y-8">

    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('customer.orders') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1">
            &larr; Back to Order History
        </a>

        <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print Docket</span>
        </button>
    </div>

    <!-- Order Header Card -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-10 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-100 pb-6">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 font-mono">Invoice Reference</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 font-mono">#{{ $order->order_number }}</h1>
                <p class="text-xs text-neutral-500 mt-0.5">Placed on {{ $order->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-neutral-900 text-white">
                    {{ $order->order_status }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ $order->payment_status }}
                </span>
            </div>
        </div>

        <!-- Addresses Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs border-b border-neutral-100 pb-6">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Shipping Destination</span>
                @if($order->shippingAddress)
                    <div class="text-neutral-800 leading-relaxed">
                        <strong class="text-black">{{ $order->shippingAddress->full_name }}</strong><br>
                        {{ $order->shippingAddress->address_line1 }}<br>
                        @if($order->shippingAddress->address_line2) {{ $order->shippingAddress->address_line2 }}<br> @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country }}<br>
                        Phone: {{ $order->shippingAddress->phone }}
                    </div>
                @endif
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Payment Method</span>
                <span class="font-mono uppercase font-bold text-neutral-900 block mb-2">{{ $order->payment_method }}</span>
                @if($order->payment_method === 'cod')
                    <p class="text-neutral-500 text-[11px]">Payable upon courier delivery via cash or wireless card terminal.</p>
                @else
                    <p class="text-neutral-500 text-[11px]">Direct atelier electronic bank wire transfer.</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-950">Acquired Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-neutral-50 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                        <tr>
                            <th class="p-3">Instrument</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3">Price</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="p-3">
                                    <div class="font-bold text-neutral-900">{{ $item->product_name }}</div>
                                    @if($item->variant_title)
                                        <div class="text-[10px] text-neutral-500 font-mono">{{ $item->variant_title }}</div>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-[11px] text-neutral-500">{{ $item->sku }}</td>
                                <td class="p-3 font-mono text-neutral-700">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="p-3 font-mono font-bold text-neutral-900">&times; {{ $item->quantity }}</td>
                                <td class="p-3 font-mono font-bold text-neutral-950 text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Totals -->
            <div class="border-t border-neutral-100 pt-6 flex justify-end">
                <div class="w-full sm:w-64 space-y-2 text-xs">
                    <div class="flex justify-between text-neutral-600">
                        <span>Subtotal</span>
                        <span class="font-mono">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_total > 0)
                        <div class="flex justify-between text-rose-600 font-semibold">
                            <span>Discount ({{ $order->coupon_code }})</span>
                            <span class="font-mono">-₹{{ number_format($order->discount_total, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-neutral-600">
                        <span>Shipping</span>
                        <span class="font-mono">₹{{ number_format($order->shipping_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-neutral-600">
                        <span>GST (18% inclusive)</span>
                        <span class="font-mono">₹{{ number_format($order->tax_total, 2) }}</span>
                    </div>
                    <div class="border-t border-neutral-200 pt-3 flex justify-between font-bold text-sm text-neutral-950">
                        <span>Grand Total</span>
                        <span class="font-mono text-base font-extrabold">₹{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
