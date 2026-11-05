@extends('layouts.app')

@section('title', 'Order Confirmed: #' . $order->order_number . ' — ATOZ GADGETZ')
@section('meta_description', 'Your bespoke order has been registered in our atelier queue.')

@section('content')
<div class="max-w-[850px] mx-auto px-6 sm:px-10 py-16 space-y-12">
    
    <!-- Success Banner Card -->
    <div class="bg-white rounded-[36px] border border-neutral-200/80 p-8 sm:p-14 text-center space-y-6 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 mx-auto flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-700">Reservation Confirmed</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-neutral-950">Thank You For Your Acquisition</h1>
            <p class="text-xs sm:text-sm text-neutral-500 max-w-md mx-auto">
                We have registered Order <strong class="font-mono text-black">#{{ $order->order_number }}</strong>. A confirmation docket has been dispatched to <strong class="font-mono text-black">{{ $order->customer_email }}</strong>.
            </p>
        </div>

        <div class="inline-flex items-center gap-4 p-4 rounded-2xl bg-neutral-50 border border-neutral-200 text-xs font-mono text-neutral-700">
            <span>Status: <strong class="uppercase text-neutral-900">{{ $order->order_status }}</strong></span>
            <span>&bull;</span>
            <span>Payment: <strong class="uppercase text-neutral-900">{{ $order->payment_method }} ({{ $order->payment_status }})</strong></span>
        </div>
    </div>

    <!-- Order Invoicing & Logistics Details -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-10 shadow-sm space-y-8">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 border-b border-neutral-100 pb-8 text-xs">
            <!-- Shipping Info -->
            <div>
                <span class="text-[10px] uppercase font-bold text-neutral-400 tracking-wider block mb-2">Shipping Destination</span>
                @if($order->shippingAddress)
                    <div class="text-neutral-800 leading-relaxed font-medium">
                        <strong class="text-black">{{ $order->shippingAddress->full_name }}</strong><br>
                        {{ $order->shippingAddress->address_line1 }}<br>
                        @if($order->shippingAddress->address_line2) {{ $order->shippingAddress->address_line2 }}<br> @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                        {{ $order->shippingAddress->country }}<br>
                        Phone: {{ $order->shippingAddress->phone }}
                    </div>
                @else
                    <span class="text-neutral-400 italic">No physical destination</span>
                @endif
            </div>

            <!-- Payment & UPI Clearance -->
            <div>
                <span class="text-[10px] uppercase font-bold text-neutral-400 tracking-wider block mb-2">Payment Clearance</span>
                @if($order->payment_method === 'cod')
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200 text-neutral-700 leading-relaxed space-y-2">
                        <strong class="text-neutral-900 block text-xs">Cash on Delivery Protocol</strong>
                        <p class="text-xs text-neutral-600">Our white-glove courier will present your secured parcel. You may settle upon delivery via cash, wireless POS card terminal, or instant mobile UPI scan.</p>
                        <div class="pt-2 flex items-center gap-2 text-[10px] text-neutral-400 font-mono">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Authorized for priority dispatch</span>
                        </div>
                    </div>
                @else
                    <div class="p-5 rounded-2xl bg-neutral-50 border border-neutral-200 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <strong class="text-neutral-900 block text-xs">Instant UPI & Direct Bank Wire</strong>
                                <p class="text-[11px] text-neutral-500 mt-0.5">Scan with Google Pay, PhonePe, Paytm, CRED or BHIM.</p>
                            </div>
                            <!-- Dynamic UPI QR -->
                            <div class="w-24 h-24 rounded-xl bg-white p-1 border border-neutral-200 shadow-sm shrink-0">
                                @php
                                    $upiString = "upi://pay?pa=atozgadgetz@hdfcbank&pn=ATOZ+GADGETZ&am=" . $order->grand_total . "&cu=INR&tn=ORD-" . $order->order_number;
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=4&data=" . urlencode($upiString);
                                @endphp
                                <img src="{{ $qrUrl }}" alt="Scan UPI to Pay" class="w-full h-full object-contain">
                            </div>
                        </div>

                        <!-- Bank Coordinates -->
                        <div class="p-3 bg-white rounded-xl border border-neutral-200/80 text-[11px] space-y-1 font-mono text-neutral-800">
                            <div><span class="text-neutral-400">VPA / UPI ID:</span> <strong>atozgadgetz@hdfcbank</strong></div>
                            <div><span class="text-neutral-400">Account:</span> ATOZ GADGETZ PRIVATE LIMITED</div>
                            <div><span class="text-neutral-400">A/C No:</span> 50200088991122 &bull; <span class="text-neutral-400">IFSC:</span> HDFC0001234</div>
                            <div class="text-[10px] text-neutral-500 pt-1 font-sans">Narration: Quote Order #{{ $order->order_number }}</div>
                        </div>

                        <!-- Copy Button -->
                        <button type="button" 
                                onclick="navigator.clipboard.writeText('atozgadgetz@hdfcbank'); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'UPI ID copied to clipboard!' } }))" 
                                class="w-full py-2 rounded-xl border border-neutral-300 bg-white hover:bg-neutral-100 text-xs font-bold text-neutral-900 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            <span>Copy UPI ID (atozgadgetz@hdfcbank)</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Purchased Line Items -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Acquired Instruments</h3>
            
            <div class="divide-y divide-neutral-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-4 text-xs">
                        <div>
                            <div class="font-bold text-neutral-900">{{ $item->product_name }}</div>
                            <div class="text-[10px] text-neutral-400 font-mono">
                                {{ $item->variant_title ?: 'Standard' }} &bull; SKU: {{ $item->sku }} &bull; Qty: {{ $item->quantity }}
                            </div>
                        </div>
                        <span class="font-mono font-bold text-neutral-950">₹{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
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
                    <span>White-Glove Shipping</span>
                    <span class="font-mono">₹{{ number_format($order->shipping_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-neutral-600">
                    <span>GST (18% inclusive)</span>
                    <span class="font-mono">₹{{ number_format($order->tax_total, 2) }}</span>
                </div>
                <div class="border-t border-neutral-200 pt-3 flex justify-between font-bold text-sm text-neutral-950">
                    <span>Total Settled</span>
                    <span class="font-mono text-lg font-extrabold">₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4 pt-4">
        <a href="{{ route('order.invoice', $order->order_number) }}" target="_blank" class="px-6 py-3 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-bold uppercase tracking-wider text-neutral-800 transition-colors shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Official GST Tax Invoice (PDF) &rarr;</span>
        </a>

        <a href="{{ route('shop.index') }}" class="px-8 py-3 rounded-full bg-neutral-950 text-white text-xs font-bold uppercase tracking-widest hover:bg-neutral-800 transition-colors shadow-lg">
            Continue Exploring Catalogue &rarr;
        </a>
    </div>

</div>
@endsection
