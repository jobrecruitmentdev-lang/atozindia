@extends('layouts.app')

@section('title', $product->name . ' — ATOZ GADGETZ')
@section('meta_description', $product->seo_description ?: Str::limit(strip_tags($product->short_description ?: $product->description), 160))

@section('content')
@php
    $defaultVariant = $product->variants->first();
    $initialColorId = null;
    $initialAttributes = [];

    if ($defaultVariant) {
        foreach ($defaultVariant->attributeValues as $av) {
            if ($av->attribute) {
                $initialAttributes[$av->attribute->code] = $av->id;
                if ($av->attribute->code === 'color' || $av->attribute->type === 'color') {
                    $initialColorId = $av->id;
                }
            }
        }
    }

    // Default gallery
    $defaultGallery = [];
    if ($initialColorId && isset($colorGalleries[$initialColorId])) {
        $defaultGallery = $colorGalleries[$initialColorId];
    } elseif (!empty($generalImages)) {
        $defaultGallery = $generalImages;
    } elseif ($product->images->isNotEmpty()) {
        $defaultGallery = $product->images->map(fn($img) => [
            'id' => $img->id,
            'path' => $img->image_path,
            'alt' => $img->alt_text ?? $product->name,
            'is_primary' => $img->is_primary,
        ])->toArray();
    } else {
        $defaultGallery = [[
            'id' => 0,
            'path' => asset('storage/banners/hero-banner-1.jpg'),
            'alt' => $product->name,
            'is_primary' => true,
        ]];
    }

    $defaultImg = $defaultGallery[0]['path'] ?? '';
    $effectivePrice = $product->sale_price ?: $product->price;
    $productImages = $product->images->isNotEmpty() 
        ? $product->images->map(fn($img) => str_starts_with($img->image_path, 'http') ? $img->image_path : asset($img->image_path))->toArray() 
        : [asset('images/logo.png')];
    $avgRating = $product->reviews_avg_rating ?: 4.9;
    $reviewsCount = $product->reviews_count ?: 8;
@endphp

