@extends('layouts.admin')

@section('page_title', 'Edit Product: ' . $product->name)
@section('page_subtitle', 'SKU: ' . $product->base_sku . ' &bull; ' . ($product->is_variable ? 'Variable Product' : 'Simple Product'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    activeTab: 'details',
    showAdvanced: false,
    searchCat: '',
    activeColorFilter: 'all',
    specs: {{ json_encode(!empty($product->specifications_json) ? collect($product->specifications_json)->map(fn($v, $k) => ['key' => $k, 'val' => $v])->values() : [['key' => '', 'val' => '']]) }}
}">
    
    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 pb-2">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Catalogue</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors flex items-center gap-2 shadow-sm">
                <span>View on Storefront</span>
                <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <form method="POST" action="{{ route('admin.products.duplicate', $product->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors shadow-sm">
                    Duplicate as Draft
                </button>
            </form>
        </div>
    </div>

    <!-- Error Messages Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please fix the following issues:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Streamlined Tab Navigation (Only 3 Intuitive Views) -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 p-1.5 shadow-sm flex flex-wrap gap-1 sticky top-4 z-20 backdrop-blur-md bg-white/95">
        <button type="button" @click="activeTab = 'details'" 
                :class="activeTab === 'details' ? 'bg-neutral-900 text-white font-bold shadow' : 'text-neutral-600 hover:text-black'" 
                class="px-5 py-2.5 rounded-xl text-xs transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>1. Product Details & Photos</span>
        </button>

        <button type="button" @click="activeTab = 'variants'" 
                :class="activeTab === 'variants' ? 'bg-neutral-900 text-white font-bold shadow ring-2 ring-emerald-500' : 'text-neutral-600 hover:text-black'" 
                class="px-5 py-2.5 rounded-xl text-xs transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            <span>2. Variants Matrix</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-neutral-200 text-neutral-800 font-mono font-bold">{{ $product->variants->count() }}</span>
        </button>

        <button type="button" @click="activeTab = 'galleries'" 
                :class="activeTab === 'galleries' ? 'bg-neutral-900 text-white font-bold shadow' : 'text-neutral-600 hover:text-black'" 
                class="px-5 py-2.5 rounded-xl text-xs transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>3. Colour Galleries</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-neutral-200 text-neutral-800 font-mono font-bold">{{ $product->images->count() }}</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: PRODUCT DETAILS & PHOTOS (UNIFIED) -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'details'">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" id="productMainForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf
            @method('PUT')

            <!-- MAIN COLUMN (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card 1: Nomenclature & Identity -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Product Identity</h3>
                        <span class="text-xs font-mono text-neutral-400">ID: #{{ $product->id }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                               class="w-full text-sm font-semibold bg-neutral-50 border border-neutral-200 rounded-2xl p-3.5 focus:outline-none focus:border-black focus:bg-white transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master Base SKU *</label>
                            <input type="text" name="base_sku" value="{{ old('base_sku', $product->base_sku) }}" required 
                                   class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Barcode (UPC / EAN)</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" 
                                   placeholder="e.g. 8905440857577"
                                   class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pricing & Inventory -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="border-b border-neutral-100 pb-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Financials & Inventory</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Control pricing, discounts, and real-time stock directly.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Regular Price (₹) *</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-neutral-500">₹</span>
                                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required 
                                       class="w-full text-sm font-bold pl-8 bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-emerald-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Sale Price (₹)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-neutral-500">₹</span>
                                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" 
                                       placeholder="Leave blank if none"
                                       class="w-full text-sm font-bold pl-8 bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-rose-700">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Available Stock Units</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $currentStock) }}" min="0" 
                                   class="w-full text-sm font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black font-bold">
                            <span class="text-[10px] text-neutral-400 mt-1 block">
                                @if($product->is_variable)
                                    Base pool (Variant matrix manages individual items)
                                @else
                                    Immediate stock available for checkout
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-neutral-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Internal Cost Price (₹)</label>
                            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" placeholder="Admin only" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">GST Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 18) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>
                    </div>
                </div>

                <!-- Card 3: Narrative & Editorial Copy -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Product Story & Narrative</h3>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Editorial Summary (Short Description)</label>
                        <textarea name="short_description" rows="2" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Comprehensive Narrative (Full Description)</label>
                        <textarea name="description" rows="5" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3.5 focus:outline-none focus:border-black">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <!-- Card 4: Optional Advanced Accordion (Logistics, Specs, SEO) -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="w-full p-6 sm:p-8 flex items-center justify-between hover:bg-neutral-50/50 transition-colors text-left">
                        <div>
                            <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                <span>Advanced Logistics, Specifications & SEO</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-neutral-100 text-neutral-600 font-normal">Optional</span>
                            </h3>
                            <p class="text-xs text-neutral-500 mt-0.5">Physical dimensions, crafting materials, custom specs, and Google meta tags.</p>
                        </div>
                        <div class="p-2 rounded-xl bg-neutral-100 text-neutral-700">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="showAdvanced ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-show="showAdvanced" x-collapse class="p-6 sm:p-8 pt-0 space-y-6 border-t border-neutral-100">
                        
                        <!-- Logistics Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Weight</label>
                                <div class="flex gap-2">
                                    <input type="number" step="0.01" name="weight" value="{{ old('weight', $product->weight) }}" placeholder="0.25" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                                    <input type="text" name="weight_unit" value="{{ old('weight_unit', $product->weight_unit ?? 'g') }}" class="w-20 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Dimensions (L × W × H)</label>
                                <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" placeholder="e.g. 180 x 170 x 70 mm" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Crafting Material</label>
                                <input type="text" name="material" value="{{ old('material', $product->material) }}" placeholder="e.g. 100% Combed Cotton, Titanium" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Manufacturer</label>
                                <input type="text" name="manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Country of Origin</label>
                                <input type="text" name="country_of_origin" value="{{ old('country_of_origin', $product->country_of_origin) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            </div>
                        </div>

                        <!-- Technical Specs -->
                        <div class="border-t border-neutral-100 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Technical Specifications</label>
                                <button type="button" @click="specs.push({key: '', val: ''})" class="text-xs font-bold text-neutral-900 hover:underline flex items-center gap-1">
                                    + Add Specification Pair
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in specs" :key="index">
                                    <div class="flex items-center gap-3">
                                        <input type="text" name="spec_keys[]" x-model="item.key" placeholder="Feature (e.g. Battery Life, Fabric)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                                        <input type="text" name="spec_values[]" x-model="item.val" placeholder="Value (e.g. 40 Hours, 100% Cotton)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                                        <button type="button" @click="specs.splice(index, 1)" class="p-2 text-rose-500 hover:text-rose-700">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- SEO Metadata -->
                        <div class="border-t border-neutral-100 pt-6 space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-700">Search Engine Optimization</h4>
                            <div>
                                <label class="block text-xs font-bold text-neutral-600 mb-1.5">SEO Title</label>
                                <input type="text" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" placeholder="Custom meta title for Google search..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-600 mb-1.5">SEO Meta Description</label>
                                <textarea name="seo_description" rows="2" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">{{ old('seo_description', $product->seo_description) }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- SIDEBAR COLUMN (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Sticky Save Changes Box -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-4 sticky top-20 z-10">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Publication Status</h3>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-600 mb-1.5">Visibility Status</label>
                        <select name="status" class="w-full text-xs font-semibold bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                            <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Visible on Storefront)</option>
                            <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Private staging)</option>
                            <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-600 mb-1.5">Catalogue Architecture</label>
                        <select name="is_variable" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                            <option value="0" {{ !$product->is_variable ? 'selected' : '' }}>Simple Product (Single standard item)</option>
                            <option value="1" {{ $product->is_variable ? 'selected' : '' }}>Variable Product (Colours, Sizes, Finishes)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl bg-neutral-950 hover:bg-black text-white text-xs font-bold uppercase tracking-wider shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Product Changes</span>
                    </button>
                </div>

                <!-- Departments & Categories -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900">Department & Category *</h3>
                        <span class="text-[10px] text-neutral-400">Select applicable</span>
                    </div>

                    <!-- Category Filter -->
                    <div class="relative">
                        <input type="text" x-model="searchCat" placeholder="Filter departments..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl pl-8 pr-3 py-2 focus:outline-none focus:border-black">
                        <svg class="w-3.5 h-3.5 text-neutral-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <!-- Scrollable Category Tree -->
                    <div class="max-h-72 overflow-y-auto pr-1 space-y-3 divide-y divide-neutral-100 text-xs">
                        @foreach($rootCategories as $dept)
                            <div class="pt-2 first:pt-0" x-show="!searchCat || '{{ strtolower($dept->name) }}'.includes(searchCat.toLowerCase()) || {{ $dept->children->pluck('name')->map(fn($n) => strtolower($n))->toJson() }}.some(c => c.includes(searchCat.toLowerCase()))">
                                <label class="flex items-center gap-2 font-bold text-neutral-900 py-1 cursor-pointer hover:text-black">
                                    <input type="checkbox" name="categories[]" value="{{ $dept->id }}" {{ $product->categories->contains($dept->id) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                                    <span>{{ $dept->name }}</span>
                                </label>

                                @if($dept->children->isNotEmpty())
                                    <div class="ml-5 pl-2 border-l border-neutral-200 space-y-1.5 my-1">
                                        @foreach($dept->children as $sub)
                                            <label class="flex items-center gap-2 text-neutral-600 hover:text-neutral-950 py-0.5 cursor-pointer" x-show="!searchCat || '{{ strtolower($sub->name) }}'.includes(searchCat.toLowerCase())">
                                                <input type="checkbox" name="categories[]" value="{{ $sub->id }}" {{ $product->categories->contains($sub->id) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                                                <span class="text-[11px]">{{ $sub->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Brand Affiliation -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Brand Affiliation</h3>
                    <select name="brand_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                        <option value="">-- Independent / No Brand --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Merchandising Badges -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Merchandising Badges</h3>
                    
                    <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Featured</span>
                            <span class="text-[10px] text-neutral-500">Surface on homepage spotlight</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                        <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">Bestseller</span>
                            <span class="text-[10px] text-neutral-500">Add bestseller badge</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                        <input type="checkbox" name="is_new_arrival" value="1" {{ $product->is_new_arrival ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                        <div>
                            <span class="text-xs font-bold text-neutral-900 block">New Arrival</span>
                            <span class="text-[10px] text-neutral-500">Show in new releases tab</span>
                        </div>
                    </label>
                </div>

            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: SMART VARIANTS MATRIX -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'variants'" class="space-y-8">
        
        @if(!$product->is_variable)
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wider">Simple Product Mode Active</h4>
                        <p class="text-xs text-amber-800 mt-0.5">This product uses a single stock pool ({{ $currentStock }} units). If you want to sell multiple sizes or colours, construct your first variant combination below.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Variant Combination Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm">
            <div class="border-b border-neutral-100 pb-4 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Construct New Variant Combination</h3>
                <p class="text-xs text-neutral-500 mt-1">Select only the attributes that apply to this item (e.g. Size only for socks, or Colour + Size for apparel). Leave irrelevant options empty.</p>
            </div>

            <form method="POST" action="{{ route('admin.products.variant.store', $product->id) }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($attributes as $attr)
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">
                                <span>{{ $attr->name }}</span>
                                <span class="text-[10px] text-neutral-400 font-normal lowercase">(optional)</span>
                            </label>
                            <select name="attribute_values[]" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                                <option value="">-- No {{ $attr->name }} --</option>
                                @foreach($attr->values as $v)
                                    <option value="{{ $v->id }}">{{ $v->value }} @if($v->code_or_hex)({{ $v->code_or_hex }})@endif</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-neutral-400 mt-1 block">
                                @if($attr->code === 'storage')
                                    Leave blank for apparel, footwear & non-tech items
                                @elseif($attr->code === 'size')
                                    Choose clothing size (S, M, L) or footwear size
                                @else
                                    Choose colour if applicable
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-4 border-t border-neutral-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Variant SKU</label>
                        <input type="text" name="sku" placeholder="Auto-generated if empty" class="w-full text-xs font-mono uppercase bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Price Override (₹)</label>
                        <input type="number" step="0.01" name="price_override" placeholder="Leave empty for base (₹{{ number_format($product->price, 2) }})" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Sale Override (₹)</label>
                        <input type="number" step="0.01" name="sale_price_override" placeholder="Optional promo price" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Initial Stock *</label>
                        <input type="number" name="stock_quantity" value="10" min="0" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                        + Add Variant Combination
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Variants Table -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Variant Matrix ({{ $product->variants->count() }})</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Manage live stock, pricing overrides, and availability for each combination.</p>
                </div>
            </div>

            @if($product->variants->isEmpty())
                <div class="p-8 text-center text-neutral-400">
                    <p class="text-sm">No variant combinations added yet. Construct your first combination above.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">SKU / Identifiers</th>
                                <th class="p-4">Attributes</th>
                                <th class="p-4">Price</th>
                                <th class="p-4">Sale Price</th>
                                <th class="p-4">Stock</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($product->variants as $variant)
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <form method="POST" action="{{ route('admin.products.variant.update', $variant->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <td class="p-4">
                                            <input type="text" name="sku" value="{{ $variant->sku }}" required class="text-xs font-mono font-bold bg-transparent border-b border-dashed border-neutral-300 focus:outline-none focus:border-black uppercase">
                                        </td>
                                        <td class="p-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse($variant->attributeValues as $val)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-medium bg-neutral-100 text-neutral-800">
                                                        @if($val->color_hex)
                                                            <span class="w-2 h-2 rounded-full border border-neutral-300" style="background-color: {{ $val->color_hex }}"></span>
                                                        @endif
                                                        <span>{{ $val->value }}</span>
                                                    </span>
                                                @empty
                                                    <span class="text-neutral-400 italic text-[11px]">Default Variant</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <input type="number" step="0.01" name="price_override" value="{{ $variant->price_override }}" placeholder="₹{{ number_format($product->price, 2) }}" class="w-24 text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-lg p-1.5 focus:outline-none focus:border-black">
                                        </td>
                                        <td class="p-4">
                                            <input type="number" step="0.01" name="sale_price_override" value="{{ $variant->sale_price_override }}" placeholder="--" class="w-24 text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-lg p-1.5 focus:outline-none focus:border-black text-rose-600">
                                        </td>
                                        <td class="p-4">
                                            <input type="number" name="stock_quantity" value="{{ $variant->inventory->quantity ?? 0 }}" min="0" required class="w-20 text-xs font-mono font-bold bg-neutral-50 border border-neutral-200 rounded-lg p-1.5 focus:outline-none focus:border-black">
                                        </td>
                                        <td class="p-4">
                                            <select name="status" class="text-xs bg-neutral-50 border border-neutral-200 rounded-lg p-1.5 focus:outline-none">
                                                <option value="active" {{ $variant->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $variant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="submit" class="px-3 py-1 rounded-lg bg-neutral-900 text-white text-[11px] font-semibold hover:bg-neutral-800 transition-colors">
                                                    Update
                                                </button>
                                    </form>
                                                <form method="POST" action="{{ route('admin.products.variant.delete', $variant->id) }}" onsubmit="return confirm('Remove variant {{ $variant->sku }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-rose-500 hover:text-rose-700" title="Delete variant">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: COLOUR SWATCH STUDIO -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'galleries'" class="space-y-8">
        
        <!-- Upload Box -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-4 mb-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Upload to Photo Gallery</h3>
                    <p class="text-xs text-neutral-500 mt-1">Add product photos and optionally map them to colour swatches for instant storefront color switching.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.image.upload', $product->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Select High-Resolution Image *</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-xs text-neutral-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-neutral-900 file:text-white hover:file:bg-neutral-800 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Associated Colour Swatch</label>
                    <select name="attribute_value_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        <option value="">Global / All Colours</option>
                        @php
                            $colorAttr = $attributes->firstWhere('code', 'color') ?? $attributes->firstWhere('name', 'Colour');
                        @endphp
                        @if($colorAttr)
                            @foreach($colorAttr->values as $val)
                                <option value="{{ $val->id }}">{{ $val->value }} @if($val->color_hex)({{ $val->color_hex }})@endif</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2 mb-3 cursor-pointer text-xs font-medium text-neutral-700">
                        <input type="checkbox" name="is_primary" value="1" class="rounded border-neutral-300 text-black">
                        <span>Set as Primary Thumbnail</span>
                    </label>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-neutral-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                        Upload Image
                    </button>
                </div>
            </form>
        </div>

        <!-- Gallery Showcase & Management -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-100 pb-4">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Gallery Items</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Total {{ $product->images->count() }} photos registered for this product.</p>
                </div>
                <!-- Filter by colour -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-neutral-500 font-medium">Filter view:</span>
                    <select x-model="activeColorFilter" class="text-xs bg-neutral-50 border border-neutral-200 rounded-xl px-3 py-1.5 focus:outline-none">
                        <option value="all">All Photos ({{ $product->images->count() }})</option>
                        <option value="global">Global Only</option>
                        @if($colorAttr)
                            @foreach($colorAttr->values as $val)
                                <option value="{{ $val->id }}">{{ $val->value }} Gallery</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            @if($product->images->isEmpty())
                <div class="py-12 text-center text-neutral-400">
                    <p class="text-sm">No images uploaded yet. Use the upload box above to add photos.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach($product->images as $img)
                        <div x-show="activeColorFilter === 'all' || (activeColorFilter === 'global' && {{ $img->attribute_value_id ? 'false' : 'true' }}) || (activeColorFilter == '{{ $img->attribute_value_id }}')" 
                             class="group relative bg-neutral-50 rounded-2xl border border-neutral-200/80 overflow-hidden flex flex-col">
                            <div class="aspect-square w-full overflow-hidden bg-neutral-100 relative">
                                <img src="{{ $img->image_path }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @if($img->is_primary)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-neutral-950 text-white uppercase tracking-wider shadow">Primary</span>
                                @endif
                            </div>
                            
                            <div class="p-2.5 flex-1 flex flex-col justify-between gap-2">
                                <div class="text-[11px] font-medium text-neutral-700 truncate">
                                    @if($img->colorValue)
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-full border border-neutral-300 shrink-0" style="background-color: {{ $img->colorValue->color_hex ?? '#000' }}"></span>
                                            <span class="truncate">{{ $img->colorValue->value }}</span>
                                        </div>
                                    @else
                                        <span class="text-neutral-400 italic">Global</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between border-t border-neutral-100 pt-2 gap-1">
                                    @if(!$img->is_primary)
                                        <form method="POST" action="{{ route('admin.products.image.primary', $img->id) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full text-[10px] text-neutral-600 hover:text-black font-semibold text-left">
                                                Set Primary
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.products.image.delete', $img->id) }}" onsubmit="return confirm('Remove this image from gallery?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] text-rose-500 hover:text-rose-700 font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
