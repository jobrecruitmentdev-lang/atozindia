@extends('layouts.app')

@section('title', 'Private Wishlist — ATOZ GADGETZ')
@section('meta_description', 'Your curated wishlist of acoustic and precision desk architecture.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-10">

    <!-- Header -->
    <div class="border-b border-neutral-200 pb-6 flex items-center justify-between">
        <div>
            <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-2">
                <a href="{{ route('home') }}" class="hover:text-black">Home</a>
                <span>/</span>
                <a href="{{ route('customer.dashboard') }}" class="hover:text-black">Account</a>
                <span>/</span>
                <span class="text-neutral-900 font-bold">Wishlist</span>
            </nav>
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-950">Private Collection Wishlist</h1>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Overview</a>
        <a href="{{ route('customer.orders') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Orders</a>
        <a href="{{ route('customer.addresses') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Address Book</a>
        <a href="{{ route('customer.wishlist') }}" class="px-5 py-2.5 rounded-full bg-neutral-950 text-white shadow-sm">Private Wishlist ({{ $wishlist->items->count() }})</a>
    </div>

    @if($wishlist->items->isEmpty())
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-12 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-neutral-100 text-neutral-400 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h2 class="text-base font-bold text-neutral-950">Your Wishlist is Empty</h2>
            <p class="text-xs text-neutral-500 max-w-sm mx-auto">Click the heart icon on any product in the catalogue to save it to your private atelier wishlist.</p>
            <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2.5 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                Explore Catalogue
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($wishlist->items as $item)
                @if($item->product)
                    @include('components.product-card', ['product' => $item->product])
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection
