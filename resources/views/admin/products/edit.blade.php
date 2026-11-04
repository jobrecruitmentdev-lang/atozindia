@extends('layouts.admin')

@section('page_title', 'Edit Product: ' . $product->name)
@section('page_subtitle', 'SKU: ' . $product->base_sku . ' &bull; Created ' . $product->created_at->format('M d, Y'))

@section('content')
<div class="max-w-6xl space-y-8" x-data="{ 
    tab: 'general',
    activeColorFilter: 'all',
    specs: {{ json_encode(!empty($product->specifications_json) ? collect($product->specifications_json)->map(fn($v, $k) => ['key' => $k, 'val' => $v])->values() : [['key' => '', 'val' => '']]) }}
}">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1">
            &larr; Back to Catalogue
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors flex items-center gap-2">
                <span>View on Storefront</span>
                <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <form method="POST" action="{{ route('admin.products.duplicate', $product->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors">
                    Duplicate as Draft
                </button>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 p-1.5 shadow-sm flex flex-wrap gap-1 sticky top-20 z-10 backdrop-blur-md bg-white/95">
        <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">1. General</button>
        <button type="button" @click="tab = 'pricing'" :class="tab === 'pricing' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">2. Pricing & Tax</button>
        <button type="button" @click="tab = 'story'" :class="tab === 'story' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">3. Story & Specs</button>
        <button type="button" @click="tab = 'affiliations'" :class="tab === 'affiliations' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">4. Categories & Brand</button>
        <button type="button" @click="tab = 'galleries'" :class="tab === 'galleries' ? 'bg-neutral-900 text-white font-bold ring-2 ring-emerald-500' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors flex items-center gap-1.5">
            <span>5. Colour Galleries</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-neutral-200 text-neutral-800 font-mono">{{ $product->images->count() }}</span>
        </button>
        <button type="button" @click="tab = 'variants'" :class="tab === 'variants' ? 'bg-neutral-900 text-white font-bold ring-2 ring-emerald-500' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors flex items-center gap-1.5">
            <span>6. Variants Matrix</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-neutral-200 text-neutral-800 font-mono">{{ $product->variants->count() }}</span>
        </button>
        <button type="button" @click="tab = 'shipping'" :class="tab === 'shipping' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">7. Logistics & Dimensions</button>
        <button type="button" @click="tab = 'seo'" :class="tab === 'seo' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-3.5 py-2 rounded-xl text-xs transition-colors">8. SEO Metadata</button>
    </div>

    <!-- MAIN PRODUCT DETAILS FORM (Tabs 1, 2, 3, 4, 7, 8) -->
    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" id="productMainForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- TAB 1: GENERAL -->
        <div x-show="tab === 'general'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Identity & Nomenclature</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master Base SKU *</label>
                    <input type="text" name="base_sku" value="{{ old('base_sku', $product->base_sku) }}" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Barcode (UPC / EAN)</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Catalog Architecture</label>
                    <select name="is_variable" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="0" {{ !$product->is_variable ? 'selected' : '' }}>Simple Product (Single standard item)</option>
                        <option value="1" {{ $product->is_variable ? 'selected' : '' }}>Variable Product (Multiple colours, sizes, finishes)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Status *</label>
                    <select name="status" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Visible on Storefront)</option>
                        <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft (Private staging)</option>
                        <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Merchandising Toggles -->
            <div class="border-t border-neutral-100 pt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Featured</span>
                        <span class="text-[10px] text-neutral-500">Surface on homepage spotlight</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Bestseller</span>
                        <span class="text-[10px] text-neutral-500">Add bestseller badge</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_new_arrival" value="1" {{ $product->is_new_arrival ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">New Arrival</span>
                        <span class="text-[10px] text-neutral-500">Show in new releases tab</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- TAB 2: PRICING -->
        <div x-show="tab === 'pricing'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Financial Economics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Base Price (₹) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-emerald-800 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Sale Price (₹)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-rose-700 font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Cost Price (₹) (Private)</label>
                    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-neutral-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 18) }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>
            </div>
        </div>

        <!-- TAB 3: STORY & SPECS -->
        <div x-show="tab === 'story'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Editorial Copy & Specifications</h3>
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Editorial Summary (Short Description)</label>
                <textarea name="short_description" rows="3" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('short_description', $product->short_description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Comprehensive Narrative (Full Description)</label>
                <textarea name="description" rows="7" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Specifications Key-Value Builder -->
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
                            <input type="text" name="spec_keys[]" x-model="item.key" placeholder="Feature / Spec (e.g. Battery Life)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <input type="text" name="spec_values[]" x-model="item.val" placeholder="Value (e.g. Up to 40 Hours)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <button type="button" @click="specs.splice(index, 1)" class="p-2 text-rose-500 hover:text-rose-700">
                                &times;
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- TAB 4: CATEGORIES & BRAND -->
        <div x-show="tab === 'affiliations'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Catalogue Classification</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Brand</label>
                    <select name="brand_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="">-- Independent Atelier / No Brand --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Categories * (Select at least one)</label>
                    <div class="max-h-56 overflow-y-auto p-3 bg-neutral-50 border border-neutral-200 rounded-2xl space-y-2">
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2.5 text-xs text-neutral-800 cursor-pointer">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ $product->categories->contains($category->id) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 7: SHIPPING & LOGISTICS -->
        <div x-show="tab === 'shipping'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Physical Characteristics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Weight</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Weight Unit</label>
                    <input type="text" name="weight_unit" value="{{ old('weight_unit', $product->weight_unit ?? 'g') }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Dimensions (L x W x H)</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" placeholder="e.g. 180 x 170 x 70 mm" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Crafting Material</label>
                    <input type="text" name="material" value="{{ old('material', $product->material) }}" placeholder="e.g. Anodized Aluminum, Lambskin" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $product->manufacturer) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Country of Origin</label>
                    <input type="text" name="country_of_origin" value="{{ old('country_of_origin', $product->country_of_origin) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>
            </div>
        </div>

        <!-- TAB 8: SEO METADATA -->
        <div x-show="tab === 'seo'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Search Engine Optimization</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">SEO Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" placeholder="e.g. Aether One ANC Wireless Headphones | ATOZ GADGETZ" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">SEO Meta Description</label>
                    <textarea name="seo_description" rows="3" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('seo_description', $product->seo_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sticky Save Button for Product Info -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-all shadow-md">
                Save Product Changes
            </button>
        </div>
    </form>

    <!-- TAB 5: MULTI-COLOUR GALLERIES MANAGER (CRITICAL REQUIREMENT) -->
    <div x-show="tab === 'galleries'" class="space-y-8">
        
        <!-- Upload Box -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-4 mb-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Upload to Colour Gallery</h3>
                    <p class="text-xs text-neutral-500 mt-1">Assign each photo to a specific colour swatch so switching colours on the storefront dynamically swaps the entire gallery.</p>
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
                        <option value="">Global / Unassigned</option>
                        @php
                            $colorAttr = $attributes->firstWhere('slug', 'color') ?? $attributes->firstWhere('name', 'Color');
                        @endphp
                        @if($colorAttr)
                            @foreach($colorAttr->values as $val)
                                <option value="{{ $val->id }}">{{ $val->value }} ({{ $val->code_or_hex }})</option>
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
                        Upload to Gallery
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
                        <option value="all">All Colours ({{ $product->images->count() }})</option>
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
                    <p class="text-sm">No images have been uploaded yet. Upload images above.</p>
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
                                            <span class="w-2.5 h-2.5 rounded-full border border-neutral-300 shrink-0" style="background-color: {{ $img->colorValue->code_or_hex ?? '#000' }}"></span>
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

    <!-- TAB 6: VARIANTS MATRIX & BUILDER (CRITICAL REQUIREMENT) -->
    <div x-show="tab === 'variants'" class="space-y-8">
        
        <!-- Add Variant Combination Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm">
            <div class="border-b border-neutral-100 pb-4 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Construct New Variant Combination</h3>
                <p class="text-xs text-neutral-500 mt-1">Combine attributes (e.g. Colour + Size + Finish) with independent SKU, pricing, and dedicated inventory control.</p>
            </div>

            <form method="POST" action="{{ route('admin.products.variant.store', $product->id) }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($attributes as $attr)
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">{{ $attr->name }} *</label>
                            <select name="attribute_values[]" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                                <option value="">-- Choose {{ $attr->name }} --</option>
                                @foreach($attr->values as $v)
                                    <option value="{{ $v->id }}">{{ $v->value }} @if($v->code_or_hex)({{ $v->code_or_hex }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-4 border-t border-neutral-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Variant SKU *</label>
                        <input type="text" name="sku" required placeholder="e.g. {{ $product->base_sku }}-BLK-1TB" class="w-full text-xs font-mono uppercase bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
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
                                                @foreach($variant->attributeValues as $val)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-medium bg-neutral-100 text-neutral-800">
                                                        @if($val->code_or_hex)
                                                            <span class="w-2 h-2 rounded-full border border-neutral-300" style="background-color: {{ $val->code_or_hex }}"></span>
                                                        @endif
                                                        <span>{{ $val->value }}</span>
                                                    </span>
                                                @endforeach
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
</div>
@endsection