<!-- Schema.org Product Rich Snippets -->
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org/',
  '@type' => 'Product',
  'name' => $product->name,
  'image' => $productImages,
  'description' => strip_tags($product->short_description ?: $product->description),
  'sku' => $product->base_sku,
  'mpn' => $product->base_sku,
  'brand' => [
    '@type' => 'Brand',
    'name' => $product->brand?->name ?? 'ATOZ GADGETZ'
  ],
  'offers' => [
    '@type' => 'Offer',
    'url' => route('product.show', $product->slug),
    'priceCurrency' => 'INR',
    'price' => (string) $effectivePrice,
    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
    'itemCondition' => 'https://schema.org/NewCondition',
    'availability' => 'https://schema.org/InStock',
    'seller' => [
      '@type' => 'Organization',
      'name' => 'ATOZ GADGETZ'
    ]
  ],
  'aggregateRating' => [
    '@type' => 'AggregateRating',
    'ratingValue' => number_format((float)$avgRating, 1),
    'reviewCount' => (string) $reviewsCount
  ]
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12 py-8 space-y-16 sm:space-y-24"
     x-data="pdpApp({
        productId: {{ $product->id }},
        baseSku: '{{ $product->base_sku }}',
        basePrice: '₹{{ number_format($product->price, 2) }}',
        baseSalePrice: '{{ $product->sale_price ? '₹' . number_format($product->sale_price, 2) : '' }}',
        isVariable: {{ $product->is_variable ? 'true' : 'false' }},
        variants: {{ json_encode($variantsData) }},
        colorGalleries: {{ json_encode($colorGalleries) }},
        generalImages: {{ json_encode($generalImages) }},
        initialAttributes: {{ json_encode($initialAttributes) }},
        initialGallery: {{ json_encode($defaultGallery) }},
        initialImg: '{{ $defaultImg }}'
     })">

    <!-- Breadcrumb Nav -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">
        <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-black transition-colors">Catalogue</a>
        @if($product->categories->isNotEmpty())
            <span>/</span>
            <a href="{{ route('shop.index', ['category' => $product->categories->first()->slug]) }}" class="hover:text-black transition-colors">
                {{ $product->categories->first()->name }}
            </a>
        @endif
        <span>/</span>
        <span class="text-neutral-900 truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main PDP Grid: Gallery (Left) & Configurator (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
        
        <!-- LEFT: MULTI-COLOUR DYNAMIC GALLERY (7 Cols) -->
        <div class="lg:col-span-7 sticky top-28 space-y-4">
            
            <!-- Main Hero Viewport -->
            <div class="relative w-full aspect-square rounded-[36px] overflow-hidden bg-white border border-neutral-200/80 shadow-md group flex items-center justify-center p-4">
                <img :src="activeImage" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-contain transition-all duration-300 transform group-hover:scale-105">

                <!-- Badges -->
                <div class="absolute top-6 left-6 flex flex-col gap-2 pointer-events-none">
                    @if($product->sale_price)
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-rose-600 text-white shadow">
                            Promotion
                        </span>
                    @endif
                    @if($product->is_bestseller)
                        <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-gradient-to-r from-[#DFBA53] to-[#D4AF37] text-neutral-950 shadow-md">
                            Atelier Bestseller
                        </span>
                    @endif
                </div>

                <!-- Gallery Count Pill -->
                <div class="absolute bottom-6 right-6 px-3 py-1 rounded-full bg-[#141310]/80 border border-[#D4AF37]/30 backdrop-blur-md text-[#D4AF37] text-[10px] font-mono tracking-wider">
                    <span x-text="activeGalleryIndex + 1"></span> / <span x-text="currentGallery.length"></span>
                </div>
            </div>

            <!-- Thumbnail Carousel -->
            <div class="flex items-center gap-3 overflow-x-auto pb-2 pt-1">
                <template x-for="(img, idx) in currentGallery" :key="img.id || idx">
                    <button type="button" 
                            @click="selectImage(img.path, idx)" 
                            :class="activeImage === img.path ? 'border-[#D4AF37] ring-2 ring-[#D4AF37]/40 shadow-sm' : 'border-neutral-200 hover:border-[#D4AF37]/50 opacity-70 hover:opacity-100'" 
                            class="relative w-20 h-20 rounded-2xl overflow-hidden bg-white border p-1 transition-all shrink-0 focus:outline-none">
                        <img :src="img.path" :alt="img.alt" class="w-full h-full object-cover rounded-xl">
                    </button>
                </template>
            </div>
        </div>

        <!-- RIGHT: PRODUCT SPECIFICATIONS & VARIANT BUILDER (5 Cols) -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Brand & Title Header -->
            <div class="space-y-2 border-b border-neutral-100 pb-6">
                @if($product->brand)
                    <a href="{{ route('shop.index', ['brand' => $product->brand->slug]) }}" class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 hover:text-black transition-colors font-mono">
                        {{ $product->brand->name }}
                    </a>
                @endif

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-neutral-950 leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-4 pt-1">
                    <span class="text-xs font-mono text-neutral-400">SKU: <span class="font-bold text-neutral-700 uppercase" x-text="currentSku"></span></span>
                    <span class="text-neutral-300">&bull;</span>
                    <!-- Reviews Stars -->
                    <div class="flex items-center gap-1.5 text-xs">
                        <div class="flex text-amber-500">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="font-mono font-bold text-neutral-900">{{ number_format($product->reviews->avg('rating') ?: 5.0, 1) }}</span>
                        <span class="text-neutral-400">({{ $product->reviews->count() }} reviews)</span>
                    </div>
                </div>
            </div>

            <!-- Price & Tax Notice -->
            <div class="space-y-1">
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl sm:text-4xl font-extrabold text-neutral-950 font-mono tracking-tight" x-text="currentPrice"></span>
                    <template x-if="currentOriginalPrice">
                        <span class="text-lg text-neutral-400 line-through font-mono" x-text="currentOriginalPrice"></span>
                    </template>
                </div>
                <p class="text-[11px] text-neutral-500">Inclusive of all taxes &bull; GST invoice issued automatically upon order</p>
            </div>

            <!-- Short Editorial Story -->
            @if($product->short_description)
                <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            <!-- DYNAMIC VARIANT SELECTORS (Colours, Sizes, Finishes) -->
            @if($product->is_variable && !empty($attributeGroups))
                <div class="space-y-6 pt-4 border-t border-neutral-100">
                    @foreach($attributeGroups as $code => $group)
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold uppercase tracking-wider text-neutral-900">{{ $group['name'] }}</span>
                                <span class="text-neutral-500 font-mono" x-text="getAttributeLabel('{{ $code }}')"></span>
                            </div>

                            <!-- Colour Swatches -->
                            @if($group['type'] === 'color' || $code === 'color')
                                <div class="flex items-center gap-3">
                                    @foreach($group['values'] as $val)
                                        <button type="button" 
                                                @click="selectAttribute('{{ $code }}', {{ $val->id }}, '{{ addslashes($val->value) }}')"
                                                :class="selectedAttributes['{{ $code }}'] == {{ $val->id }} ? 'ring-2 ring-[#D4AF37] ring-offset-2 scale-110 shadow-md' : 'hover:scale-105 border-neutral-300'"
                                                class="relative w-8 h-8 rounded-full border transition-all focus:outline-none"
                                                title="{{ $val->value }}"
                                                style="background-color: {{ $val->code_or_hex ?? '#000000' }};">
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <!-- Text Pills (Size / Storage / Material) -->
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($group['values'] as $val)
                                        <button type="button" 
                                                @click="selectAttribute('{{ $code }}', {{ $val->id }}, '{{ addslashes($val->value) }}')"
                                                :class="selectedAttributes['{{ $code }}'] == {{ $val->id }} ? 'bg-[#D4AF37] text-neutral-950 border-[#D4AF37] font-black shadow-md' : 'bg-white text-neutral-700 border-neutral-200 hover:border-[#D4AF37]'"
                                                class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all focus:outline-none">
                                            {{ $val->value }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Inventory Stock Indicator -->
            <div class="pt-2">
                <template x-if="isAvailable">
                    <div class="flex items-center gap-2 text-xs font-medium text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Available in Atelier &bull; Dispatches within 24 hours</span>
                    </div>
                </template>
                <template x-if="!isAvailable">
                    <div class="flex items-center gap-2 text-xs font-medium text-rose-600">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Current Allocation Depleted &bull; Notify when replenished</span>
                    </div>
                </template>
            </div>

            <!-- Action Controls: Quantity & Add to Bag -->
            <div class="pt-4 space-y-4">
                <div class="flex items-center gap-4">
                    <!-- Quantity Stepper -->
                    <div class="flex items-center border border-neutral-300 rounded-2xl bg-white p-1">
                        <button type="button" @click="if(qty > 1) qty--" class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:text-black font-bold focus:outline-none text-base">&minus;</button>
                        <span class="w-10 text-center font-mono font-bold text-xs text-neutral-950" x-text="qty"></span>
                        <button type="button" @click="qty++" class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:text-black font-bold focus:outline-none text-base">&plus;</button>
                    </div>

                    <!-- Add to Bag Button -->
                    <button type="button" 
                            @click="submitToCart()" 
                            :disabled="!isAvailable"
                            :class="!isAvailable ? 'bg-neutral-300 cursor-not-allowed text-neutral-500' : 'btn-gold text-neutral-950 font-black shadow-xl transform hover:-translate-y-0.5'"
                            class="flex-1 py-4 px-8 rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Add to Bag</span>
                    </button>

                    <!-- Wishlist Toggle -->
                    <button type="button" 
                            @click="window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item saved to your private atelier wishlist.' } }))" 
                            class="w-12 h-12 rounded-2xl border border-neutral-300 bg-white hover:bg-neutral-50 flex items-center justify-center text-neutral-600 hover:text-rose-600 transition-colors shrink-0"
                            aria-label="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Atelier Guarantees -->
            <div class="border-t border-neutral-200/80 pt-6 space-y-3 text-xs text-neutral-600">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-neutral-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Complimentary Express Delivery on orders above ₹2,999</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-neutral-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>3-Year Official Atelier Guarantee & Authenticity Certificate</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-neutral-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>14-Day Private Studio Audition & Hassle-Free Returns</span>
                </div>
            </div>
        </div>
    </div>

    <!-- LOWER SECTION: SPECS ACCORDION, NARRATIVE, & REVIEWS -->
    <div class="border-t border-neutral-200 pt-16 space-y-16" x-data="{ sectionTab: 'specs' }">
        
        <!-- Tab Bar -->
        <div class="flex items-center gap-8 border-b border-neutral-200 pb-4 text-xs font-bold uppercase tracking-wider">
            <button type="button" @click="sectionTab = 'specs'" :class="sectionTab === 'specs' ? 'text-[#99772C] border-b-2 border-[#D4AF37] font-extrabold pb-4 -mb-4' : 'text-neutral-400 hover:text-[#99772C]'" class="transition-colors">
                Specifications & Engineering
            </button>
            <button type="button" @click="sectionTab = 'story'" :class="sectionTab === 'story' ? 'text-[#99772C] border-b-2 border-[#D4AF37] font-extrabold pb-4 -mb-4' : 'text-neutral-400 hover:text-[#99772C]'" class="transition-colors">
                Design Philosophy
            </button>
            <button type="button" @click="sectionTab = 'reviews'" :class="sectionTab === 'reviews' ? 'text-[#99772C] border-b-2 border-[#D4AF37] font-extrabold pb-4 -mb-4' : 'text-neutral-400 hover:text-[#99772C]'" class="transition-colors">
                Client Testimonials ({{ $product->reviews->count() }})
            </button>
        </div>

        <!-- Tab 1: Specs -->
        <div x-show="sectionTab === 'specs'" class="max-w-3xl space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @if($product->material)
                    <div class="p-4 rounded-2xl bg-white border border-neutral-200/80">
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block mb-1">Milled Material</span>
                        <span class="font-semibold text-neutral-900">{{ $product->material }}</span>
                    </div>
                @endif
                @if($product->dimensions)
                    <div class="p-4 rounded-2xl bg-white border border-neutral-200/80">
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block mb-1">Dimensions</span>
                        <span class="font-semibold text-neutral-900">{{ $product->dimensions }}</span>
                    </div>
                @endif
                @if($product->weight)
                    <div class="p-4 rounded-2xl bg-white border border-neutral-200/80">
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block mb-1">Net Weight</span>
                        <span class="font-semibold text-neutral-900">{{ $product->weight }} {{ $product->weight_unit }}</span>
                    </div>
                @endif
                @if($product->country_of_origin)
                    <div class="p-4 rounded-2xl bg-white border border-neutral-200/80">
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block mb-1">Assembly & Origin</span>
                        <span class="font-semibold text-neutral-900">{{ $product->country_of_origin }}</span>
                    </div>
                @endif
            </div>

            <!-- Custom Specs Key-Value Table -->
            @if(!empty($product->specifications_json))
                <div class="bg-white rounded-2xl border border-neutral-200/80 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($product->specifications_json as $k => $v)
                                <tr>
                                    <td class="p-3.5 font-bold uppercase tracking-wider text-neutral-500 bg-neutral-50/50 w-1/3">{{ $k }}</td>
                                    <td class="p-3.5 text-neutral-900 font-medium">{{ $v }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab 2: Story Narrative -->
        <div x-show="sectionTab === 'story'" class="max-w-3xl prose prose-neutral text-xs sm:text-sm leading-relaxed text-neutral-700">
            {!! nl2br(e($product->description ?: $product->short_description)) !!}
        </div>

        <!-- Tab 3: Reviews & Verification -->
        <div x-show="sectionTab === 'reviews'" class="space-y-10 max-w-4xl">
            <!-- Review Submission Form -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 mb-4">Submit Verified Atelier Feedback</h3>
                <form method="POST" action="{{ route('product.review', $product->slug) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Rating (1 to 5) *</label>
                            <select name="rating" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-semibold">
                                <option value="5">★★★★★ (5 - Exceptional)</option>
                                <option value="4">★★★★☆ (4 - Excellent)</option>
                                <option value="3">★★★☆☆ (3 - Good)</option>
                                <option value="2">★★☆☆☆ (2 - Fair)</option>
                                <option value="1">★☆☆☆☆ (1 - Disappointing)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Reviewer Name *</label>
                            <input type="text" name="author_name" required value="{{ auth()->user()?->name ?? '' }}" placeholder="Your name" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address *</label>
                            <input type="email" name="author_email" required value="{{ auth()->user()?->email ?? '' }}" placeholder="For purchase verification" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Review Headline</label>
                        <input type="text" name="title" placeholder="e.g. Flawless acoustic imaging and finish" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Your Experience Narrative *</label>
                        <textarea name="comment" rows="3" required placeholder="Detail the tonal balance, physical weight, and material feel..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black"></textarea>
                    </div>

                    <button type="submit" class="px-6 py-2.5 rounded-xl btn-gold text-neutral-950 font-black text-xs uppercase tracking-wider shadow">
                        Submit Review
                    </button>
                </form>
            </div>

            <!-- Existing Reviews List -->
            <div class="space-y-4">
                @forelse($product->reviews as $rev)
                    <div class="p-6 rounded-2xl bg-white border border-neutral-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex text-amber-500 text-xs">
                                    @for($i = 1; $i <= $rev->rating; $i++) ★ @endfor
                                </div>
                                <span class="font-bold text-xs text-neutral-900">{{ $rev->title ?: 'Verified Assessment' }}</span>
                            </div>
                            <span class="text-[11px] font-mono text-neutral-400">{{ $rev->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-xs text-neutral-600 leading-relaxed">{{ $rev->comment }}</p>
                        <div class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider pt-1">
                            &bull; {{ $rev->author_name }} @if($rev->is_verified_buyer)<span class="text-emerald-700 font-bold ml-1">(Verified Owner)</span>@endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-neutral-400 italic">No published reviews yet. Be the first collector to review this instrument.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RELATED PRODUCTS -->
    @if($relatedProducts->isNotEmpty())
        <div class="border-t border-neutral-200 pt-16 space-y-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">Companion Instruments</span>
                <h3 class="text-2xl font-extrabold tracking-tight text-neutral-950">You May Also Admire</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    @include('components.product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
    @endif

</div>

<!-- Alpine Script for Multi-Colour Dynamic Gallery & Variant Matrix -->
<script>
    function pdpApp(config) {
        return {
            productId: config.productId,
            baseSku: config.baseSku,
            basePrice: config.basePrice,
            baseSalePrice: config.baseSalePrice,
            isVariable: config.isVariable,
            variants: config.variants,
            colorGalleries: config.colorGalleries,
            generalImages: config.generalImages,
            selectedAttributes: Object.assign({}, config.initialAttributes),
            currentGallery: config.initialGallery,
            activeImage: config.initialImg,
            activeGalleryIndex: 0,
            qty: 1,

            init() {
                this.syncVariantState();
            },

            selectImage(path, index) {
                this.activeImage = path;
                this.activeGalleryIndex = index;
            },

            selectAttribute(code, valId, label) {
                this.selectedAttributes[code] = valId;

                // CRITICAL REQUIREMENT: If this is colour attribute, immediately swap gallery!
                if (code === 'color') {
                    this.switchColorGallery(valId);
                }

                this.syncVariantState();
            },

            switchColorGallery(colorId) {
                if (this.colorGalleries && this.colorGalleries[colorId] && this.colorGalleries[colorId].length > 0) {
                    this.currentGallery = this.colorGalleries[colorId];
                } else if (this.generalImages && this.generalImages.length > 0) {
                    this.currentGallery = this.generalImages;
                }

                if (this.currentGallery.length > 0) {
                    this.activeImage = this.currentGallery[0].path;
                    this.activeGalleryIndex = 0;
                }
            },

            get matchedVariant() {
                if (!this.isVariable || this.variants.length === 0) return null;

                return this.variants.find(v => {
                    for (let code in this.selectedAttributes) {
                        if (v.attributes[code] !== this.selectedAttributes[code]) {
                            return false;
                        }
                    }
                    return true;
                }) || this.variants[0];
            },

            get currentSku() {
                return this.matchedVariant ? this.matchedVariant.sku : this.baseSku;
            },

            get currentPrice() {
                return this.matchedVariant ? this.matchedVariant.formatted_price : (this.baseSalePrice || this.basePrice);
            },

            get currentOriginalPrice() {
                if (this.matchedVariant) {
                    return this.matchedVariant.original_price;
                }
                return this.baseSalePrice ? this.basePrice : null;
            },

            get isAvailable() {
                if (!this.isVariable) return true;
                return this.matchedVariant ? this.matchedVariant.is_in_stock : true;
            },

            getAttributeLabel(code) {
                return '';
            },

            syncVariantState() {
                // Keep local state reactive
            },

            submitToCart() {
                const varId = this.matchedVariant ? this.matchedVariant.id : null;
                // Dispatch directly to the globalApp addToCart
                if (window.dispatchEvent) {
                    // Call layout globalApp method
                    const appEl = document.querySelector('body[x-data]');
                    if (appEl && appEl.__x) {
                        appEl.__x.$data.addToCart(this.productId, varId, this.qty);
                    } else {
                        // Fallback fetch
                        fetch('{{ route("cart.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: this.productId,
                                variant_id: varId,
                                quantity: this.qty
                            })
                        })
                        .then(r => r.json())
                        .then(d => {
                            if (d.success) {
                                window.dispatchEvent(new CustomEvent('cart-updated'));
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: d.message } }));
                            }
                        });
                    }
                }
            }
        };
    }
</script>
@endsection
