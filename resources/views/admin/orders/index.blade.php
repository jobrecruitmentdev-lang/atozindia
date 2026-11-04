@extends('layouts.admin')

@section('page_title', 'Order Fulfillment & Sales')
@section('page_subtitle', 'Review customer orders, dispatch status, invoices, and transaction audits')

@section('content')
<div class="space-y-6">
    
    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[260px] flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order #, email, phone..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-black">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <select name="order_status" class="text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-3 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Order Statuses</option>
                    <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('order_status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('order_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <select name="payment_status" class="text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-3 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Payment Statuses</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                Filter Orders
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Recent Orders ({{ $orders->total() }})</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Chronological ledger of customer purchases.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                        <tr>
                            <th class="p-4">Order #</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Total Amount</th>
                            <th class="p-4">Order Status</th>
                            <th class="p-4">Payment</th>
                            <th class="p-4">Fulfillment</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-neutral-50/50 transition-colors">
                                <td class="p-4 font-mono font-bold text-neutral-900">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:underline">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="p-4 text-neutral-500 font-mono text-[11px]">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="p-4">
                                    <div class="font-semibold text-neutral-900">{{ $order->customer_name }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">{{ $order->customer_email }}</div>
                                </td>
                                <td class="p-4 font-mono font-bold text-neutral-950">
                                    ₹{{ number_format($order->grand_total, 2) }}
                                </td>
                                <td class="p-4">
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                        $style = $statusStyles[$order->order_status] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $style }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-600' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider {{ $order->fulfillment_status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-600' }}">
                                        {{ $order->fulfillment_status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 rounded-xl border border-neutral-300 hover:bg-neutral-900 hover:text-white transition-all text-[11px] font-semibold">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-neutral-400">No orders match the filter criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-neutral-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
