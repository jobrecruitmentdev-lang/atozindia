@extends('layouts.app')

@section('title', 'Secure Checkout — ATOZ GADGETZ')
@section('meta_description', 'Complete your order with encrypted concierge checkout.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-8" 
     x-data="{ 
        sameAsShipping: true, 
        paymentMethod: 'cod',
        selectedAddressId: null
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">
        <a href="{{ route('home') }}" class="hover:text-black">Home</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-black">Shopping Bag</a>
        <span>/</span>
        <span class="text-neutral-900 font-bold">Checkout</span>
    </nav>

    <!-- Error Notice if any -->
    @if($errors->has('checkout_error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 font-medium">
            {{ $errors->first('checkout_error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        @csrf

        <!-- LEFT 7 COLS: SHIPPING, CONTACT & PAYMENT -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- 1. Contact Information -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">1. Concierge Contact</h3>
                    @guest
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-neutral-600 hover:text-black underline">Have an account? Log in &rarr;</a>
                    @endguest
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="concierge@example.com" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                        <span class="text-[10px] text-neutral-400 mt-1 block">Invoicing and track & trace links are sent here.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Contact Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="+91 98765 43210" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                        <span class="text-[10px] text-neutral-400 mt-1 block">For delivery coordination.</span>
                    </div>
                </div>
            </div>

            <!-- 2. Shipping Destination -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">2. Shipping Destination</h3>

                @if($savedAddresses->isNotEmpty())
                    <div class="space-y-2 mb-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600">Choose from Saved Addresses</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($savedAddresses as $addr)
                                <label class="p-3.5 rounded-2xl border border-neutral-200 bg-neutral-50 cursor-pointer text-xs hover:border-black transition-colors block">
                                    <input type="radio" name="saved_address_id" value="{{ $addr->id }}" class="mr-2 text-black"
                                           @change="
                                               document.getElementById('ship_fname').value = '{{ addslashes($addr->first_name) }}';
                                               document.getElementById('ship_lname').value = '{{ addslashes($addr->last_name) }}';
                                               document.getElementById('ship_addr1').value = '{{ addslashes($addr->address_line1) }}';
                                               document.getElementById('ship_addr2').value = '{{ addslashes($addr->address_line2) }}';
                                               document.getElementById('ship_city').value = '{{ addslashes($addr->city) }}';
                                               document.getElementById('ship_state').value = '{{ addslashes($addr->state) }}';
                                               document.getElementById('ship_zip').value = '{{ addslashes($addr->postal_code) }}';
                                           ">
                                    <span class="font-bold text-neutral-900">{{ $addr->full_name }}</span>
                                    <div class="text-[11px] text-neutral-500 mt-1 truncate">{{ $addr->address_line1 }}, {{ $addr->city }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">First Name *</label>
                        <input type="text" name="shipping[first_name]" id="ship_fname" value="{{ old('shipping.first_name', auth()->user()?->first_name ?? auth()->user()?->name) }}" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Last Name</label>
                        <input type="text" name="shipping[last_name]" id="ship_lname" value="{{ old('shipping.last_name', auth()->user()?->last_name) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Street Address *</label>
                        <input type="text" name="shipping[address_line_1]" id="ship_addr1" value="{{ old('shipping.address_line_1') }}" required placeholder="Apartment, suite, building, street" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Apartment, Floor, Landmark (Optional)</label>
                        <input type="text" name="shipping[address_line_2]" id="ship_addr2" value="{{ old('shipping.address_line_2') }}" placeholder="Tower 3, Penthouse B" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">City *</label>
                        <input type="text" name="shipping[city]" id="ship_city" value="{{ old('shipping.city') }}" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">State *</label>
                        <input type="text" name="shipping[state]" id="ship_state" value="{{ old('shipping.state') }}" required placeholder="e.g. Haryana" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Postal / PIN Code *</label>
                        <input type="text" name="shipping[postal_code]" id="ship_zip" value="{{ old('shipping.postal_code') }}" required placeholder="122002" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Country</label>
                        <input type="text" name="shipping[country]" value="India" readonly class="w-full text-xs bg-neutral-100 border border-neutral-200 rounded-xl p-3 text-neutral-600 font-semibold cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-4 border-t border-neutral-100">
                    <label class="flex items-center gap-2.5 text-xs text-neutral-800 cursor-pointer font-medium">
                        <input type="checkbox" name="same_as_shipping" value="1" x-model="sameAsShipping" class="rounded border-neutral-300 text-black">
                        <span>Billing address matches shipping address</span>
                    </label>
                </div>
            </div>

            <!-- 3. Payment Method -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">3. Payment Protocol</h3>

                <div class="space-y-3">
                    <!-- COD Option -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                           :class="paymentMethod === 'cod' ? 'border-neutral-950 bg-neutral-50/80 ring-1 ring-neutral-950' : 'border-neutral-200 bg-white hover:bg-neutral-50/50'">
                        <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-0.5 text-black">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Cash on Delivery (COD)</span>
                            <span class="text-[11px] text-neutral-500 block mt-0.5">Pay upon white-glove arrival via cash, mobile UPI, or card on the courier terminal.</span>
                        </div>
                    </label>

                    <!-- Direct Wire Option -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                           :class="paymentMethod === 'wire' ? 'border-neutral-950 bg-neutral-50/80 ring-1 ring-neutral-950' : 'border-neutral-200 bg-white hover:bg-neutral-50/50'">
                        <input type="radio" name="payment_method" value="wire" x-model="paymentMethod" class="mt-0.5 text-black">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Atelier Direct Bank Wire (NEFT / RTGS / IMPS)</span>
                            <span class="text-[11px] text-neutral-500 block mt-0.5">Wire transfer instructions & corporate account coordinates provided immediately upon order reservation.</span>
                        </div>
                    </label>
                </div>

                <!-- Notes for Courier -->
                <div class="pt-4 border-t border-neutral-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Special Courier Dispatch Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Gate security code, leave at reception, call before delivery" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black"></textarea>
                </div>
            </div>

        </div>

        <!-- RIGHT 5 COLS: IMMUTABLE ORDER SUMMARY & SUBMIT -->
        <div class="lg:col-span-5 sticky top-28 space-y-6">
            
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Atelier Bag ({{ $totals['items_count'] }})</h3>
                    <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black underline">Edit Bag</a>
                </div>

                <!-- Items list -->
                <div class="max-h-72 overflow-y-auto divide-y divide-neutral-100 pr-1">
                    @foreach($cart->items as $item)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-neutral-100 overflow-hidden shrink-0 border border-neutral-200">
                                    <img src="{{ $item->product->primary_image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-neutral-900 line-clamp-1">{{ $item->product->name }}</div>
                                    <div class="text-[10px] text-neutral-500 font-mono">
                                        Qty: {{ $item->quantity }} &bull; {{ $item->variant ? $item->variant->sku : $item->product->base_sku }}
                                    </div>
                                </div>
                            </div>
                            <span class="font-mono font-bold text-neutral-950 shrink-0">₹{{ number_format($item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Breakdown -->
                <div class="border-t border-neutral-100 pt-4 space-y-2.5 text-xs">
                    <div class="flex justify-between text-neutral-600">
                        <span>Subtotal</span>
                        <span class="font-mono font-bold text-neutral-900">₹{{ number_format($totals['subtotal'], 2) }}</span>
                    </div>

                    @if($totals['discount'] > 0)
                        <div class="flex justify-between text-rose-600 font-medium">
                            <span>Promo ({{ $cart->coupon_code }})</span>
                            <span class="font-mono font-bold">-₹{{ number_format($totals['discount'], 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-neutral-600">
                        <span>White-Glove Shipping</span>
                        <span class="font-mono font-bold text-neutral-900">
                            {{ $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'COMPLIMENTARY' }}
                        </span>
                    </div>

                    <div class="flex justify-between text-neutral-600">
                        <span>GST (18% inclusive)</span>
                        <span class="font-mono font-bold text-neutral-900">₹{{ number_format($totals['tax_amount'], 2) }}</span>
                    </div>

                    <div class="border-t border-neutral-200 pt-4 flex justify-between items-baseline font-bold text-neutral-950">
                        <span>Grand Total Due</span>
                        <span class="font-mono text-2xl font-extrabold">₹{{ number_format($totals['total'], 2) }}</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 rounded-2xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-all shadow-xl flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Confirm Order & Dispatch</span>
                </button>

                <div class="text-[10px] text-neutral-400 text-center leading-relaxed">
                    By confirming this reservation, you agree to ATOZ GADGETZ terms of bespoke service, 14-day trial audition, and privacy policy.
                </div>
            </div>

        </div>

    </form>
</div>
@endsection
