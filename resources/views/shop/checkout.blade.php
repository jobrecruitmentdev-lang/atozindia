@extends('layouts.app')

@section('title', 'Secure Checkout — ATOZ GADGETZ')
@section('meta_description', 'Complete your order with 2FA encrypted concierge checkout.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-8" 
     x-data="{ 
        sameAsShipping: true, 
        paymentMethod: 'cod',
        selectedAddressId: null,
        
        // 2FA Order Authorization State
        otpModalOpen: false,
        otpDigits: ['', '', '', '', '', ''],
        otpSending: false,
        otpSubmitting: false,
        otpError: '',
        otpSuccessMsg: '',
        devOtp: '',
        targetEmail: '{{ auth()->user()->email }}',
        timerSeconds: 60,
        timerInterval: null,
        
        get fullOtp() {
            return this.otpDigits.join('');
        },
        
        autoFillDevOtp() {
            if (!this.devOtp) return;
            const arr = String(this.devOtp).split('');
            for (let i = 0; i < 6; i++) {
                this.otpDigits[i] = arr[i] || '';
            }
            this.confirmOrderWithOtp();
        },
        
        startTimer() {
            this.timerSeconds = 60;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if (this.timerSeconds > 0) {
                    this.timerSeconds--;
                } else {
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },
        
        handleDigitInput(index, event) {
            const val = event.target.value.replace(/[^0-9]/g, '');
            this.otpDigits[index] = val ? val[val.length - 1] : '';
            if (val && index < 5) {
                const nextField = this.$refs['otp' + (index + 1)];
                if (nextField) nextField.focus();
            }
            if (this.fullOtp.length === 6) {
                this.confirmOrderWithOtp();
            }
        },
        
        handleBackspace(index, event) {
            if (!this.otpDigits[index] && index > 0) {
                const prevField = this.$refs['otp' + (index - 1)];
                if (prevField) prevField.focus();
            }
        },
        
        handlePaste(event) {
            event.preventDefault();
            const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim();
            const numbers = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
            for (let i = 0; i < 6; i++) {
                this.otpDigits[i] = numbers[i] || '';
            }
            if (numbers.length > 0) {
                const focusIdx = Math.min(numbers.length, 5);
                const targetRef = this.$refs['otp' + focusIdx];
                if (targetRef) targetRef.focus();
            }
            if (numbers.length === 6) {
                this.confirmOrderWithOtp();
            }
        },
        
        async requestOrderOtp() {
            const form = this.$refs.checkoutForm;
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            this.otpSending = true;
            this.otpError = '';
            
            const formData = new FormData(form);
            
            try {
                const res = await fetch('{{ route('checkout.send-otp') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                
                if (!res.ok || !data.success) {
                    this.otpError = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Unable to send authorization code.');
                    this.otpSending = false;
                    return;
                }
                
                this.targetEmail = data.email || '{{ auth()->user()->email }}';
                this.otpModalOpen = true;
                this.otpDigits = ['', '', '', '', '', ''];
                this.otpSuccessMsg = data.message;
                if (data.dev_otp) {
                    this.devOtp = data.dev_otp;
                }
                this.startTimer();
                setTimeout(() => {
                    if (this.$refs.otp0) this.$refs.otp0.focus();
                }, 250);
            } catch (err) {
                this.otpError = 'Network communication error. Please check your connection.';
            } finally {
                this.otpSending = false;
            }
        },
        
        async resendOrderOtp() {
            if (this.timerSeconds > 0) return;
            this.otpSending = true;
            this.otpError = '';
            try {
                const res = await fetch('{{ route('checkout.resend-otp') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.otpSuccessMsg = data.message;
                    if (data.dev_otp) {
                        this.devOtp = data.dev_otp;
                    }
                    this.startTimer();
                } else {
                    this.otpError = data.message || 'Could not resend authorization code.';
                }
            } catch (e) {
                this.otpError = 'Network error during resend. Please try again.';
            } finally {
                this.otpSending = false;
            }
        },
        
        async confirmOrderWithOtp() {
            if (this.fullOtp.length !== 6) {
                this.otpError = 'Please enter all 6 digits of the authorization code.';
                return;
            }
            
            this.otpSubmitting = true;
            this.otpError = '';
            
            try {
                const res = await fetch('{{ route('checkout.verify-otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ otp: this.fullOtp })
                });
                const data = await res.json();
                
                if (res.ok && data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    this.otpError = data.message || 'Invalid authorization code. Please verify.';
                    this.otpDigits = ['', '', '', '', '', ''];
                    if (this.$refs.otp0) this.$refs.otp0.focus();
                }
            } catch (err) {
                this.otpError = 'Network communication failure during verification.';
            } finally {
                this.otpSubmitting = false;
            }
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">
        <a href="{{ route('home') }}" class="hover:text-black">Home</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-black">Shopping Bag</a>
        <span>/</span>
        <span class="text-neutral-900 font-bold">Encrypted Checkout</span>
    </nav>

    <!-- Error Notice if any -->
    @if($errors->has('checkout_error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 font-medium">
            {{ $errors->first('checkout_error') }}
        </div>
    @endif

    <div x-show="otpError && !otpModalOpen" x-cloak class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 font-medium">
        <span x-text="otpError"></span>
    </div>

    <!-- Main Checkout Form -->
    <form x-ref="checkoutForm" method="POST" action="{{ route('checkout.process') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        @csrf

        <!-- LEFT 7 COLS: SHIPPING, CONTACT & PAYMENT -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- 1. Contact Information -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#D4AF37] text-neutral-950 text-[11px] font-black flex items-center justify-center shadow-sm">1</span>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Concierge Collector Account</h3>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[11px] font-bold text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Verified Collector
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Registered Email *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required readonly class="w-full text-xs font-mono bg-neutral-100 border border-neutral-200 rounded-xl p-3 text-neutral-700 font-semibold cursor-not-allowed">
                        <span class="text-[10px] text-neutral-400 mt-1 block">2FA security passcode will be sent to this email.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Contact Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="+91 98765 43210" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-[#D4AF37]">
                        <span class="text-[10px] text-neutral-400 mt-1 block">For courier coordination.</span>
                    </div>
                </div>
            </div>

            <!-- 2. Shipping Destination -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 border-b border-neutral-100 pb-3">
                    <span class="w-6 h-6 rounded-full bg-[#D4AF37] text-neutral-950 text-[11px] font-black flex items-center justify-center shadow-sm">2</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Shipping Destination</h3>
                </div>

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
                <div class="flex items-center gap-2.5 border-b border-neutral-100 pb-3">
                    <span class="w-6 h-6 rounded-full bg-[#D4AF37] text-neutral-950 text-[11px] font-black flex items-center justify-center shadow-sm">3</span>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Payment Protocol</h3>
                </div>

                <div class="space-y-3">
                    <!-- COD Option -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                           :class="paymentMethod === 'cod' ? 'border-[#D4AF37] bg-[#FDF9EE] ring-1 ring-[#D4AF37]' : 'border-neutral-200 bg-white hover:bg-neutral-50/50'">
                        <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-0.5 text-[#D4AF37] focus:ring-[#D4AF37]">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Cash on Delivery (COD)</span>
                            <span class="text-[11px] text-neutral-500 block mt-0.5">Pay upon white-glove arrival via cash, mobile UPI, or card on the courier terminal.</span>
                        </div>
                    </label>

                    <!-- Direct Wire Option -->
                    <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                           :class="paymentMethod === 'wire' ? 'border-[#D4AF37] bg-[#FDF9EE] ring-1 ring-[#D4AF37]' : 'border-neutral-200 bg-white hover:bg-neutral-50/50'">
                        <input type="radio" name="payment_method" value="wire" x-model="paymentMethod" class="mt-0.5 text-[#D4AF37] focus:ring-[#D4AF37]">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Atelier Direct Bank Wire (NEFT / RTGS / IMPS)</span>
                            <span class="text-[11px] text-neutral-500 block mt-0.5">Wire transfer instructions & corporate account coordinates provided immediately upon order reservation.</span>
                        </div>
                    </label>
                </div>

                <!-- Notes for Courier -->
                <div class="pt-4 border-t border-neutral-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Special Courier Dispatch Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Gate security code, leave at reception, call before delivery" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-[#D4AF37]"></textarea>
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

                <!-- 2-Step Trigger Button -->
                <button type="button" 
                        @click="requestOrderOtp()" 
                        :disabled="otpSending" 
                        class="w-full py-4 rounded-2xl btn-gold text-neutral-950 font-black text-xs uppercase tracking-widest shadow-xl flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!otpSending">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Authorize & Place Order (2FA)</span>
                        </span>
                    </template>
                    <template x-if="otpSending">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-neutral-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span>Dispatching Security Code...</span>
                        </span>
                    </template>
                </button>

                <!-- Security Trust Note -->
                <div class="flex items-center justify-center gap-2 text-[10px] text-neutral-400">
                    <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>6-digit email authorization prevents unauthorized purchases</span>
                </div>
            </div>

        </div>

    </form>

    <!-- ======================================================== -->
    <!-- 2FA ORDER AUTHORIZATION MODAL (LUXURY OBSIDIAN DIALOG)  -->
    <!-- ======================================================== -->
    <div x-show="otpModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="otpModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-neutral-950/80 backdrop-blur-md transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="otpModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="otpModalOpen = false"
                 class="relative transform overflow-hidden rounded-3xl bg-[#141517] border border-[#27272a] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 sm:p-8">
                
                <!-- Close Button -->
                <button type="button" 
                        @click="otpModalOpen = false" 
                        class="absolute top-5 right-5 text-neutral-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Modal Header -->
                <div class="text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] mx-auto flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>

                    <div class="inline-block px-3 py-1 rounded-full bg-[#1A1917] border border-[#D4AF37]/40 text-[10px] font-bold tracking-widest text-[#D4AF37] uppercase">
                        2FA Acquisition Protocol
                    </div>

                    <h3 class="text-lg font-bold text-white tracking-tight">Authorize Your Acquisition</h3>
                    
                    <p class="text-xs text-neutral-400 leading-relaxed">
                        A single-use 6-digit passcode has been dispatched to:
                        <span class="block mt-1 font-mono font-bold text-white truncate" x-text="targetEmail"></span>
                    </p>
                </div>

                <!-- Error Alert -->
                <div x-show="otpError" x-cloak class="mt-4 p-3 rounded-xl bg-rose-950/50 border border-rose-800/80 text-xs text-rose-300 font-medium text-center">
                    <span x-text="otpError"></span>
                </div>

                <!-- Success Alert -->
                <div x-show="otpSuccessMsg" x-cloak class="mt-4 p-3 rounded-xl bg-emerald-950/40 border border-emerald-800/80 text-xs text-emerald-300 font-medium text-center">
                    <span x-text="otpSuccessMsg"></span>
                </div>

                <!-- Local Dev Mode Passcode Helper -->
                <template x-if="devOtp">
                    <div class="mt-4 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-center space-y-1.5">
                        <div class="flex items-center justify-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Local Dev Mode &bull; Passcode Generated</span>
                        </div>
                        <div class="font-mono text-xl font-black tracking-[0.3em] text-amber-200" x-text="devOtp"></div>
                        <button type="button" 
                                @click="autoFillDevOtp()" 
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-400/20 hover:bg-amber-400/30 text-[10px] font-bold text-amber-300 transition-colors">
                            <span>Auto-Fill & Authorize</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </template>

                <!-- 6-Box OTP Input -->
                <div class="mt-6 flex justify-between gap-2 max-w-xs mx-auto">
                    <template x-for="(digit, idx) in otpDigits" :key="idx">
                        <input type="text" 
                               maxlength="1" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               class="w-11 h-13 text-center text-xl font-bold font-mono rounded-xl bg-[#1c1d21] border border-[#2e3036] text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all selection:bg-indigo-500"
                               :x-ref="'otp' + idx"
                               x-model="otpDigits[idx]"
                               @input="handleDigitInput(idx, $event)"
                               @keydown.backspace="handleBackspace(idx, $event)"
                               @paste="handlePaste($event)">
                    </template>
                </div>

                <!-- Actions -->
                <div class="mt-6 space-y-3">
                    <button type="button" 
                            @click="confirmOrderWithOtp()" 
                            :disabled="otpSubmitting || fullOtp.length !== 6" 
                            class="w-full py-3.5 rounded-xl btn-gold text-neutral-950 font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-lg">
                        <template x-if="!otpSubmitting">
                            <span>Confirm & Authorize Order</span>
                        </template>
                        <template x-if="otpSubmitting">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-neutral-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Verifying Authorization...</span>
                            </span>
                        </template>
                    </button>

                    <!-- Resend / Cooldown -->
                    <div class="text-center text-xs text-neutral-400">
                        <span x-show="timerSeconds > 0">
                            Resend code in <strong class="text-white font-mono" x-text="timerSeconds"></strong>s
                        </span>
                        <button type="button" 
                                x-show="timerSeconds === 0" 
                                @click="resendOrderOtp()" 
                                class="text-indigo-400 hover:text-indigo-300 font-semibold underline">
                            Resend 6-Digit Passcode
                        </button>
                    </div>

                    <!-- Cancel / Return -->
                    <button type="button" 
                            @click="otpModalOpen = false" 
                            class="w-full py-2 text-[11px] text-neutral-500 hover:text-neutral-300 text-center transition-colors">
                        Modify Delivery Information
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
