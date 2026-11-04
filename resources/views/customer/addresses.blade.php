@extends('layouts.app')

@section('title', 'Address Book — ATOZ GADGETZ')
@section('meta_description', 'Manage saved shipping and billing destinations.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-10" x-data="{ addModalOpen: false }">

    <!-- Header -->
    <div class="border-b border-neutral-200 pb-6 flex items-center justify-between">
        <div>
            <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-2">
                <a href="{{ route('home') }}" class="hover:text-black">Home</a>
                <span>/</span>
                <a href="{{ route('customer.dashboard') }}" class="hover:text-black">Account</a>
                <span>/</span>
                <span class="text-neutral-900 font-bold">Address Book</span>
            </nav>
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-950">Shipping & Billing Addresses</h1>
        </div>

        <button type="button" @click="addModalOpen = true" class="px-5 py-2.5 rounded-full bg-neutral-950 text-white text-xs font-bold uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
            + Add New Address
        </button>
    </div>

    <!-- Navigation Pills -->
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Overview</a>
        <a href="{{ route('customer.orders') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Orders</a>
        <a href="{{ route('customer.addresses') }}" class="px-5 py-2.5 rounded-full bg-neutral-950 text-white shadow-sm">Address Book ({{ $addresses->count() }})</a>
        <a href="{{ route('customer.wishlist') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Private Wishlist</a>
    </div>

    <!-- Addresses Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($addresses as $addr)
            <div class="p-6 rounded-3xl bg-white border border-neutral-200/80 shadow-sm flex flex-col justify-between space-y-4 relative">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-700">
                            {{ $addr->type }}
                        </span>
                        @if($addr->is_default_shipping)
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Default Shipping</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-sm text-neutral-950 mb-1">{{ $addr->full_name }}</h3>
                    <div class="text-xs text-neutral-600 leading-relaxed">
                        {{ $addr->address_line1 }}<br>
                        @if($addr->address_line2) {{ $addr->address_line2 }}<br> @endif
                        {{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}<br>
                        {{ $addr->country }}<br>
                        Phone: {{ $addr->phone }}
                    </div>
                </div>

                <div class="border-t border-neutral-100 pt-3 flex justify-end">
                    <form method="POST" action="{{ route('customer.address.delete', $addr->id) }}" onsubmit="return confirm('Remove address?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold">
                            Delete Address
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="sm:col-span-3 p-12 bg-white rounded-3xl border border-neutral-200 text-center text-neutral-400 text-xs">
                No addresses stored yet. Click "+ Add New Address" above.
            </div>
        @endforelse
    </div>

    <!-- Add Address Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative" @click.outside="addModalOpen = false">
            <button type="button" @click="addModalOpen = false" class="absolute top-6 right-6 text-neutral-400 hover:text-black text-2xl">&times;</button>
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 mb-6">New Delivery Destination</h3>

            <form method="POST" action="{{ route('customer.address.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">First Name *</label>
                        <input type="text" name="first_name" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Address Type *</label>
                    <select name="type" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        <option value="shipping">Shipping Address</option>
                        <option value="billing">Billing Address</option>
                        <option value="both">Both Shipping & Billing</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Street Address *</label>
                    <input type="text" name="address_line_1" required placeholder="House / Flat / Street" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Apartment / Landmark</label>
                    <input type="text" name="address_line_2" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">City *</label>
                        <input type="text" name="city" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">State *</label>
                        <input type="text" name="state" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Postal Code *</label>
                        <input type="text" name="postal_code" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Phone Number *</label>
                    <input type="tel" name="phone" required placeholder="+91 ..." class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="pt-2 space-y-2">
                    <label class="flex items-center gap-2 text-xs text-neutral-800 cursor-pointer">
                        <input type="checkbox" name="is_default_shipping" value="1" class="rounded border-neutral-300 text-black">
                        <span>Set as default shipping address</span>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-semibold text-neutral-600 hover:text-black">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">Save Address</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
