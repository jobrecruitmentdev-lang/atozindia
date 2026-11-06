@extends('layouts.admin')

@section('page_title', 'Edit Product: ' . $product->name)
@section('page_subtitle', 'SKU: ' . $product->base_sku . ' &bull; ' . ($product->is_variable ? 'Multi-Colour Variable Product' : 'Single Product'))

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-16" 
     x-data="productEditFormController({{ json_encode($productColorsData) }}, {{ json_encode(!empty($product->specifications_json) ? collect($product->specifications_json)->map(fn($v, $k) => ['key' => $k, 'val' => $v])->values() : []) }})"
     x-init="initData()">
    
    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 pb-2">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Catalogue</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="px-4 py-2.5 rounded-full text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors flex items-center gap-2 shadow-sm">
                <span>View on Storefront</span>
                <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <form method="POST" action="{{ route('admin.products.duplicate', $product->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-full text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors shadow-sm">
                    Duplicate as Draft
                </button>
            </form>
            <button 
                type="button" 
                @click="submitForm()"
                class="px-8 py-2.5 rounded-full btn-gold text-neutral-950 text-xs font-black uppercase tracking-wider shadow-md transition active:scale-95"
            >
                Save Product Changes
            </button>
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

    <form id="productMainForm" method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf
        @method('PUT')

        <!-- Hidden input for total colors count -->
        <input type="hidden" name="color_count" :value="colors.length">

        <!-- MAIN COLUMN (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card 1: Nomenclature & Identity -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Product Identity</h3>
                    <span class="text-xs font-mono text-neutral-400">Product ID: #{{ $product->id }}</span>
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
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master Base Stock Units</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $currentStock) }}" min="0" 
                               class="w-full text-sm font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black font-bold">
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

            <!-- Card 4: DYNAMIC MULTI-COLOUR, MULTI-POSE GALLERIES & SIZE VARIANTS (Availsmart Style) -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-neutral-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Visual Merchandising Studio</span>
                        <h3 class="text-base font-bold text-neutral-950 flex items-center gap-2">
                            <span>Product Colours, Multi-Pose Colour Galleries & Variants Matrix</span>
                        </h3>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            Manage multiple photos for each colour (different poses/angles) and configure size variants with dedicated stock.
                        </p>
                    </div>

                    <button 
                        type="button" 
                        @click="addColor()" 
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full btn-gold text-neutral-950 font-black text-xs shadow-md transition active:scale-95 shrink-0"
                    >
                        <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Add Colour Swatch</span>
                    </button>
                </div>

                <!-- Dynamic Colour Cards Container -->
                <div class="space-y-8">
                    <template x-for="(color, cIdx) in colors" :key="cIdx">
                        <div class="p-6 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-6 relative transition">
                            
                            <!-- Colour Card Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-200/60 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full border-2 border-white shadow-sm shrink-0" :style="'background-color: ' + color.hex"></div>
                                    <div>
                                        <h4 class="text-sm font-bold text-neutral-900" x-text="color.name ? color.name : 'Colour #' + (cIdx + 1)"></h4>
                                        <span class="text-[10px] text-neutral-400 font-mono" x-text="color.hex"></span>
                                    </div>
                                </div>

                                <!-- Colour Inputs & Delete Button -->
                                <div class="flex items-center gap-3">
                                    <input type="hidden" :name="'color_id_' + cIdx" :value="color.id">

                                    <!-- Colour Name -->
                                    <input 
                                        type="text" 
                                        :name="'color_name_' + cIdx" 
                                        x-model="color.name" 
                                        placeholder="Colour Name (e.g. Obsidian Black)"
                                        class="px-3.5 py-1.5 bg-white border border-neutral-200 rounded-xl text-xs text-neutral-900 focus:outline-none focus:border-black w-48 font-semibold shadow-sm"
                                        required
                                    >

                                    <!-- Native Color Picker & Hex Text -->
                                    <div class="flex items-center gap-1.5 bg-white border border-neutral-200 rounded-xl px-2 py-1 shadow-sm">
                                        <input 
                                            type="color" 
                                            x-model="color.hex" 
                                            class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0"
                                        >
                                        <input 
                                            type="text" 
                                            :name="'color_hex_' + cIdx" 
                                            x-model="color.hex" 
                                            class="w-20 text-[11px] font-mono text-neutral-800 focus:outline-none uppercase"
                                        >
                                    </div>

                                    <!-- Remove Colour Button -->
                                    <button 
                                        type="button" 
                                        @click="removeColor(cIdx)" 
                                        x-show="colors.length > 1"
                                        class="p-2 text-neutral-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition" 
                                        title="Delete this colour"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- PART A: Multi-Pose Images Uploader for THIS colour -->
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider">
                                    Photos for <span class="text-neutral-950 font-extrabold" x-text="color.name ? color.name : 'this colour'"></span>
                                </label>

                                <!-- Existing Saved Photos in DB -->
                                <template x-if="color.existing_images && color.existing_images.length > 0">
                                    <div>
                                        <p class="text-[11px] text-neutral-500 mb-2">Saved Photos in Database (Hover to set Primary or delete):</p>
                                        <div class="flex flex-wrap gap-3">
                                            <template x-for="img in color.existing_images" :key="img.id">
                                                <div class="relative w-24 h-24 rounded-2xl bg-white border border-neutral-200 p-1 flex flex-col items-center justify-center overflow-hidden group shadow-sm">
                                                    <img :src="img.url" class="w-full h-full object-cover rounded-xl">
                                                    
                                                    <template x-if="img.is_primary">
                                                        <span class="absolute top-1.5 left-1.5 bg-[#D4AF37] text-neutral-950 text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-sm">
                                                            Primary
                                                        </span>
                                                    </template>

                                                    <div class="absolute inset-0 bg-[#0f0e0b]/85 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 p-1 text-white">
                                                        <label class="flex items-center gap-1 text-[9px] cursor-pointer hover:underline text-[#D4AF37]">
                                                            <input 
                                                                type="radio" 
                                                                :name="'existing_primary_' + (color.id || cIdx)" 
                                                                :value="img.id" 
                                                                :checked="img.is_primary"
                                                                class="text-[#D4AF37] focus:ring-[#D4AF37]"
                                                            >
                                                            <span>Primary</span>
                                                        </label>

                                                        <label class="flex items-center gap-1 text-[9px] text-rose-300 cursor-pointer hover:underline">
                                                            <input type="checkbox" name="delete_images[]" :value="img.id">
                                                            <span>Delete</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Multi-File Drag & Drop Zone for New Photos -->
                                <div class="border-2 border-dashed border-neutral-300 hover:border-[#D4AF37] rounded-2xl p-4 bg-white text-center cursor-pointer transition">
                                    <input 
                                        type="file" 
                                        :name="'color_images_' + cIdx + '[]'" 
                                        multiple 
                                        accept="image/*"
                                        @change="previewImages($event, cIdx)"
                                        class="w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:btn-gold file:text-neutral-950 cursor-pointer"
                                    >
                                    <p class="text-[11px] text-neutral-400 mt-1">Select 4, 5 or more photos in different angles/poses for this colour swatch.</p>
                                </div>

                                <!-- Client-Side Instant Previews of newly selected files -->
                                <template x-if="color.new_previews && color.new_previews.length > 0">
                                    <div>
                                        <p class="text-[11px] font-semibold text-emerald-700 mt-2">New Photos Queued for Upload:</p>
                                        <div class="flex flex-wrap gap-3 mt-1.5">
                                            <template x-for="(prev, pIdx) in color.new_previews" :key="pIdx">
                                                <div class="relative w-20 h-20 rounded-xl bg-white border border-neutral-200 p-1 overflow-hidden shadow-sm">
                                                    <img :src="prev.url" class="w-full h-full object-cover rounded-lg">
                                                    <label class="absolute bottom-1 left-1 right-1 bg-black/75 text-white text-[8px] rounded px-1 py-0.5 text-center cursor-pointer flex items-center justify-center gap-1">
                                                        <input type="radio" :name="'primary_image_' + cIdx" :value="pIdx" :checked="pIdx === 0">
                                                        <span>Primary</span>
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- PART B: Size Variants Table under THIS Colour -->
                            <div class="space-y-3 pt-4 border-t border-neutral-200/70">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider">
                                        Size Variants & Inventory Stock for <span x-text="color.name"></span>
                                    </label>
                                    <button 
                                        type="button" 
                                        @click="addVariant(cIdx)" 
                                        class="text-xs text-neutral-900 font-bold hover:underline flex items-center gap-1"
                                    >
                                        <span>+ Add Size Variant</span>
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <template x-for="(variant, vIdx) in color.variants" :key="vIdx">
                                        <div class="p-3 bg-white rounded-xl border border-neutral-200 flex flex-wrap sm:flex-nowrap items-center gap-3 shadow-sm">
                                            <input type="hidden" :name="'variant_id_' + cIdx + '[]'" :value="variant.id">

                                            <div class="w-full sm:w-36">
                                                <label class="block text-[10px] text-neutral-400 uppercase font-bold mb-1">Size (e.g. Free Size, S, M, L)</label>
                                                <input 
                                                    type="text" 
                                                    :name="'variant_size_' + cIdx + '[]'" 
                                                    x-model="variant.size" 
                                                    placeholder="e.g. Free Size, M"
                                                    class="w-full px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs text-neutral-900 focus:outline-none focus:border-black font-semibold"
                                                >
                                            </div>

                                            <div class="w-full sm:flex-1">
                                                <label class="block text-[10px] text-neutral-400 uppercase font-bold mb-1">Variant SKU</label>
                                                <input 
                                                    type="text" 
                                                    :name="'variant_sku_' + cIdx + '[]'" 
                                                    x-model="variant.sku" 
                                                    placeholder="Auto-generated if blank"
                                                    class="w-full px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-mono text-neutral-900 focus:outline-none focus:border-black uppercase"
                                                >
                                            </div>

                                            <div class="w-full sm:w-28">
                                                <label class="block text-[10px] text-neutral-400 uppercase font-bold mb-1">Price Override (₹)</label>
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    :name="'variant_price_' + cIdx + '[]'" 
                                                    x-model="variant.price" 
                                                    placeholder="Base price"
                                                    class="w-full px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs text-neutral-900 focus:outline-none focus:border-black font-mono"
                                                >
                                            </div>

                                            <div class="w-full sm:w-24">
                                                <label class="block text-[10px] text-neutral-400 uppercase font-bold mb-1">Stock *</label>
                                                <input 
                                                    type="number" 
                                                    min="0" 
                                                    :name="'variant_stock_' + cIdx + '[]'" 
                                                    x-model="variant.stock" 
                                                    class="w-full px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-mono font-bold text-neutral-900 focus:outline-none focus:border-black"
                                                    required
                                                >
                                            </div>

                                            <div class="pt-5 shrink-0">
                                                <button 
                                                    type="button" 
                                                    @click="removeVariant(cIdx, vIdx, variant.id)" 
                                                    x-show="color.variants.length > 1"
                                                    class="p-1.5 text-neutral-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" 
                                                    title="Delete this size variant"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <!-- Card 5: Optional Advanced Accordion (Logistics, Specs, SEO) -->
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
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-4 sticky top-6 z-10">
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

                <button type="submit" class="w-full py-4 rounded-2xl btn-gold text-neutral-950 text-xs font-black uppercase tracking-wider shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
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

