@extends('layouts.app')

@section('title', 'All Departments & Collections — ATOZ GADGETZ')
@section('meta_description', 'Explore all 18 certified luxury departments and 190 specialized categories with express insured delivery across India.')

@section('content')
<div class="bg-neutral-50/50 min-h-screen pb-24">

    <!-- Hero Header Banner -->
    <div class="bg-white border-b border-neutral-200/80 pt-10 pb-12">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-black">Home</a>
                <span>/</span>
                <span class="text-neutral-900 font-bold">All Departments</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-neutral-100 text-neutral-800 text-[10px] font-bold uppercase tracking-widest font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        18 Master Departments &bull; {{ $totalSubcategoriesCount }} Specialized Collections
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-neutral-950 uppercase font-sans">
                        Marketplace Directory
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                        Navigate our comprehensive catalogue spanning haute horology, computational instruments, artisanal apparel, luxury footwear, ambient living, and gourmet provisions.
                    </p>
                </div>

                <!-- Quick Stats Badges -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('shop.index') }}" 
                       class="px-5 py-3 rounded-full btn-gold text-neutral-950 font-black text-xs uppercase tracking-wider shadow-md flex items-center gap-2">
                        <span>Browse Entire Catalogue</span>
                        <svg class="w-4 h-4 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Sticky Quick-Jump Floating Bar -->
            <div class="pt-6 border-t border-neutral-100 overflow-x-auto scrollbar-none flex items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#99772C] shrink-0 mr-1 font-mono">Jump To:</span>
                @foreach($departments as $dept)
                    <a href="#dept-{{ $dept->slug }}" 
                       class="px-3.5 py-1.5 rounded-full bg-neutral-100 hover:bg-[#D4AF37] hover:text-neutral-950 text-neutral-700 text-xs font-semibold whitespace-nowrap transition-all shrink-0">
                        {{ $dept->name }}
                    </a>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Main Departments Showcase Container -->
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-16">
        
        @foreach($departments as $index => $dept)
            <section id="dept-{{ $dept->slug }}" class="scroll-mt-28 space-y-6">
                
                <!-- Department Master Header Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                    <div class="space-y-2 z-10 max-w-2xl">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-extrabold text-neutral-400 uppercase tracking-widest">
                                Department {{ sprintf('%02d', $index + 1) }} / 18
                            </span>
                            <span class="w-1 h-1 rounded-full bg-neutral-300"></span>
                            <span class="text-xs font-semibold text-neutral-500 font-mono">
                                {{ $dept->children->count() }} Subcategories
                            </span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">
                            {{ $dept->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            {{ $dept->description }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 z-10 shrink-0">
                        <a href="{{ route('shop.index', ['category' => $dept->slug]) }}" 
                           class="px-5 py-3 rounded-full bg-neutral-100 hover:bg-[#D4AF37] hover:border-[#D4AF37] hover:text-neutral-950 text-neutral-900 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2 group border border-neutral-200/80">
                            <span>Explore Department</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                    </div>

                    <!-- Ambient background decorative numeral -->
                    <span class="absolute -right-4 -bottom-6 text-8xl font-black text-neutral-100 pointer-events-none select-none z-0">
                        {{ sprintf('%02d', $index + 1) }}
                    </span>
                </div>

                <!-- Subcategories Interactive Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @foreach($dept->children as $sub)
                        <a href="{{ route('shop.index', ['category' => $sub->slug]) }}" 
                           class="group bg-white rounded-2xl border border-neutral-200/80 p-4 hover:border-[#D4AF37] hover:shadow-gold transition-all flex flex-col justify-between min-h-[95px]">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-neutral-900 group-hover:text-[#99772C] line-clamp-1 block">
                                    {{ $sub->name }}
                                </span>
                                <span class="text-[10px] text-neutral-400 group-hover:text-neutral-600 line-clamp-1 block">
                                    {{ $sub->products_count }} product(s) available
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-neutral-400 group-hover:text-[#99772C] pt-2 mt-auto border-t border-neutral-100">
                                <span>Browse</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Featured Products In This Department (if any) -->
                @if(isset($departmentProducts[$dept->id]) && $departmentProducts[$dept->id]->isNotEmpty())
                    <div class="pt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-700 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-neutral-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span>Featured In {{ $dept->name }}</span>
                            </h3>
                            <a href="{{ route('shop.index', ['category' => $dept->slug]) }}" class="text-[11px] font-bold text-neutral-500 hover:text-black uppercase tracking-wider">
                                View All ({{ $dept->products_count }}) &rarr;
                            </a>
                        </div>

                        <!-- 4-Product Shelf -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($departmentProducts[$dept->id] as $product)
                                @include('components.product-card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                @endif

            </section>
        @endforeach

    </div>

</div>
@endsection
