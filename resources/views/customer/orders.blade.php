@extends('layouts.app')

@section('title', 'My Atelier Orders — ATOZ GADGETZ')
@section('meta_description', 'Chronological history of all acquisitions and delivery dockets.')

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
                <span class="text-neutral-900 font-bold">Orders</span>
            </nav>
            <h1 class="text-3xl font-extrabold tracking-tight text-neutral-950">Acquisition History</h1>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="flex flex-wrap gap-2 text-xs font-bold">
        <a href="{{ route('customer.dashboard') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Overview</a>
        <a href="{{ route('customer.orders') }}" class="px-5 py-2.5 rounded-full bg-neutral-950 text-white shadow-sm">Orders ({{ $orders->total() }})</a>
        <a href="{{ route('customer.addresses') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Address Book</a>
        <a href="{{ route('customer.wishlist') }}" class="px-5 py-2.5 rounded-full bg-white border border-neutral-200 text-neutral-700 hover:border-black">Private Wishlist</a>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-neutral-50 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                    <tr>
                        <th class="p-4">Order #</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Instruments</th>
                        <th class="p-4">Total Settled</th>
                        <th class="p-4">Order Status</th>
                        <th class="p-4">Payment</th>
                        <th class="p-4 text-right">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="p-4 font-mono font-bold text-neutral-950">
                                <a href="{{ route('customer.orders.show', $o->order_number) }}" class="hover:underline">
                                    #{{ $o->order_number }}
                                </a>
                            </td>
                            <td class="p-4 text-neutral-500 font-mono">{{ $o->created_at->format('M d, Y') }}</td>
                            <td class="p-4 text-neutral-800">{{ $o->items->count() }} items</td>
                            <td class="p-4 font-mono font-bold text-neutral-950">₹{{ number_format($o->grand_total, 2) }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-neutral-100 text-neutral-800">
                                    {{ $o->order_status }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider {{ $o->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-800' : 'bg-neutral-100 text-neutral-700' }}">
                                    {{ $o->payment_status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('customer.orders.show', $o->order_number) }}" class="px-3 py-1.5 rounded-xl border border-neutral-300 hover:bg-neutral-950 hover:text-white transition-colors text-[11px] font-semibold">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-neutral-400">You have no order records in this account.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-neutral-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
