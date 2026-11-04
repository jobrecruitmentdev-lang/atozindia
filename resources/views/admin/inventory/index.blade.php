@extends('layouts.admin')

@section('page_title', 'Inventory & Ledger Control')
@section('page_subtitle', 'Live stock levels, safety thresholds, and immutable change audits')

@section('content')
<div class="space-y-8" x-data="{ 
    adjustModalOpen: false, 
    activeItem: { id: null, sku: '', productName: '', quantity: 0, low_stock_threshold: 5, notes: '' } 
}">
    
    <!-- Top Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative min-w-[260px] flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU or product name..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-black">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="flex items-center gap-1.5 bg-neutral-100 p-1 rounded-2xl text-xs font-semibold">
                    <a href="{{ route('admin.inventory.index') }}" class="px-3 py-1.5 rounded-xl {{ !request('filter') ? 'bg-white text-neutral-950 shadow-sm' : 'text-neutral-600 hover:text-black' }}">All Units</a>
                    <a href="{{ route('admin.inventory.index', ['filter' => 'low_stock']) }}" class="px-3 py-1.5 rounded-xl {{ request('filter') === 'low_stock' ? 'bg-amber-500 text-white shadow-sm' : 'text-neutral-600 hover:text-black' }}">Low Stock</a>
                    <a href="{{ route('admin.inventory.index', ['filter' => 'out_of_stock']) }}" class="px-3 py-1.5 rounded-xl {{ request('filter') === 'out_of_stock' ? 'bg-rose-500 text-white shadow-sm' : 'text-neutral-600 hover:text-black' }}">Out of Stock</a>
                </div>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Main Inventory Grid & Movement Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Inventory Stock Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Stock Holdings</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">{{ $inventories->total() }} tracked variants in inventory ledger.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Product & SKU</th>
                                <th class="p-4">Attributes</th>
                                <th class="p-4">Available</th>
                                <th class="p-4">Reserved</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Adjust</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($inventories as $inv)
                                @php
                                    $variant = $inv->variant;
                                    $product = $variant->product ?? null;
                                @endphp
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-neutral-900 truncate max-w-xs">{{ $product ? $product->name : 'N/A' }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono uppercase">{{ $variant->sku }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($variant->attributeValues as $val)
                                                <span class="px-2 py-0.5 rounded text-[10px] bg-neutral-100 text-neutral-700 font-medium">{{ $val->value }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-sm text-neutral-900">
                                        {{ $inv->quantity }}
                                    </td>
                                    <td class="p-4 font-mono text-neutral-500">
                                        {{ $inv->reserved_quantity }}
                                    </td>
                                    <td class="p-4">
                                        @if($inv->quantity <= 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Depleted</span>
                                        @elseif($inv->quantity <= $inv->low_stock_threshold)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Low ({{ $inv->quantity }})</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Optimal</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <button type="button" 
                                                @click="activeItem = {
                                                    id: {{ $inv->id }},
                                                    sku: '{{ $variant->sku }}',
                                                    productName: '{{ addslashes($product ? $product->name : '') }}',
                                                    quantity: {{ $inv->quantity }},
                                                    low_stock_threshold: {{ $inv->low_stock_threshold }},
                                                    notes: ''
                                                }; adjustModalOpen = true;"
                                                class="px-3 py-1.5 rounded-xl border border-neutral-300 hover:bg-neutral-900 hover:text-white transition-all text-[11px] font-semibold">
                                            Adjust Stock
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-neutral-400">No inventory matches found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($inventories->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $inventories->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Stock Ledger Movements (1 col) -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm h-fit space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">
                Immutable Ledger Audit
            </h3>
            <p class="text-xs text-neutral-500">Real-time stock adjustments, sales deductions, and returns.</p>

            <div class="divide-y divide-neutral-100 max-h-[500px] overflow-y-auto pr-1">
                @forelse($movements as $m)
                    <div class="py-3 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[10px] uppercase font-bold text-neutral-600">{{ $m->type }}</span>
                            <span class="font-mono text-[10px] text-neutral-400">{{ $m->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-[11px] font-semibold text-neutral-900 truncate">
                            {{ $m->inventory->variant->product->name ?? 'SKU: ' . ($m->inventory->variant->sku ?? 'N/A') }}
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-neutral-500">{{ $m->notes ?: 'Stock adjustment' }}</span>
                            <span class="font-mono font-bold {{ $m->change_amount >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $m->change_amount >= 0 ? '+' : '' }}{{ $m->change_amount }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-neutral-400 text-xs">No movements recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div x-show="adjustModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative" @click.outside="adjustModalOpen = false">
            <button type="button" @click="adjustModalOpen = false" class="absolute top-6 right-6 text-neutral-400 hover:text-black text-xl">&times;</button>
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 mb-1">Adjust Inventory Level</h3>
            <p class="text-xs text-neutral-500 mb-6 font-mono" x-text="'SKU: ' + activeItem.sku"></p>

            <form :action="'/admin/inventory/' + activeItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">New Quantity On Hand *</label>
                    <input type="number" name="quantity" x-model="activeItem.quantity" min="0" required class="w-full text-sm font-mono font-bold bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Low-Stock Alert Threshold *</label>
                    <input type="number" name="low_stock_threshold" x-model="activeItem.low_stock_threshold" min="0" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Adjustment Reason / Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Physical cycle count audit" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="adjustModalOpen = false" class="px-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-semibold text-neutral-600 hover:text-black">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">Record Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
