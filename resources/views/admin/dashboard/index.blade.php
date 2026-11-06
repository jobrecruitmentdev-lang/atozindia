@extends('layouts.admin')

@section('page_title', 'Operational Overview')
@section('page_subtitle', 'Real-time sales, inventory telemetry, and customer insights')

@section('content')
<div class="space-y-8">
    
    <!-- Top KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Revenue Card -->
        <div class="bg-white rounded-3xl p-6 border border-neutral-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-neutral-500">
                <span class="text-xs font-bold uppercase tracking-wider">Gross Revenue</span>
                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">₹</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">
                    ₹{{ number_format($totalRevenue, 2) }}
                </div>
                <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <span>&uarr; Live Transaction Engine</span>
                </p>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-3xl p-6 border border-neutral-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-neutral-500">
                <span class="text-xs font-bold uppercase tracking-wider">Total Orders</span>
                <span class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">
                    {{ $totalOrders }}
                </div>
                <div class="flex items-center gap-2 text-[11px] text-neutral-500 mt-1">
                    <span class="text-amber-600 font-semibold">{{ $pendingOrders }} pending</span>
                    <span>&bull;</span>
                    <span class="text-emerald-600 font-semibold">{{ $completedOrders }} completed</span>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-white rounded-3xl p-6 border border-neutral-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-neutral-500">
                <span class="text-xs font-bold uppercase tracking-wider">Active Catalogue</span>
                <span class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">
                    {{ $activeProducts }}
                </div>
                <div class="flex items-center gap-2 text-[11px] text-neutral-500 mt-1">
                    <span>{{ $totalCategories }} categories</span>
                    <span>&bull;</span>
                    <span>{{ $totalBrands }} brands</span>
                </div>
            </div>
        </div>

        <!-- Stock Health Card -->
        <div class="bg-white rounded-3xl p-6 border border-neutral-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-neutral-500">
                <span class="text-xs font-bold uppercase tracking-wider">Inventory Health</span>
                <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">
                    {{ $lowStockCount }}
                </div>
                <div class="flex items-center gap-2 text-[11px] text-neutral-500 mt-1">
                    <span class="text-amber-600 font-semibold">{{ $lowStockCount }} low stock</span>
                    <span>&bull;</span>
                    <span class="text-rose-600 font-semibold">{{ $outOfStockCount }} depleted</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-[#0f0e0b] border border-[#D4AF37]/30 text-white rounded-3xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
        <div>
            <h3 class="text-sm font-black uppercase tracking-wider text-[#D4AF37]">Quick Dispatch & Catalogue Management</h3>
            <p class="text-xs text-neutral-400 mt-0.5">Rapidly add products, calibrate inventory levels, or review order queues.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.products.create') }}" class="btn-gold text-neutral-950 text-xs font-black uppercase px-5 py-2.5 rounded-full shadow-md transition-all">
                + Add Product
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="bg-[#1A1917] hover:bg-[#D4AF37]/20 hover:text-[#D4AF37] text-neutral-300 text-xs font-bold uppercase px-4 py-2.5 rounded-full border border-[#D4AF37]/30 transition-all">
                Adjust Stock
            </a>
            <a href="{{ route('admin.orders.index') }}" class="bg-[#1A1917] hover:bg-[#D4AF37]/20 hover:text-[#D4AF37] text-neutral-300 text-xs font-bold uppercase px-4 py-2.5 rounded-full border border-[#D4AF37]/30 transition-all">
                View Orders
            </a>
        </div>
    </div>

    <!-- Split Section: Recent Orders & Stock Alert -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Orders (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Recent Orders</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Latest transactions from customer storefront</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-neutral-900 hover:underline">View All &rarr;</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="py-12 text-center text-xs text-neutral-400">
                    No orders have been placed yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-neutral-100 text-neutral-400 uppercase text-[10px] tracking-wider">
                                <th class="pb-3">Order Ref</th>
                                <th class="pb-3">Client</th>
                                <th class="pb-3">Items</th>
                                <th class="pb-3">Total</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-neutral-50/50">
                                <td class="py-3.5 font-bold text-neutral-950">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5">
                                    <div class="font-medium text-neutral-900">{{ $order->customer_email }}</div>
                                    <div class="text-[10px] text-neutral-400">{{ $order->customer_phone }}</div>
                                </td>
                                <td class="py-3.5 text-neutral-600">{{ $order->items->count() }} item(s)</td>
                                <td class="py-3.5 font-bold text-neutral-950">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3.5">
                                    @if($order->order_status === 'pending')
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase">Pending</span>
                                    @elseif($order->order_status === 'delivered')
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase">Delivered</span>
                                    @else
                                        <span class="bg-neutral-100 text-neutral-800 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase">{{ $order->order_status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-semibold text-black hover:underline">Inspect</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Stock Depletion Warning (1 Column) -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Inventory Alerts</h3>
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full uppercase">Attention</span>
                </div>
                <p class="text-xs text-neutral-500 mb-4">Variants nearing or below threshold limits:</p>

                <div class="space-y-3">
                    @forelse($lowStockProducts as $variant)
                    <div class="p-3 rounded-2xl bg-neutral-50 border border-neutral-100 flex items-center justify-between text-xs">
                        <div class="min-w-0 pr-2">
                            <p class="font-bold text-neutral-900 truncate">{{ $variant->product->name }}</p>
                            <p class="text-[10px] text-neutral-500">SKU: {{ $variant->sku }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-extrabold text-sm {{ $variant->inventory->quantity <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                {{ $variant->inventory->quantity }}
                            </span>
                            <span class="text-[10px] text-neutral-400 block">in stock</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-neutral-400 py-6 text-center">All variants are sufficiently stocked.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-neutral-100">
                <a href="{{ route('admin.inventory.index') }}" class="block text-center text-xs font-bold text-neutral-900 hover:underline">
                    Manage Full Inventory Ledger &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
