@extends('layouts.admin')

@section('page_title', 'Client Profile: ' . $customer->name)
@section('page_subtitle', 'Member since ' . $customer->created_at->format('M d, Y') . ' &bull; ' . $customer->email)

@section('content')
<div class="space-y-8">
    
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1">
            &larr; Back to Directory
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Customer Summary Card -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm h-fit space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-neutral-900 text-white font-bold flex items-center justify-center text-xl">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-neutral-950 text-base">{{ $customer->name }}</h3>
                    <p class="text-xs text-neutral-400 font-mono">Customer ID #{{ $customer->id }}</p>
                </div>
            </div>

            <div class="border-t border-neutral-100 pt-4 space-y-3 text-xs">
                <div>
                    <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Email</span>
                    <a href="mailto:{{ $customer->email }}" class="font-mono text-neutral-800 hover:underline">{{ $customer->email }}</a>
                </div>

                <div>
                    <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Phone</span>
                    <span class="font-mono text-neutral-800">{{ $customer->phone ?: 'Not provided' }}</span>
                </div>

                <div>
                    <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Lifetime Orders</span>
                    <span class="font-mono font-bold text-neutral-950 text-sm">{{ $customer->orders->count() }} Orders</span>
                </div>

                <div>
                    <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Total Spent</span>
                    <span class="font-mono font-bold text-emerald-800 text-sm">₹{{ number_format($customer->orders->where('order_status', '!=', 'cancelled')->sum('grand_total'), 2) }}</span>
                </div>
            </div>

            <!-- Saved Addresses -->
            <div class="border-t border-neutral-100 pt-4 space-y-3">
                <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Saved Addresses ({{ $customer->addresses->count() }})</span>
                @forelse($customer->addresses as $addr)
                    <div class="p-3 bg-neutral-50 rounded-2xl border border-neutral-200 text-xs text-neutral-700 leading-relaxed">
                        <div class="font-bold text-neutral-900 mb-0.5">{{ $addr->full_name }} ({{ $addr->type }})</div>
                        {{ $addr->address_line1 }}<br>
                        @if($addr->address_line2) {{ $addr->address_line2 }}<br> @endif
                        {{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}<br>
                        Phone: {{ $addr->phone }}
                    </div>
                @empty
                    <div class="text-neutral-400 text-xs italic">No stored addresses.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Order History & Activity (2 cols) -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Customer Order History</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">All purchases made under this user account.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Order #</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Items</th>
                                <th class="p-4">Total</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">View</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($customer->orders as $o)
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4 font-mono font-bold text-neutral-900">
                                        #{{ $o->order_number }}
                                    </td>
                                    <td class="p-4 text-neutral-500 font-mono text-[11px]">
                                        {{ $o->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-4 text-neutral-700">
                                        {{ $o->items->count() }} items
                                    </td>
                                    <td class="p-4 font-mono font-bold text-neutral-950">
                                        ₹{{ number_format($o->grand_total, 2) }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-neutral-100 text-neutral-800">
                                            {{ $o->order_status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('admin.orders.show', $o->id) }}" class="text-[11px] font-semibold text-neutral-900 hover:underline">
                                            Inspect &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-neutral-400">Customer has not placed any orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
