@extends('layouts.app')

@section('title', 'Verify Atelier Email — ATOZ GADGETZ')
@section('meta_description', 'Authenticate your collector email address to activate your ATOZ GADGETZ profile.')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-[32px] border border-neutral-200/80 p-8 sm:p-12 shadow-sm space-y-8"
         x-data="otpVerification()">
        
        <!-- Atelier Brand Monogram -->
        <div class="text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] mx-auto flex items-center justify-center font-black tracking-widest text-xs uppercase shadow-md">
                ATOZ
            </div>
            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#99772C] block font-mono">Security Gateway</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-950">Verify Your Identity</h1>
            <p class="text-xs sm:text-sm text-neutral-500 max-w-sm mx-auto leading-relaxed">
                We have dispatched a 6-digit authentication passcode to<br>
                <strong class="font-mono text-neutral-900">{{ session('pending_registration.email') }}</strong>
            </p>
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium text-center">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs text-center font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        @if(app()->environment('local') && session('registration_otp'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-center space-y-2 shadow-sm">
                <div class="flex items-center justify-center gap-1.5 font-bold uppercase tracking-wider text-[10px] text-amber-800">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Local Dev Mode &bull; Passcode Generated</span>
                </div>
                <div class="font-mono text-2xl font-black tracking-[0.3em] text-neutral-950">
                    {{ session('registration_otp') }}
                </div>
                <button type="button" 
                        @click="autoFill('{{ session('registration_otp') }}')" 
                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-200/60 hover:bg-amber-200 text-[11px] font-bold text-amber-900 transition-colors">
                    <span>Auto-Fill 6 Digits</span>
                    <span>&darr;</span>
                </button>
            </div>
        @endif

        <!-- OTP Submission Form -->
        <form method="POST" action="{{ route('register.otp.verify') }}" class="space-y-6" @submit="submitOtp($event)">
            @csrf

            <!-- 6 Auto-Advancing Digit Boxes -->
            <div>
                <label class="block text-center text-xs font-bold uppercase tracking-wider text-neutral-700 mb-4">
                    Enter 6-Digit Passcode
                </label>
                
                <div class="flex items-center justify-center gap-2 sm:gap-3" @paste="handlePaste($event)">
                    <template x-for="(digit, index) in digits" :key="index">
                        <input type="text" 
                               maxlength="1" 
                               inputmode="numeric"
                               pattern="[0-9]*"
                               autocomplete="one-time-code"
                               class="w-11 h-14 sm:w-12 sm:h-16 text-center text-2xl font-mono font-extrabold rounded-2xl border border-neutral-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all shadow-sm bg-neutral-50/50"
                               :id="'otp-input-' + index"
                               x-model="digits[index]"
                               @input="handleInput(index, $event)"
                               @keydown.backspace="handleBackspace(index, $event)"
                               @focus="$event.target.select()">
                    </template>
                </div>

                <!-- Combined hidden input for standard submission -->
                <input type="hidden" name="otp" :value="fullOtp">
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="fullOtp.length !== 6 || loading"
                    :class="fullOtp.length === 6 && !loading ? 'btn-gold text-neutral-950 font-black shadow-xl' : 'bg-neutral-200 text-neutral-400 cursor-not-allowed'"
                    class="w-full py-4 rounded-full font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                <span x-show="!loading">Authenticate & Activate Account &rarr;</span>
                <span x-show="loading" x-cloak>Verifying Passcode...</span>
            </button>
        </form>

        <!-- Resend Passcode Timer & Action -->
        <div class="pt-4 border-t border-neutral-100 flex flex-col items-center justify-center gap-3 text-xs">
            <div class="text-neutral-500 font-mono" x-show="countdown > 0">
                Resend code available in: <strong class="text-neutral-900" x-text="formatTime(countdown)"></strong>
            </div>

            <form method="POST" action="{{ route('register.otp.resend') }}" x-show="countdown <= 0" x-cloak>
                @csrf
                <button type="submit" class="font-bold text-neutral-900 hover:text-black uppercase tracking-wider text-xs underline underline-offset-4 transition-colors">
                    Resend 6-Digit Code Now
                </button>
            </form>

            <a href="{{ route('register') }}" class="text-[11px] text-neutral-400 hover:text-neutral-700 transition-colors pt-2">
                &larr; Register with a different email address
            </a>
        </div>

    </div>
</div>

<script>
    function otpVerification() {
        return {
            digits: ['', '', '', '', '', ''],
            loading: false,
            countdown: 60,
            init() {
                // Focus first input automatically
                this.$nextTick(() => {
                    const first = document.getElementById('otp-input-0');
                    if (first) first.focus();
                });

                // Start 60-second cooldown timer
                const timer = setInterval(() => {
                    if (this.countdown > 0) {
                        this.countdown--;
                    } else {
                        clearInterval(timer);
                    }
                }, 1000);
            },
            get fullOtp() {
                return this.digits.join('');
            },
            autoFill(code) {
                const arr = String(code).split('');
                for (let i = 0; i < 6; i++) {
                    this.digits[i] = arr[i] || '';
                }
                this.$nextTick(() => {
                    const lastInput = document.getElementById('otp-input-5');
                    if (lastInput) lastInput.focus();
                });
            },
            handleInput(index, event) {
                const val = event.target.value.replace(/[^0-9]/g, '');
                this.digits[index] = val ? val[val.length - 1] : '';

                if (this.digits[index] && index < 5) {
                    const nextInput = document.getElementById('otp-input-' + (index + 1));
                    if (nextInput) nextInput.focus();
                }
            },
            handleBackspace(index, event) {
                if (!this.digits[index] && index > 0) {
                    const prevInput = document.getElementById('otp-input-' + (index - 1));
                    if (prevInput) {
                        prevInput.focus();
                        this.digits[index - 1] = '';
                    }
                }
            },
            handlePaste(event) {
                event.preventDefault();
                const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                if (!pasteData) return;

                for (let i = 0; i < 6 && i < pasteData.length; i++) {
                    this.digits[i] = pasteData[i];
                }

                const targetIndex = Math.min(pasteData.length, 5);
                const nextInput = document.getElementById('otp-input-' + targetIndex);
                if (nextInput) nextInput.focus();
            },
            submitOtp(event) {
                if (this.fullOtp.length === 6) {
                    this.loading = true;
                } else {
                    event.preventDefault();
                }
            },
            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            }
        };
    }
</script>
@endsection
