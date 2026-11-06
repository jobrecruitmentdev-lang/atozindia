@extends('layouts.admin')

@section('page_title', 'Create New Product')
@section('page_subtitle', 'Multi-color, multi-pose gallery product creation with dynamic size variants')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-16" 
     x-data="productFormController()" 
     x-init="initData()">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Catalogue</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 transition">
                Cancel
            </a>
            <button 
                type="button" 
                @click="submitForm()"
                class="px-8 py-2.5 rounded-full btn-gold text-neutral-950 text-xs font-black uppercase tracking-wider shadow-md transition active:scale-95"
            >
                Save & Publish Product
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
            <div class="flex items-center gap-2 font-bold mb-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please fix the following issues before continuing:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="productMainForm" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- Hidden input for total colors count -->
        <input type="hidden" name="color_count" :value="colors.length">

        <!-- LEFT MAIN COLUMN (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Identity & Description -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Step 1 &bull; Essential Information</span>
                    <h3 class="text-base font-bold text-neutral-950">Product Name & Narrative</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Title *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           placeholder="e.g. Pure Cotton Ankle Socks (Pack of 3) or Smart AI 4K Dashcam" 
                           class="w-full text-sm font-semibold bg-neutral-50 border border-neutral-200 rounded-2xl p-3.5 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                <!-- Short Summary & Description -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Short Summary (Card Preview)</label>
                        <textarea name="short_description" rows="2" placeholder="Quick 1-2 sentence highlight for catalog cards and search..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('short_description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Detailed Product Narrative / Description</label>
                        <textarea name="description" rows="5" placeholder="Full product story, materials, usage instructions, or package contents..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3.5 focus:outline-none focus:border-black">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pricing & Master SKU -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Step 2 &bull; Financials</span>
                    <h3 class="text-base font-bold text-neutral-950">Pricing & Base Identifiers</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Regular Price (₹) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-neutral-500">₹</span>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" required 
                                   placeholder="999.00" 
                                   class="w-full text-sm font-bold pl-8 bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-emerald-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Sale Price (₹)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-neutral-500">₹</span>
                            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" 
                                   placeholder="799.00" 
                                   class="w-full text-sm font-bold pl-8 bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-rose-700">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master Base SKU</label>
                        <input type="text" name="base_sku" value="{{ old('base_sku') }}" 
                               placeholder="Auto-generated if empty" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2 border-t border-neutral-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Barcode (UPC / EAN)</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" 
                               placeholder="e.g. 8901234567890" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Internal Cost Price (₹)</label>
                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" placeholder="Private" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">GST Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 18) }}" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    </div>
                </div>
            </div>

            <!-- Card 3: DYNAMIC MULTI-COLOUR, MULTI-POSE GALLERY & SIZE VARIANTS (Availsmart Style) -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-neutral-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Step 3 &bull; Visual Merchandising</span>
                        <h3 class="text-base font-bold text-neutral-950 flex items-center gap-2">
                            <span>Product Colours, Multi-Pose Galleries & Variants</span>
                        </h3>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            Upload 4, 5 or more photos per colour (front, back, angle, close-up poses) and set size variants with inventory stock.
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
                                    Upload Multi-Pose Photos for <span class="text-neutral-950 font-extrabold" x-text="color.name ? color.name : 'this colour'"></span>
                                </label>

                                <!-- Multi-File Drag & Drop Zone -->
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

            <!-- Card 4: Optional Advanced Details Accordion -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                <button type="button" @click="showAdvanced = !showAdvanced" class="w-full p-6 sm:p-8 flex items-center justify-between hover:bg-neutral-50/50 transition-colors text-left">
                    <div>
                        <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                            <span>Advanced Specifications & Shipping</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-neutral-100 text-neutral-600 font-normal">Optional</span>
                        </h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Physical dimensions, materials, technical specs, and SEO overrides.</p>
                    </div>
                    <div class="p-2 rounded-xl bg-neutral-100 text-neutral-700">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="showAdvanced ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>

                <div x-show="showAdvanced" x-collapse class="p-6 sm:p-8 pt-0 space-y-6 border-t border-neutral-100">
                    
                    <!-- Logistics -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Weight</label>
                            <div class="flex gap-2">
                                <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" placeholder="0.25" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                                <select name="weight_unit" class="w-20 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                                    <option value="kg">kg</option>
                                    <option value="g">g</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Dimensions (L × W × H)</label>
                            <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="e.g. 20 × 15 × 5 cm" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Primary Material</label>
                            <input type="text" name="material" value="{{ old('material') }}" placeholder="e.g. 100% Combed Cotton, Titanium" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Country of Origin</label>
                            <input type="text" name="country_of_origin" value="{{ old('country_of_origin', 'India') }}" placeholder="e.g. India, Japan" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Manufacturer / Brand</label>
                            <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" placeholder="e.g. ATOZ Private Limited" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>
                    </div>

                    <!-- Dynamic Specifications -->
                    <div class="border-t border-neutral-100 pt-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Key Specifications</label>
                            <button type="button" @click="specs.push({ key: '', value: '' })" class="text-xs font-bold text-neutral-900 hover:underline">
                                + Add Row
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(spec, idx) in specs" :key="idx">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="spec_keys[]" x-model="spec.key" placeholder="Feature (e.g. Fabric, Battery)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5">
                                    <input type="text" name="spec_values[]" x-model="spec.value" placeholder="Value (e.g. 100% Breathable, 40h)" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5">
                                    <button type="button" @click="specs.splice(idx, 1)" class="p-2 text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="border-t border-neutral-100 pt-6 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-700">Search Engine Optimization (SEO)</h4>
                        <div>
                            <label class="block text-xs font-bold text-neutral-600 mb-1.5">Meta Title</label>
                            <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Custom Google title (auto-generated if empty)" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-600 mb-1.5">Meta Description</label>
                            <textarea name="seo_description" rows="2" placeholder="Search snippet preview..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">{{ old('seo_description') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- RIGHT SIDEBAR COLUMN (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Publish Action Box -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-4 sticky top-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Publication Status</h3>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-600 mb-1.5">Storefront Visibility</label>
                    <select name="status" class="w-full text-xs font-semibold bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Immediately Visible)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private Staging)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <!-- Primary Submit Action -->
                <button type="submit" class="w-full py-4 rounded-2xl btn-gold text-neutral-950 text-xs font-black uppercase tracking-wider shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save & Publish Product</span>
                </button>
            </div>

            <!-- Categories / Department Selection Box -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900">Department & Category *</h3>
                    <span class="text-[10px] text-neutral-400">Select one or more</span>
                </div>

                <!-- Quick Category Search -->
                <div class="relative">
                    <input type="text" x-model="searchCat" placeholder="Filter departments..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl pl-8 pr-3 py-2 focus:outline-none focus:border-black">
                    <svg class="w-3.5 h-3.5 text-neutral-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Clean Scrollable Category Tree -->
                <div class="max-h-72 overflow-y-auto pr-1 space-y-3 divide-y divide-neutral-100 text-xs">
                    @foreach($rootCategories as $dept)
                        <div class="pt-2 first:pt-0" x-show="!searchCat || '{{ strtolower($dept->name) }}'.includes(searchCat.toLowerCase()) || {{ $dept->children->pluck('name')->map(fn($n) => strtolower($n))->toJson() }}.some(c => c.includes(searchCat.toLowerCase()))">
                            <label class="flex items-center gap-2 font-bold text-neutral-900 py-1 cursor-pointer hover:text-black">
                                <input type="checkbox" name="categories[]" value="{{ $dept->id }}" {{ in_array($dept->id, old('categories', [])) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                                <span>{{ $dept->name }}</span>
                            </label>

                            @if($dept->children->isNotEmpty())
                                <div class="ml-5 pl-2 border-l border-neutral-200 space-y-1.5 my-1">
                                    @foreach($dept->children as $sub)
                                        <label class="flex items-center gap-2 text-neutral-600 hover:text-neutral-950 py-0.5 cursor-pointer" x-show="!searchCat || '{{ strtolower($sub->name) }}'.includes(searchCat.toLowerCase())">
                                            <input type="checkbox" name="categories[]" value="{{ $sub->id }}" {{ in_array($sub->id, old('categories', [])) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                                            <span class="text-[11px]">{{ $sub->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Brand Selection -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Brand Affiliation</h3>
                <select name="brand_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                    <option value="">-- No Brand / Atelier House --</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Merchandising Badges -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-900 border-b border-neutral-100 pb-3">Merchandising Badges</h3>
                
                <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Featured Spotlight</span>
                        <span class="text-[10px] text-neutral-500">Surface prominently on homepage</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                    <input type="checkbox" name="is_bestseller" value="1" {{ old('is_bestseller') ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Bestseller Badge</span>
                        <span class="text-[10px] text-neutral-500">Flag as customer favourite</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/70 cursor-pointer hover:bg-neutral-100 transition-colors">
                    <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', 1) ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">New Arrival</span>
                        <span class="text-[10px] text-neutral-500">Include in new arrivals feed</span>
                    </div>
                </label>
            </div>

        </div>
    </form>
</div>

<script>
    function productFormController() {
        return {
            showAdvanced: false,
            searchCat: '',
            specs: [],
            colors: [],

            initData() {
                // Initialize Default Color Card (Availsmart style)
                this.colors.push({
                    id: null,
                    name: 'Obsidian Black',
                    hex: '#111110',
                    existing_images: [],
                    new_previews: [],
                    variants: [
                        { id: null, size: 'Free Size', sku: '', price: '', stock: 20 }
                    ]
                });
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
                if (this.colors[cIdx].variants.length > 1) {
                    this.colors[cIdx].variants.splice(vIdx, 1);
                }
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
