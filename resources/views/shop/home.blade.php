@extends('layouts.app')

@section('title', 'ATOZ GADGETZ — Haute Gadgetry & Architectural Tech')
@section('meta_description', 'Curated collection of precision-machined headphones, titanium smartwatches, and minimalist workspace acoustics.')

@section('content')
<div class="space-y-24 sm:space-y-32 pb-24">

    <!-- 1. HERO SECTION (Split Screen Luxury Presentation) -->
    <section class="relative bg-neutral-950 text-white overflow-hidden rounded-b-[40px] sm:rounded-b-[56px] shadow-2xl">
        <!-- Ambient Subtle Background Glow -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.08),transparent_50%)] pointer-events-none"></div>

        <div class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12 py-16 sm:py-24 lg:py-32 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Narrative (7 Cols) -->
                <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-bold tracking-widest uppercase text-neutral-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>2026 Atelier Collection</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.08] text-white">
                        ARCHITECTURAL <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-neutral-100 via-neutral-300 to-neutral-500">
                            ACOUSTICS & TECH.
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-neutral-400 max-w-xl font-normal leading-relaxed">
                        Engineered for the discerning audiophile and designer. Milled titanium, anodized aluminum, and bespoke planar acoustic drivers assembled with zero compromise.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-4">
                        <a href="{{ route('shop.index') }}" class="px-8 py-4 rounded-full btn-gold text-neutral-950 font-extrabold text-xs uppercase tracking-widest transition-all duration-300 transform hover:-translate-y-0.5 shadow-xl flex items-center gap-2">
                            <span>Explore Catalogue</span>
                            <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('shop.index', ['category' => 'audio-acoustics']) }}" class="px-8 py-4 rounded-full bg-transparent text-[#D4AF37] font-bold text-xs uppercase tracking-widest border border-[#D4AF37]/50 hover:border-[#D4AF37] hover:bg-[#D4AF37]/10 transition-all duration-300">
                            Audio Atelier
                        </a>
                    </div>

                    <!-- Client Trust Badges -->
                    <div class="pt-8 border-t border-white/10 grid grid-cols-3 gap-6 text-left">
                        <div>
                            <span class="block text-2xl sm:text-3xl font-extrabold tracking-tight text-[#D4AF37] font-mono">100%</span>
                            <span class="text-[11px] uppercase tracking-wider text-neutral-400">Authentic Milled</span>
                        </div>
                        <div>
                            <span class="block text-2xl sm:text-3xl font-extrabold tracking-tight text-[#D4AF37] font-mono">3-Yr</span>
                            <span class="text-[11px] uppercase tracking-wider text-neutral-400">Bespoke Warranty</span>
                        </div>
                        <div>
                            <span class="block text-2xl sm:text-3xl font-extrabold tracking-tight text-[#D4AF37] font-mono">Free</span>
                            <span class="text-[11px] uppercase tracking-wider text-neutral-400">Express Delivery</span>
                        </div>
                    </div>
                </div>

                <!-- Right Hero Visual (5 Cols) -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <div class="relative w-full max-w-lg aspect-square rounded-[36px] overflow-hidden bg-neutral-900 border border-white/10 shadow-2xl group">
                        @php
                            $heroImg = $heroBanners->first()?->desktop_image ?? asset('storage/banners/hero-banner-1.jpg');
                        @endphp
                        <img src="{{ $heroImg }}" 
                             alt="ATOZ Flagship Acoustic" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        
                        <!-- Floating Glass Pill on Product -->
                        <div class="absolute bottom-6 left-6 right-6 p-4 rounded-2xl bg-black/60 backdrop-blur-md border border-[#D4AF37]/30 flex items-center justify-between text-white">
                            <div>
                                <span class="text-[10px] font-bold text-[#D4AF37] uppercase tracking-widest block">Featured Masterpiece</span>
                                <h4 class="text-sm font-bold text-white tracking-wide">Aether One ANC Wireless</h4>
                            </div>
                            <span class="font-mono text-sm font-black text-neutral-950 bg-[#D4AF37] px-3 py-1.5 rounded-xl shadow-md">₹34,999</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. CURATED DEPARTMENTS / CATEGORIES (Floating Minimalist Cards) -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-2">Curated Taxonomy</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-neutral-950">Departments of Excellence</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs font-bold uppercase tracking-wider text-neutral-900 hover:text-neutral-500 transition-colors flex items-center gap-1">
                <span>View Full Index</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
            @foreach($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" 
                   class="group relative rounded-3xl overflow-hidden bg-neutral-100 border border-neutral-200/80 p-5 flex flex-col justify-between aspect-[3/4] hover:shadow-xl hover:border-[#D4AF37] transition-all duration-300 transform hover:-translate-y-1">
                    
                    <div class="z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#99772C] block mb-1">Dept</span>
                        <h3 class="text-sm sm:text-base font-bold text-neutral-950 leading-tight group-hover:text-[#99772C] transition-colors">{{ $category->name }}</h3>
                    </div>

                    <!-- Category Silhouette / Thumbnail -->
                    <div class="relative w-full h-32 flex items-center justify-center overflow-hidden my-auto">
                        @if($category->image_path)
                            <img src="{{ $category->image_path }}" alt="{{ $category->name }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-16 h-16 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-400 group-hover:bg-[#D4AF37] group-hover:text-neutral-950 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                        @endif
                    </div>

                    <div class="z-10 flex items-center justify-between text-[11px] font-semibold text-neutral-500 pt-2 border-t border-neutral-200/50">
                        <span>Browse</span>
                        <span class="group-hover:translate-x-1 group-hover:text-[#99772C] transition-transform">&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 3. FEATURED PRODUCTS (Editorial Presentation with Swatch Preview) -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-2">Spotlight Series</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-neutral-950">Architectural Objects</h2>
            </div>
            <a href="{{ route('shop.index', ['featured' => 1]) }}" class="text-xs font-bold uppercase tracking-wider text-neutral-900 hover:text-neutral-500 transition-colors flex items-center gap-1">
                <span>View All Featured</span> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

    <!-- 4. EDITORIAL SPLIT CAMPAIGN BANNER -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="bg-[#0f0e0b] border-2 border-[#D4AF37]/40 rounded-[36px] sm:rounded-[48px] overflow-hidden text-white grid grid-cols-1 lg:grid-cols-12 shadow-2xl">
            <div class="lg:col-span-6 p-8 sm:p-14 lg:p-16 flex flex-col justify-between space-y-8">
                <div class="space-y-4">
                    <span class="px-3.5 py-1.5 rounded-full text-[10px] font-mono uppercase tracking-widest bg-white/10 text-[#D4AF37] border border-[#D4AF37]/30 inline-block font-bold">
                        HAUTE ATELIER CRAFTSMANSHIP
                    </span>
                    <h3 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                        PRECISION SOLID TITANIUM. ZERO RESONANCE.
                    </h3>
                    <p class="text-sm sm:text-base text-neutral-400 leading-relaxed max-w-md">
                        Every dial, bezel, and ear-cup chassis is milled from grade 5 aerospace titanium ingots, delivering an acoustic enclosure with non-existent harmonic distortion.
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('shop.index') }}" class="px-8 py-4 rounded-full btn-gold text-neutral-950 font-extrabold text-xs uppercase tracking-widest transition-colors shadow-lg">
                        Shop Titanium Range
                    </a>
                </div>
            </div>

            <div class="lg:col-span-6 relative aspect-square sm:aspect-auto min-h-[350px] overflow-hidden">
                <img src="{{ asset('storage/banners/hero-banner-2.jpg') }}" 
                     alt="Titanium Watch Engineering" 
                     class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- 5. TABBED COLLECTIONS: BESTSELLERS & NEW ARRIVALS -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12" x-data="{ currentTab: 'bestsellers' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10 pb-4 border-b border-neutral-200">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#99772C] block mb-1">Curated Selection</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-950">Trending in the Atelier</h2>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center p-1 bg-neutral-100 rounded-full text-xs font-bold">
                <button type="button" 
                        @click="currentTab = 'bestsellers'" 
                        :class="currentTab === 'bestsellers' ? 'bg-[#D4AF37] text-neutral-950 font-black shadow-md' : 'text-neutral-600 hover:text-[#99772C]'" 
                        class="px-6 py-2.5 rounded-full transition-all uppercase tracking-wider">
                    Bestsellers
                </button>
                <button type="button" 
                        @click="currentTab = 'new_arrivals'" 
                        :class="currentTab === 'new_arrivals' ? 'bg-[#D4AF37] text-neutral-950 font-black shadow-md' : 'text-neutral-600 hover:text-[#99772C]'" 
                        class="px-6 py-2.5 rounded-full transition-all uppercase tracking-wider">
                    New Arrivals
                </button>
            </div>
        </div>

        <!-- Tab 1: Bestsellers -->
        <div x-show="currentTab === 'bestsellers'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach($bestsellers as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>

        <!-- Tab 2: New Arrivals -->
        <div x-show="currentTab === 'new_arrivals'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach($newArrivals as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. BRAND ATELIER PARTNERS -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#99772C] block mb-2">Certified Provenance</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-950">Official Atelier Partners</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($brands as $brand)
                <div class="p-6 rounded-2xl bg-white border border-neutral-200/80 hover:border-[#D4AF37] hover:shadow-gold transition-all flex flex-col items-center justify-center text-center group">
                    <span class="font-extrabold text-sm sm:text-base tracking-widest uppercase text-neutral-800 group-hover:text-[#99772C] transition-colors font-sans">
                        {{ $brand->name }}
                    </span>
                    <span class="text-[10px] text-neutral-400 uppercase tracking-wider mt-1">Official Dist.</span>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 7. THE ATOZ STANDARD (Trust & Service Guarantee Strip) -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="bg-neutral-100/70 border border-neutral-200 rounded-[36px] p-8 sm:p-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">White-Glove Delivery</h4>
                <p class="text-xs text-neutral-500 leading-relaxed">Insured express delivery with temperature-controlled handling on all high-fidelity instruments.</p>
            </div>

            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">3-Year Atelier Warranty</h4>
                <p class="text-xs text-neutral-500 leading-relaxed">Direct manufacturer and concierge warranty replacement with zero hidden clauses.</p>
            </div>

            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">14-Day Private Audition</h4>
                <p class="text-xs text-neutral-500 leading-relaxed">Audition any acoustic headphone or monitor in your personal studio with hassle-free returns.</p>
            </div>

            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-[#1A1917] border border-[#D4AF37]/40 text-[#D4AF37] flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">Dedicated Concierge</h4>
                <p class="text-xs text-neutral-500 leading-relaxed">24/7 technical advice, acoustic pairing guidance, and custom order assistance.</p>
            </div>
        </div>
    </section>

    <!-- 8. PRIVATE SOCIETY NEWSLETTER -->
    <section class="max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="bg-[#0f0e0b] border-2 border-[#D4AF37]/40 text-white rounded-[36px] sm:rounded-[48px] p-8 sm:p-16 text-center max-w-4xl mx-auto shadow-2xl space-y-6">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#D4AF37] block">Private Invitations</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">The ATOZ Collector's Circle</h2>
            <p class="text-sm text-neutral-300 max-w-lg mx-auto leading-relaxed">
                Receive private allocations of limited-run titanium editions, unannounced product drops, and invitations to private listening salons.
            </p>

            <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-3 pt-2" onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'You have been enrolled in the Collector\'s Circle.' } })); this.reset();">
                <input type="email" required placeholder="Enter your private email..." class="flex-1 bg-white/10 border border-[#D4AF37]/30 rounded-full px-5 py-3 text-xs text-white placeholder:text-neutral-500 focus:outline-none focus:border-[#D4AF37]">
                <button type="submit" class="px-8 py-3 rounded-full btn-gold text-neutral-950 font-black text-xs uppercase tracking-widest shadow">
                    Request Entry
                </button>
            </form>
        </div>
    </section>

</div>
@endsection
