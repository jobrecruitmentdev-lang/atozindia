@extends('layouts.admin')

@section('page_title', 'Product Catalogue')
@section('page_subtitle', 'Curate, modify, and manage luxury products and multi-colour variants')

@section('content')
<div class="space-y-6">
    
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-neutral-950 uppercase tracking-tight">All Products ({{ $products->total() }})</h2>
            <p class="text-xs text-neutral-500">Simple and variable products with multi-colour image support</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-neutral-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider px-5 py-3 rounded-full shadow transition-all flex items-center justify-center gap-2 self-start sm:self-auto">
            <span>+ Create New Product</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, or barcode..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-4 py-2.5 focus:outline-none focus:border-black">
            </div>

            <div>
                <select name="status" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-3 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div>
                <select name="brand_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-3 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Brands</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-neutral-900 hover:bg-black text-white text-xs font-bold uppercase rounded-2xl px-4 py-2.5 transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'brand_id', 'category_id']))
                <a href="{{ route('admin.products.index') }}" class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-2xl px-3 py-2.5 flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-neutral-50/70 border-b border-neutral-100 text-[10px] font-bold text-neutral-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Product</th>
                        <th class="py-3.5 px-4">Brand</th>
                        <th class="py-3.5 px-4">Price</th>
                        <th class="py-3.5 px-4">Variants</th>
                        <th class="py-3.5 px-4">Stock</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($products as $p)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ $p->primary_image ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100' }}" 
                                     alt="{{ $p->name }}" 
                                     class="w-12 h-12 object-cover rounded-xl bg-neutral-100 border border-neutral-200/60 shrink-0">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="font-bold text-neutral-900 hover:underline block truncate max-w-xs">
                                        {{ $p->name }}
                                    </a>
                                    <span class="text-[10px] text-neutral-400 font-mono">SKU: {{ $p->base_sku }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 font-medium text-neutral-700">
                            {{ $p->brand?->name ?: '—' }}
                        </td>

                        <td class="py-4 px-4 font-bold text-neutral-950">
                            ₹{{ number_format($p->effective_price, 2) }}
                            @if($p->sale_price && $p->sale_price < $p->price)
                            <span class="block text-[10px] text-neutral-400 line-through">₹{{ number_format($p->price, 2) }}</span>
                            @endif
                        </td>

                        <td class="py-4 px-4">
                            @if($p->is_variable)
                                <span class="bg-neutral-100 text-neutral-800 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                    {{ $p->variants->count() }} Variants
                                </span>
                            @else
                                <span class="text-neutral-400 text-[11px]">Simple</span>
                            @endif
                        </td>

                        <td class="py-4 px-4">
                            @php
                                $totalStock = $p->variants->sum(fn($v) => $v->inventory?->quantity ?? 0);
                            @endphp
                            <span class="font-bold {{ $totalStock <= 5 ? 'text-amber-600' : 'text-neutral-900' }}">
                                {{ $totalStock }}
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            @if($p->status === 'active')
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Active</span>
                            @elseif($p->status === 'draft')
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Draft</span>
                            @else
                                <span class="bg-neutral-100 text-neutral-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Archived</span>
                            @endif
                        </td>

                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('product.show', $p->slug) }}" target="_blank" class="p-1.5 text-neutral-400 hover:text-black rounded-lg hover:bg-neutral-100" title="View in Storefront">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <form method="POST" action="{{ route('admin.products.duplicate', $p->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-neutral-400 hover:text-black rounded-lg hover:bg-neutral-100" title="Duplicate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </form>

                                <a href="{{ route('admin.products.edit', $p->id) }}" class="p-1.5 text-neutral-900 hover:text-black rounded-lg hover:bg-neutral-100 font-semibold" title="Edit">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" onsubmit="return confirm('Move this product to trash?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-xs text-neutral-400">
                            No products matching criteria found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-6 border-t border-neutral-100">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
