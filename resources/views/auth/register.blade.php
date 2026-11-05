@extends('layouts.app')

@section('title', 'Client Registration — ATOZ GADGETZ')
@section('meta_description', 'Create your private atelier collector profile.')

@section('content')
<div class="max-w-md mx-auto px-6 py-20 space-y-8">
    <div class="text-center space-y-2">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-950">Join the Atelier</h1>
        <p class="text-xs text-neutral-500">Create an account to track dispatches, retain preferences, and receive private allocations.</p>
    </div>

    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Password *</label>
                <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-colors shadow-lg mt-2 flex items-center justify-center gap-2">
                <span>Continue to 6-Digit Email Verification</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
            <p class="text-[10px] text-neutral-400 text-center">A 6-digit cryptographic security code will be dispatched to your email for authentication.</p>
        </form>

        <div class="border-t border-neutral-100 pt-4 text-center text-xs text-neutral-500">
            Already registered? 
            <a href="{{ route('login', request('redirect') ? ['redirect' => request('redirect')] : []) }}" class="font-bold text-neutral-950 underline hover:text-neutral-600">Sign in</a>
        </div>
    </div>
</div>
@endsection