<script>
    function productEditFormController(initialColors = [], initialSpecs = []) {
        return {
            showAdvanced: false,
            searchCat: '',
            specs: initialSpecs,
            colors: initialColors,

            initData() {
                // If no colors pre-loaded, add default
                if (!this.colors || this.colors.length === 0) {
                    this.colors = [{
                        id: null,
                        name: 'Obsidian Black',
                        hex: '#111110',
                        existing_images: [],
                        new_previews: [],
                        variants: [
                            { id: null, size: 'Free Size', sku: '', price: '', stock: 20 }
                        ]
                    }];
                }
            },

            addColor() {
                const nextNum = this.colors.length + 1;
                this.colors.push({
                    id: null,
                    name: 'Colour #' + nextNum,
                    hex: '#374151',
                    existing_images: [],
                    new_previews: [],
                    variants: [
                        { id: null, size: 'Free Size', sku: '', price: '', stock: 20 }
                    ]
                });
            },

            removeColor(cIdx) {
                if (this.colors.length > 1) {
                    this.colors.splice(cIdx, 1);
                }
            },

            addVariant(cIdx) {
                this.colors[cIdx].variants.push({
                    id: null,
                    size: '',
                    sku: '',
                    price: '',
                    stock: 10
                });
            },

            removeVariant(cIdx, vIdx, variantId) {
                if (variantId) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'delete_variants[]';
                    hiddenInput.value = variantId;
                    document.getElementById('productMainForm').appendChild(hiddenInput);
                }
                this.colors[cIdx].variants.splice(vIdx, 1);
            },

            previewImages(event, cIdx) {
                const files = event.target.files;
                this.colors[cIdx].new_previews = [];
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.colors[cIdx].new_previews.push({
                            url: e.target.result,
                            name: files[i].name
                        });
                    };
                    reader.readAsDataURL(files[i]);
                }
            },

            submitForm() {
                const form = document.getElementById('productMainForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                form.submit();
            }
        };
    }
</script>
@endsection
