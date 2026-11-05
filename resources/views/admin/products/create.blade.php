@extends('layouts.admin')

@section('page_title', 'Create New Product')
@section('page_subtitle', 'Fast product listing — fill the essentials to launch or expand with advanced specs.')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    isVariable: false,
    showAdvanced: false,
    imagePreview: null,
    searchCat: '',
    specs: [{ key: '', value: '' }],
    previewFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.imagePreview = URL.createObjectURL(file);
        } else {
            this.imagePreview = null;
        }
    }
}">
    
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Catalogue</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
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

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- LEFT MAIN COLUMN: Core Product Attributes (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Identity & Primary Photo -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Step 1 &bull; Essential Information</span>
                    <h3 class="text-base font-bold text-neutral-950">Product Name & Imagery</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Title *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           placeholder="e.g. Pure Cotton Ankle Socks (Pack of 3) or Smart AI 4K Dashcam" 
                           class="w-full text-sm font-semibold bg-neutral-50 border border-neutral-200 rounded-2xl p-3.5 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                <!-- Primary Photo Upload with Live Preview -->
                <div class="border border-dashed border-neutral-300 rounded-3xl p-6 bg-neutral-50/60 hover:bg-neutral-50 transition-colors">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Primary Showcase Photo</label>
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-32 h-32 rounded-2xl border border-neutral-200 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-inner relative">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="text-center p-2 text-neutral-400">
                                    <svg class="w-8 h-8 mx-auto mb-1 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] font-medium block">No photo</span>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-2 text-center sm:text-left flex-1">
                            <input type="file" name="primary_image" id="primary_image" accept="image/*" @change="previewFile" class="hidden">
                            <label for="primary_image" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-neutral-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider cursor-pointer shadow transition-all active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Upload Photo</span>
                            </label>
                            <p class="text-[11px] text-neutral-500">Supports JPG, PNG, WEBP up to 5MB. You can add more photos & colour swatches anytime later.</p>
                        </div>
                    </div>
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

            <!-- Card 2: Pricing & Stock (Instant Visible) -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Step 2 &bull; Pricing & Inventory</span>
                    <h3 class="text-base font-bold text-neutral-950">Financials & Inventory</h3>
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
                        <span class="text-[10px] text-neutral-400 mt-1 block">Listed customer price</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Sale Price (₹)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-neutral-500">₹</span>
                            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" 
                                   placeholder="799.00" 
                                   class="w-full text-sm font-bold pl-8 bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-rose-700">
                        </div>
                        <span class="text-[10px] text-neutral-400 mt-1 block">Optional discounted offer</span>
                    </div>

                    <div x-show="!isVariable">
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Available Stock Units</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 10) }}" min="0" 
                               placeholder="10" 
                               class="w-full text-sm font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <span class="text-[10px] text-neutral-400 mt-1 block">Units available for immediate order</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2 border-t border-neutral-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master SKU</label>
                        <input type="text" name="base_sku" value="{{ old('base_sku') }}" 
                               placeholder="Leave blank to auto-generate" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                        <span class="text-[10px] text-neutral-400 mt-1 block">Unique product identifier</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Barcode (UPC / EAN)</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" 
                               placeholder="e.g. 8901234567890" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <span class="text-[10px] text-neutral-400 mt-1 block">Optional retail barcode</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">GST Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 18) }}" 
                               class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <span class="text-[10px] text-neutral-400 mt-1 block">Default: 18% standard</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Optional Advanced Details Accordion -->
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

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Internal Cost Price (₹)</label>
                            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" placeholder="Internal only" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
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

        <!-- RIGHT SIDEBAR COLUMN: Publishing, Category & Badges (4 Cols) -->
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

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-600 mb-1.5">Product Architecture</label>
                    <select name="is_variable" x-model="isVariable" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-black">
                        <option :value="false">Simple Product (Single standard item)</option>
                        <option :value="true">Variable Product (Colours, Sizes, Finishes)</option>
                    </select>
                    <p class="text-[10px] text-neutral-400 mt-1" x-show="!isVariable">Best for socks, single books, or items without combinations.</p>
                    <p class="text-[10px] text-emerald-600 mt-1" x-show="isVariable">Variant matrix builder will unlock right after saving.</p>
                </div>

                <!-- Primary Submit Action -->
                <button type="submit" class="w-full py-4 rounded-2xl bg-neutral-950 hover:bg-black text-white text-xs font-bold uppercase tracking-wider shadow-lg transition-transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
@endsection
