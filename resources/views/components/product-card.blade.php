@props(['product'])

@php
    $images = $product->images;
    $primaryImg = $product->primary_image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80';
    $secondaryImg = $images->count() > 1 ? $images[1]->image_path : $primaryImg;
    
    // Check for color variants
    $colorOptions = collect();
    $firstVariantId = null;
    if ($product->is_variable && $product->variants->isNotEmpty()) {
        $firstVariantId = $product->variants->first()->id;
        foreach ($product->variants as $var) {
            foreach ($var->attributeValues as $val) {
                if ($val->attribute && $val->attribute->type === 'color' && !$colorOptions->has($val->id)) {
                    $colorOptions->put($val->id, [
                        'id' => $val->id,
                        'name' => $val->value,
                        'hex' => $val->code_or_hex ?? '#000000',
                        'variant_id' => $var->id,
                        'image' => $images->firstWhere('attribute_value_id', $val->id)?->image_path ?? $primaryImg,
                    ]);
                }
            }
        }
    }

    $discountPct = null;
    if ($product->sale_price && $product->sale_price < $product->price) {
        $discountPct = round((($product->price - $product->sale_price) / $product->price) * 100);
    }

    $cardPayload = [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'brand' => $product->brand?->name ?? 'ATOZ GADGETZ',
        'sku' => $product->base_sku,
        'image' => $primaryImg,
        'price' => '₹' . number_format($product->price, 2),
        'sale_price' => $product->sale_price ? '₹' . number_format($product->sale_price, 2) : null,
        'description' => Str::limit(strip_tags($product->short_description ?: $product->description), 160),
        'url' => route('product.show', $product->slug),
        'variant_id' => $firstVariantId,
        'specs' => is_array($product->specifications_json) ? array_slice($product->specifications_json, 0, 4) : [],
    ];
@endphp

<div class="group relative flex flex-col bg-white rounded-3xl border border-neutral-200/80 p-4 transition-all duration-300 hover:shadow-xl hover:border-neutral-900"
     x-data="{
        activeImg: '{{ $primaryImg }}',
        hoverImg: '{{ $secondaryImg }}',
        isHovered: false,
        selectedVariantId: {{ $firstVariantId ?? 'null' }},
        productPayload: {{ json_encode($cardPayload) }}
     }">
    
    <!-- Image Viewport with Hover Transition -->
    <a href="{{ route('product.show', $product->slug) }}" 
       class="relative w-full aspect-square rounded-2xl overflow-hidden bg-neutral-100 flex items-center justify-center mb-4 block"
       @mouseenter="isHovered = true"
       @mouseleave="isHovered = false">
        
        <img :src="isHovered && hoverImg !== activeImg ? hoverImg : activeImg" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover transition-all duration-500 transform group-hover:scale-105">

        <!-- Badges Container -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            @if($discountPct)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-rose-600 text-white shadow-sm">
                    -{{ $discountPct }}%
                </span>
            @endif
            @if($product->is_bestseller)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-neutral-900 text-white shadow-sm">
                    Bestseller
                </span>
            @elseif($product->is_new_arrival)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500 text-white shadow-sm">
                    New Release
                </span>
            @endif
        </div>

        <!-- Wishlist Button -->
        <button type="button" 
                @click.prevent="window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Saved to your private atelier wishlist.' } }))" 
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-md border border-neutral-200 flex items-center justify-center text-neutral-600 hover:text-rose-600 hover:bg-white transition-all shadow-sm z-10"
                aria-label="Save to Wishlist">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </button>

        <!-- Quick Actions Overlay -->
        <div class="absolute inset-x-3 bottom-3 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-10 hidden sm:flex items-center gap-2">
            <button type="button" 
                    @click.prevent="window.dispatchEvent(new CustomEvent('open-quickview', { detail: productPayload }))"
                    class="flex-1 py-2.5 rounded-xl bg-white/95 hover:bg-white text-neutral-900 border border-neutral-200/80 font-bold text-[11px] uppercase tracking-wider transition-all shadow-lg flex items-center justify-center gap-1.5 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Quick View</span>
            </button>
            <button type="button" 
                    @click.prevent="addToCart({{ $product->id }}, selectedVariantId, 1)"
                    class="p-2.5 rounded-xl bg-neutral-950/90 hover:bg-black text-white transition-all shadow-lg flex items-center justify-center backdrop-blur-md"
                    title="Add to Bag">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </button>
        </div>
    </a>

    <!-- Product Metadata -->
    <div class="flex-1 flex flex-col justify-between space-y-3">
        <div class="space-y-1">
            @if($product->brand)
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 block font-mono">
                    {{ $product->brand->name }}
                </span>
            @endif

            <h3 class="text-xs sm:text-sm font-bold text-neutral-950 leading-snug line-clamp-2 hover:text-neutral-600 transition-colors">
                <a href="{{ route('product.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <!-- Color Swatches (if variable) -->
        @if($colorOptions->isNotEmpty())
            <div class="flex items-center gap-1.5 pt-1">
                @foreach($colorOptions as $opt)
                    <button type="button" 
                            @click="activeImg = '{{ $opt['image'] }}'; selectedVariantId = {{ $opt['variant_id'] }};"
                            title="{{ $opt['name'] }}"
                            class="w-3.5 h-3.5 rounded-full border border-neutral-300 transition-transform hover:scale-125 focus:outline-none"
                            style="background-color: {{ $opt['hex'] }}">
                    </button>
                @endforeach
                <span class="text-[10px] text-neutral-400 font-mono ml-1">+{{ $colorOptions->count() }} colours</span>
            </div>
        @endif

        <!-- Price & Mobile Quick Add -->
        <div class="pt-2 border-t border-neutral-100 flex items-center justify-between">
            <div class="flex items-baseline gap-2">
                @if($product->sale_price)
                    <span class="font-mono text-sm sm:text-base font-extrabold text-neutral-950">
                        ₹{{ number_format($product->sale_price, 2) }}
                    </span>
                    <span class="font-mono text-xs text-neutral-400 line-through">
                        ₹{{ number_format($product->price, 2) }}
                    </span>
                @else
                    <span class="font-mono text-sm sm:text-base font-extrabold text-neutral-950">
                        ₹{{ number_format($product->price, 2) }}
                    </span>
                @endif
            </div>

            <!-- Mobile Only Add Button -->
            <button type="button" 
                    @click.prevent="addToCart({{ $product->id }}, selectedVariantId, 1)"
                    class="sm:hidden p-2 rounded-xl bg-neutral-950 text-white hover:bg-neutral-800 transition-colors"
                    aria-label="Add to bag">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>
    </div>
</div>
