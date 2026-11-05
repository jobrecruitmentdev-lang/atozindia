@extends('layouts.app')

@section('title', 'Shopping Bag — ATOZ GADGETZ')
@section('meta_description', 'Review your selected atelier instruments and proceed to secure checkout.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-12">
    
    <div>
        <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-2">
            <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
            <span>/</span>
            <span class="text-neutral-900">Shopping Bag</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-neutral-950">
            Your Atelier Selection
        </h1>
    </div>

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-[36px] border border-neutral-200/80 p-12 sm:p-20 text-center space-y-6 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-neutral-100 text-neutral-400 mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-neutral-950">Your Shopping Bag is Currently Empty</h2>
                <p class="text-xs text-neutral-500 max-w-sm mx-auto">Explore our curated collection of acoustic instruments, titanium wearables, and desk architectures.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="inline-block px-8 py-4 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-colors shadow-lg">
                Explore Catalogue
            </a>
        </div>
    @else
        <!-- Free Shipping Tier Bar -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold">
                @if($totals['remaining_for_free_shipping'] > 0)
                    <span class="text-neutral-700">Add <strong class="font-mono text-black">₹{{ number_format($totals['remaining_for_free_shipping'], 2) }}</strong> more to unlock <strong class="text-emerald-700 uppercase">Complimentary Express Delivery</strong></span>
                @else
                    <span class="text-emerald-700 font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>You have unlocked Complimentary White-Glove Delivery!</span>
                    </span>
                @endif
                <span class="font-mono text-neutral-400 text-[11px]">Threshold: ₹{{ number_format($totals['free_shipping_threshold'], 2) }}</span>
            </div>
            @php
                $pct = min(100, round(($totals['subtotal'] / ($totals['free_shipping_threshold'] ?: 2999)) * 100));
            @endphp
            <div class="w-full h-2 rounded-full bg-neutral-100 overflow-hidden">
                <div class="h-full bg-neutral-900 transition-all duration-500 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left 8 Cols: Cart Items -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden divide-y divide-neutral-100">
                @foreach($cart->items as $item)
                    <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:bg-neutral-50/30 transition-colors">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-20 h-20 rounded-2xl bg-neutral-100 border border-neutral-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                @php
                                    $thumb = $item->product->primary_image;
                                    if ($item->variant && $item->variant->attributeValues->isNotEmpty()) {
                                        $col = $item->variant->attributeValues->firstWhere('attribute.type', 'color');
                                        if ($col) {
                                            $colImg = $item->product->images->firstWhere('attribute_value_id', $col->id);
                                            if ($colImg) $thumb = $colImg->image_path;
                                        }
                                    }
                                @endphp
                                <img src="{{ $thumb }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-1">
                                <h3 class="font-bold text-sm text-neutral-950">
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="hover:underline">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                @if($item->variant)
                                    <div class="text-[11px] text-neutral-500 font-mono">
                                        {{ $item->variant->attributes_summary }} &bull; SKU: {{ $item->variant->sku }}
                                    </div>
                                @endif
                                <div class="font-mono text-xs font-bold text-neutral-800">
                                    ₹{{ number_format($item->unit_price, 2) }} each
                                </div>
                            </div>
                        </div>

                        <!-- Stepper & Line Total -->
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-neutral-100">
                            <!-- Stepper Form -->
                            <div class="flex items-center border border-neutral-200 rounded-xl bg-white p-0.5">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center text-neutral-500 hover:text-black font-bold">&minus;</button>
                                </form>
                                <span class="w-8 text-center font-mono font-bold text-xs">{{ $item->quantity }}</span>
                                <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center text-neutral-500 hover:text-black font-bold">&plus;</button>
                                </form>
                            </div>

                            <!-- Total -->
                            <div class="text-right">
                                <span class="font-mono font-extrabold text-sm text-neutral-950 block">
                                    ₹{{ number_format($item->total_price, 2) }}
                                </span>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold underline mt-0.5">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right 4 Cols: Order Summary & Coupon -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Promo Code Box -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-950">Promotional Privileges</h3>
                    <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="code" value="{{ $cart->coupon_code }}" placeholder="Enter code (e.g. WELCOME10)" class="flex-1 text-xs font-mono uppercase bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-semibold">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                            Apply
                        </button>
                    </form>
                    @if($cart->coupon_code)
                        <div class="flex items-center justify-between text-xs text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200">
                            <span>Applied: <strong>{{ $cart->coupon_code }}</strong></span>
                            <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                @csrf
                                <button type="submit" class="text-rose-600 font-bold hover:underline">&times; Remove</button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Order Totals Box -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Order Economics</h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-neutral-600">
                            <span>Subtotal ({{ $totals['items_count'] }} items)</span>
                            <span class="font-mono text-neutral-900 font-bold">₹{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        @if($totals['discount'] > 0)
                            <div class="flex justify-between text-rose-600 font-medium">
                                <span>Privilege Discount</span>
                                <span class="font-mono font-bold">-₹{{ number_format($totals['discount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-neutral-600">
                            <span>White-Glove Shipping</span>
                            <span class="font-mono text-neutral-900 font-bold">
                                {{ $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'COMPLIMENTARY' }}
                            </span>
                        </div>

                        <div class="flex justify-between text-neutral-600">
                            <span>GST (18% inclusive)</span>
                            <span class="font-mono text-neutral-900 font-bold">₹{{ number_format($totals['tax_amount'], 2) }}</span>
                        </div>

                        <div class="border-t border-neutral-200 pt-4 flex justify-between items-baseline font-bold text-neutral-950 text-base">
                            <span>Total Due</span>
                            <span class="font-mono text-xl sm:text-2xl font-extrabold text-neutral-950">₹{{ number_format($totals['total'], 2) }}</span>
                        </div>
                    </div>

                    @auth('web')
                        <a href="{{ route('checkout.index') }}" class="w-full py-4 rounded-2xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-all flex items-center justify-center gap-2 shadow-xl">
                            <span>Proceed to Secure Checkout</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login', ['redirect' => 'checkout']) }}" class="w-full py-4 rounded-2xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-all flex items-center justify-center gap-2 shadow-xl">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Sign In to Complete Acquisition</span>
                        </a>
                        <p class="text-[11px] text-neutral-500 text-center">Account registration & 2FA passcode required to place order.</p>
                    @endauth

                    <div class="text-[11px] text-neutral-400 text-center space-y-1">
                        <p>256-Bit Encrypted Secure Checkout</p>
                        <p>COD & Direct Wire Transfer Accepted</p>
                    </div>
                </div>

            </div>

        </div>
    @endif

</div>
@endsection
