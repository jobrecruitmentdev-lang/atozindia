@extends('layouts.app')

@section('title', 'Atelier Account Dashboard — ATOZ GADGETZ')
@section('meta_description', 'Manage your private atelier profile, orders, and addresses.')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12 space-y-10">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-200 pb-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400">Atelier Membership</span>
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-950">Welcome, {{ $user->name }}</h1>
            <p class="text-xs text-neutral-500 font-mono mt-0.5">{{ $user->email }} &bull; Member since {{ $user->created_at->format('M Y') }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-bold text-neutral-800 transition-colors">
                Sign Out
            </button>
        </form>
    </div>

    <!-- Navigation Pills -->
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-full bg-neutral-950 text-white shadow-sm">Overview</a>
        <a href="{{ route('customer.orders') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Orders ({{ $user->orders->count() }})</a>
        <a href="{{ route('customer.addresses') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Address Book ({{ $user->addresses->count() }})</a>
        <a href="{{ route('customer.wishlist') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Private Wishlist</a>
    </div>

    <!-- KPI / Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="p-6 rounded-3xl bg-white border border-neutral-200/80 shadow-sm space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Total Acquisitions</span>
            <div class="text-2xl font-mono font-extrabold text-neutral-950">{{ $user->orders->count() }} Orders</div>
            <a href="{{ route('customer.orders') }}" class="text-[11px] font-semibold text-neutral-900 underline hover:text-neutral-500 block pt-1">Inspect all orders &rarr;</a>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-neutral-200/80 shadow-sm space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Primary Delivery Address</span>
            @php $defAddr = $user->addresses->firstWhere('is_default_shipping', true) ?? $user->addresses->first(); @endphp
            <div class="text-xs text-neutral-800 font-medium truncate">
                {{ $defAddr ? $defAddr->address_line1 . ', ' . $defAddr->city : 'None configured' }}
            </div>
            <a href="{{ route('customer.addresses') }}" class="text-[11px] font-semibold text-neutral-900 underline hover:text-neutral-500 block pt-1">Manage addresses &rarr;</a>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-neutral-200/80 shadow-sm space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Atelier Concierge</span>
            <div class="text-xs text-neutral-800 font-medium">VIP Support Line Active</div>
            <span class="text-[11px] text-neutral-400 block pt-1 font-mono">concierge@atozgadgetz.com</span>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden space-y-4">
        <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Recent Atelier Acquisitions</h3>
            <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-neutral-900 underline hover:text-neutral-500">View All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-neutral-50 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                    <tr>
                        <th class="p-4">Order #</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($user->orders as $o)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="p-4 font-mono font-bold text-neutral-950">#{{ $o->order_number }}</td>
                            <td class="p-4 text-neutral-500 font-mono">{{ $o->created_at->format('M d, Y') }}</td>
                            <td class="p-4 font-mono font-bold text-neutral-950">₹{{ number_format($o->grand_total, 2) }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-neutral-100 text-neutral-800">
                                    {{ $o->order_status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('customer.orders.show', $o->order_number) }}" class="text-[11px] font-semibold text-neutral-900 underline hover:text-neutral-500">
                                    Inspect &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-neutral-400">No orders placed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Profile Edit Form -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6 max-w-xl">
        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Update Personal Details</h3>

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
            </div>

            <div class="border-t border-neutral-100 pt-4 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-900 block">Change Password (Optional)</span>
                <div>
                    <input type="password" name="current_password" placeholder="Current password" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black mb-2">
                    <input type="password" name="new_password" placeholder="New password (min 8 chars)" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black mb-2">
                    <input type="password" name="new_password_confirmation" placeholder="Confirm new password" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                Save Profile Updates
            </button>
        </form>
    </div>

</div>
@endsection
