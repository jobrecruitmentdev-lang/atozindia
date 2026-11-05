@extends('layouts.app')

@section('title', 'Curated Catalogue — ATOZ GADGETZ')
@section('meta_description', 'Browse our complete atelier collection of studio acoustics, titanium wearables, and desk architectures.')

@section('content')
<div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12 py-8 space-y-8" x-data="{ mobileFiltersOpen: false }">

    <!-- Header & Breadcrumbs -->
    <div class="border-b border-neutral-200 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider mb-2">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span>/</span>
                <span class="text-neutral-900">Catalogue</span>
                @if(request('category'))
                    <span>/</span>
                    <span class="text-neutral-900">{{ ucwords(str_replace('-', ' ', request('category'))) }}</span>
                @endif
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-neutral-950">
                @if(request('q'))
                    Results for "{{ request('q') }}"
                @elseif(request('category'))
                    {{ ucwords(str_replace('-', ' ', request('category'))) }}
                @else
                    All Atelier Instruments
                @endif
            </h1>
            <p class="text-xs text-neutral-500 mt-1">Showing {{ $products->total() }} precision objects available for private dispatch.</p>
        </div>

        <!-- Sorting & Mobile Filter Trigger -->
        <div class="flex items-center gap-3 self-start md:self-end">
            <!-- Mobile Filter Button -->
            <button type="button" 
                    @click="mobileFiltersOpen = true" 
                    class="lg:hidden px-4 py-2.5 rounded-full border border-neutral-300 bg-white text-xs font-bold text-neutral-900 flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Refine</span>
            </button>

            <!-- Sorting Select -->
            <form method="GET" action="{{ route('shop.index') }}" id="sortForm" class="flex items-center gap-2">
                @foreach(request()->except(['sort', 'page']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $subV)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <label for="sortSelect" class="text-xs font-semibold text-neutral-500 uppercase tracking-wider hidden sm:inline">Sort:</label>
                <select name="sort" id="sortSelect" onchange="this.form.submit()" class="text-xs font-semibold bg-white border border-neutral-300 rounded-full px-4 py-2.5 focus:outline-none focus:border-black shadow-sm">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Releases</option>
                    <option value="bestseller" {{ $sort === 'bestseller' ? 'selected' : '' }}>Most Coveted (Bestsellers)</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="featured" {{ $sort === 'featured' ? 'selected' : '' }}>Curator's Spotlight</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Active Filter Tags / Pills -->
    @if(request()->hasAny(['category', 'brand', 'min_price', 'max_price', 'color', 'in_stock', 'on_sale', 'q']))
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="text-xs text-neutral-400 font-semibold uppercase tracking-wider">Active Refinements:</span>
            
            @if(request('q'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                    Query: "{{ request('q') }}"
                </span>
            @endif

            @if(request('category'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-neutral-900 text-white">
                    Dept: {{ request('category') }}
                </span>
            @endif

            @if(request('brand'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                    Brand: {{ is_array(request('brand')) ? implode(', ', request('brand')) : request('brand') }}
                </span>
            @endif

            @if(request('in_stock'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                    In Stock Only
                </span>
            @endif

            @if(request('on_sale'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200">
                    On Promotion
                </span>
            @endif

            <a href="{{ route('shop.index') }}" class="text-xs font-bold text-neutral-900 underline hover:text-neutral-500 ml-2">
                Clear All
            </a>
        </div>
    @endif

    <!-- Main Catalog Layout (Sticky Sidebar + Product Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- DESKTOP STICKY FILTER SIDEBAR (3 cols) -->
        <aside class="hidden lg:block lg:col-span-3 sticky top-28 space-y-8 pr-4">
            <form method="GET" action="{{ route('shop.index') }}" class="space-y-8">
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Department / Category Hierarchy -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-950">Departments</h3>
                    <div class="space-y-1.5 max-h-[500px] overflow-y-auto pr-1">
                        <a href="{{ route('shop.index', request()->except(['category', 'page'])) }}" 
                           class="flex items-center justify-between py-1.5 font-medium {{ !request('category') ? 'text-black font-bold' : 'text-neutral-600 hover:text-black' }}">
                            <span>All Departments</span>
                            <span class="font-mono text-neutral-400 text-[11px]">{{ \App\Models\Product::active()->count() }}</span>
                        </a>
                        @foreach($allCategories as $cat)
                            @php
                                $isCatActive = request('category') === $cat->slug || $cat->children->pluck('slug')->contains(request('category'));
                            @endphp
                            <div class="border border-neutral-100 rounded-xl overflow-hidden" x-data="{ open: {{ $isCatActive ? 'true' : 'false' }} }">
                                <div class="flex items-center justify-between py-2 px-2.5 bg-neutral-50/50 hover:bg-neutral-100/70 transition-colors">
                                    <a href="{{ route('shop.index', array_merge(request()->except(['page']), ['category' => $cat->slug])) }}" 
                                       class="flex-1 font-medium text-xs truncate {{ request('category') === $cat->slug ? 'text-black font-bold' : 'text-neutral-700 hover:text-black' }}">
                                        <span>{{ $cat->name }}</span>
                                        @if($cat->products_count > 0)
                                            <span class="font-mono text-neutral-400 text-[10px] ml-1">({{ $cat->products_count }})</span>
                                        @endif
                                    </a>
                                    @if($cat->children->isNotEmpty())
                                        <button type="button" @click="open = !open" class="p-1 text-neutral-400 hover:text-black focus:outline-none">
                                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @endif
                                </div>
                                @if($cat->children->isNotEmpty())
                                    <div x-show="open" x-cloak class="p-2 space-y-1 bg-white border-t border-neutral-100 text-[11px]">
                                        @foreach($cat->children as $sub)
                                            <a href="{{ route('shop.index', array_merge(request()->except(['page']), ['category' => $sub->slug])) }}" 
                                               class="flex items-center justify-between py-1 px-2 rounded hover:bg-neutral-50 transition-colors {{ request('category') === $sub->slug ? 'text-black font-bold bg-neutral-50' : 'text-neutral-500 hover:text-black' }}">
                                                <span class="truncate">{{ $sub->name }}</span>
                                                @if($sub->products_count > 0)
                                                    <span class="font-mono text-neutral-400 text-[10px]">({{ $sub->products_count }})</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Certified Brands -->
                <div class="space-y-3 pt-6 border-t border-neutral-200">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-950">Atelier Brands</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($allBrands as $b)
                            <label class="flex items-center justify-between text-xs text-neutral-700 hover:text-black cursor-pointer">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="brand[]" value="{{ $b->slug }}" 
                                           {{ (is_array(request('brand')) && in_array($b->slug, request('brand'))) || request('brand') === $b->slug ? 'checked' : '' }}
                                           class="rounded border-neutral-300 text-black focus:ring-0">
                                    <span>{{ $b->name }}</span>
                                </div>
                                <span class="font-mono text-[10px] text-neutral-400">{{ $b->products_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="space-y-3 pt-6 border-t border-neutral-200">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-950">Price Ceiling</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min ₹" class="w-1/2 text-xs bg-neutral-100 rounded-xl p-2.5 border border-transparent focus:border-black focus:bg-white focus:outline-none font-mono">
                            <span class="text-neutral-400 text-xs">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max ₹" class="w-1/2 text-xs bg-neutral-100 rounded-xl p-2.5 border border-transparent focus:border-black focus:bg-white focus:outline-none font-mono">
                        </div>
                    </div>
                </div>

                <!-- Stock & Sale Toggles -->
                <div class="space-y-3 pt-6 border-t border-neutral-200">
                    <label class="flex items-center gap-2.5 text-xs font-medium text-neutral-800 cursor-pointer">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                        <span>Ready to Ship (In Stock)</span>
                    </label>

                    <label class="flex items-center gap-2.5 text-xs font-medium text-neutral-800 cursor-pointer">
                        <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }} class="rounded border-neutral-300 text-black focus:ring-0">
                        <span>Promotional Allocations</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest hover:bg-neutral-800 transition-colors shadow">
                    Apply Refinements
                </button>
            </form>
        </aside>

        <!-- PRODUCT GRID (9 cols) -->
        <main class="lg:col-span-9 space-y-8">
            @if($products->isEmpty())
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-12 text-center space-y-4">
                    <div class="w-14 h-14 rounded-full bg-neutral-100 text-neutral-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-neutral-950">No Instruments Match This Refinement</h3>
                    <p class="text-xs text-neutral-500 max-w-sm mx-auto">Try clearing selected filters, widening the price range, or searching for alternative specifications.</p>
                    <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2.5 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                        Reset All Filters
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pt-8 border-t border-neutral-200">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </main>
    </div>

    <!-- MOBILE FILTERS MODAL / SLIDE-OVER -->
    <div x-show="mobileFiltersOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden lg:hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileFiltersOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white p-6 shadow-2xl flex flex-col justify-between overflow-y-auto">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Refine Collection</h3>
                        <button type="button" @click="mobileFiltersOpen = false" class="text-neutral-400 hover:text-black text-2xl">&times;</button>
                    </div>

                    <form method="GET" action="{{ route('shop.index') }}" class="space-y-6">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        
                        <!-- Categories -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Department</label>
                            <select name="category" class="w-full text-xs bg-neutral-100 rounded-xl p-3 focus:outline-none">
                                <option value="">All Departments</option>
                                @foreach($allCategories as $c)
                                    <option value="{{ $c->slug }}" class="font-bold" {{ request('category') === $c->slug ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @if($c->children->isNotEmpty())
                                        <optgroup label="&nbsp;&nbsp;↳ {{ $c->name }} Subcategories">
                                            @foreach($c->children as $sub)
                                                <option value="{{ $sub->slug }}" {{ request('category') === $sub->slug ? 'selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;{{ $sub->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Brands -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Brands</label>
                            <div class="space-y-1.5 max-h-40 overflow-y-auto">
                                @foreach($allBrands as $b)
                                    <label class="flex items-center gap-2 text-xs text-neutral-800">
                                        <input type="checkbox" name="brand[]" value="{{ $b->slug }}" {{ (is_array(request('brand')) && in_array($b->slug, request('brand'))) || request('brand') === $b->slug ? 'checked' : '' }} class="rounded border-neutral-300 text-black">
                                        <span>{{ $b->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-neutral-700">Price Range (₹)</label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 text-xs bg-neutral-100 rounded-xl p-2.5">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 text-xs bg-neutral-100 rounded-xl p-2.5">
                            </div>
                        </div>

                        <!-- Toggles -->
                        <div class="space-y-2 pt-2">
                            <label class="flex items-center gap-2 text-xs text-neutral-800">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="rounded border-neutral-300 text-black">
                                <span>In Stock Only</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-neutral-800">
                                <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }} class="rounded border-neutral-300 text-black">
                                <span>On Promotion</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-full bg-neutral-950 text-white font-bold text-xs uppercase tracking-widest">
                            Apply Refinements
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
