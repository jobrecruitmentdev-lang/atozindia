@extends('layouts.app')

@section('title', 'Client Authentication — ATOZ GADGETZ')
@section('meta_description', 'Log in to your private atelier collector account.')

@section('content')
<div class="max-w-md mx-auto px-6 py-20 space-y-8">
    <div class="text-center space-y-2">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-950">Atelier Sign In</h1>
        <p class="text-xs text-neutral-500">Access your acquisitions, private wishlists, and shipping preferences.</p>
    </div>

    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Password</label>
                </div>
                <input type="password" name="password" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 text-xs text-neutral-700 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-neutral-300 text-black">
                    <span>Remember this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-colors shadow-lg">
                Authenticate
            </button>
        </form>

        <div class="border-t border-neutral-100 pt-4 text-center text-xs text-neutral-500">
            Don't have an atelier account? 
            <a href="{{ route('register', request('redirect') ? ['redirect' => request('redirect')] : []) }}" class="font-bold text-neutral-950 underline hover:text-neutral-600">Register now</a>
        </div>
    </div>
</div>
@endsection
