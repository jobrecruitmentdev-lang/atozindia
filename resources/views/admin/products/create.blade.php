@extends('layouts.admin')

@section('page_title', 'Create New Product')
@section('page_subtitle', 'Define core specifications, pricing, brand affiliations, and catalogue properties')

@section('content')
<div class="max-w-5xl" x-data="{ tab: 'general', isVariable: false }">
    
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1">
            &larr; Back to Catalogue
        </a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-8">
        @csrf

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-2xl border border-neutral-200/80 p-1.5 shadow-sm flex flex-wrap gap-1">
            <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">1. General</button>
            <button type="button" @click="tab = 'pricing'" :class="tab === 'pricing' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">2. Pricing & Tax</button>
            <button type="button" @click="tab = 'description'" :class="tab === 'description' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">3. Story & Specs</button>
            <button type="button" @click="tab = 'categories'" :class="tab === 'categories' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">4. Affiliations</button>
            <button type="button" @click="tab = 'dimensions'" :class="tab === 'dimensions' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">5. Shipping & Materials</button>
            <button type="button" @click="tab = 'seo'" :class="tab === 'seo' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">6. SEO & Meta</button>
        </div>

        <!-- Tab 1: General -->
        <div x-show="tab === 'general'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Identity & Nomenclature</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Aether One Active Noise Cancelling Headphones" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Master Base SKU *</label>
                    <input type="text" name="base_sku" value="{{ old('base_sku') }}" required placeholder="e.g. AET-ONE-BASE" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Barcode (UPC / EAN)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 8901234567890" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Product Architecture</label>
                    <select name="is_variable" x-model="isVariable" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option :value="false">Simple Product (Single standard item)</option>
                        <option :value="true">Variable Product (Multiple colours, sizes, finishes)</option>
                    </select>
                </div>

                <div x-show="!isVariable">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Initial Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 10) }}" min="0" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Visibility Status *</label>
                    <select name="status" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Visible in Storefront)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private staging)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Merchandising Toggles -->
            <div class="border-t border-neutral-100 pt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Featured</span>
                        <span class="text-[10px] text-neutral-500">Surface on homepage spotlight</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_bestseller" value="1" class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">Bestseller</span>
                        <span class="text-[10px] text-neutral-500">Add bestseller badge</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/80 cursor-pointer hover:bg-neutral-100">
                    <input type="checkbox" name="is_new_arrival" value="1" class="rounded border-neutral-300 text-black focus:ring-0">
                    <div>
                        <span class="text-xs font-bold text-neutral-900 block">New Arrival</span>
                        <span class="text-[10px] text-neutral-500">Show in new releases tab</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Tab 2: Pricing & Tax -->
        <div x-show="tab === 'pricing'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Financial Valuation</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Regular Price (INR ₹) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required placeholder="34999.00" class="w-full text-xs font-bold bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Sale Price (INR ₹)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" placeholder="29999.00" class="w-full text-xs font-bold bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    <span class="text-[10px] text-neutral-400 mt-1 block">Leave blank if not on promotion</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Internal Cost Price (INR ₹)</label>
                    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" placeholder="16000.00" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    <span class="text-[10px] text-neutral-400 mt-1 block">Visible to administrators only</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">GST Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 18) }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>
            </div>
        </div>

        <!-- Tab 3: Description & Specs -->
        <div x-show="tab === 'description'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6" x-data="{ specs: [{ key: '', value: '' }] }">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Editorial Copy & Technical Specifications</h3>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Short Summary (Card Preview)</label>
                <textarea name="short_description" rows="2" placeholder="Concise high-impact architectural summary..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('short_description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Full Editorial Story</label>
                <textarea name="description" rows="6" placeholder="Extensive product narrative, acoustic/material design notes, and details..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ old('description') }}</textarea>
            </div>

            <!-- Dynamic Specifications Generator -->
            <div class="border-t border-neutral-100 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Technical Specifications</label>
                    <button type="button" @click="specs.push({ key: '', value: '' })" class="text-xs font-bold text-neutral-900 hover:underline">
                        + Add Spec Row
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(spec, idx) in specs" :key="idx">
                        <div class="flex items-center gap-3">
                            <input type="text" name="spec_keys[]" x-model="spec.key" placeholder="e.g. Diaphragm Material" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5">
                            <input type="text" name="spec_values[]" x-model="spec.value" placeholder="e.g. 40mm Beryllium" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5">
                            <button type="button" @click="specs.splice(idx, 1)" class="p-2 text-rose-500 hover:text-rose-700">&times;</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Tab 4: Categories & Brands -->
        <div x-show="tab === 'categories'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Catalogue Classification</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Brand Affiliation</label>
                    <select name="brand_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="">No Brand Affiliation</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Categories (Hold CTRL/CMD for multiple)</label>
                    <select name="categories[]" multiple required size="5" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ in_array($c->id, old('categories', [])) ? 'selected' : '' }}>
                                {{ $c->parent ? "— {$c->name} ({$c->parent->name})" : $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tab 5: Dimensions & Shipping -->
        <div x-show="tab === 'dimensions'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Logistics & Physical Properties</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Weight</label>
                    <div class="flex gap-2">
                        <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" placeholder="0.65" class="flex-1 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                        <select name="weight_unit" class="w-20 text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Dimensions (L × W × H)</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="e.g. 210 × 185 × 75 mm" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Primary Material</label>
                    <input type="text" name="material" value="{{ old('material') }}" placeholder="e.g. Grade-5 Titanium, Beryllium" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Country of Origin</label>
                    <input type="text" name="country_of_origin" value="{{ old('country_of_origin') }}" placeholder="e.g. Germany, Switzerland, Japan" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" placeholder="e.g. Aether Acoustic AG" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
                </div>
            </div>
        </div>

        <!-- Tab 6: SEO -->
        <div x-show="tab === 'seo'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Search Engine Optimization</h3>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Meta Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Custom meta title for Google search..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Meta Description</label>
                <textarea name="seo_description" rows="3" placeholder="Optimized SERP snippet..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3">{{ old('seo_description') }}</textarea>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="flex items-center justify-between border-t border-neutral-200 pt-6">
            <span class="text-xs text-neutral-500">Multi-colour image galleries and variant builder will unlock after saving.</span>
            <button type="submit" class="bg-neutral-950 hover:bg-black text-white text-xs font-bold uppercase tracking-wider px-8 py-3.5 rounded-full shadow-lg transition-transform active:scale-95">
                Save & Continue to Gallery Setup &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
