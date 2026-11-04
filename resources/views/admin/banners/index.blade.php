@extends('layouts.admin')

@section('page_title', 'Homepage Banners & Hero Assets')
@section('page_subtitle', 'Curate high-impact split-screen heroes, promotional banners, and campaign imagery')

@section('content')
<div class="space-y-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Create Banner Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm h-fit">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3 mb-6">
                Add Visual Campaign Banner
            </h3>

            <form method="POST" action="{{ route('admin.banners.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Headline Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Sonic Perfection Redefined" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Subtitle / Campaign Narrative</label>
                    <input type="text" name="subtitle" placeholder="e.g. Experience precision-engineered acoustic clarity." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Position *</label>
                        <select name="position" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <option value="hero">Hero Carousel</option>
                            <option value="promo_strip">Promotional Strip</option>
                            <option value="featured_grid">Featured Grid</option>
                            <option value="footer_banner">Footer Marquee</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Pill Badge</label>
                        <input type="text" name="badge_text" placeholder="e.g. 2026 EDITION" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Desktop Image URL *</label>
                    <input type="text" name="desktop_image" required placeholder="https://images.unsplash.com/... or /assets/..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Mobile Image URL (Optional)</label>
                    <input type="text" name="mobile_image" placeholder="Fallback for vertical screens" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Button CTA Text</label>
                        <input type="text" name="cta_text" placeholder="e.g. Explore Atelier" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Button CTA Link</label>
                        <input type="text" name="cta_link" placeholder="e.g. /shop or /products/..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Sort Sequence</label>
                    <input type="number" name="sort_order" value="0" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-mono">
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-neutral-300 text-black">
                        <span>Active (Publicly rendered)</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                    Save Banner Asset
                </button>
            </form>
        </div>

        <!-- Right: Banners Table / Preview -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Display Assets ({{ $banners->total() }})</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Media assets loaded dynamically by the storefront theme.</p>
                    </div>
                </div>

                <div class="divide-y divide-neutral-100">
                    @forelse($banners as $banner)
                        <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:bg-neutral-50/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-16 rounded-2xl overflow-hidden bg-neutral-100 border border-neutral-200 shrink-0">
                                    <img src="{{ $banner->desktop_image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-neutral-900 text-white font-mono">{{ $banner->position }}</span>
                                        @if($banner->badge_text)
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700">{{ $banner->badge_text }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-sm text-neutral-950">{{ $banner->title }}</h4>
                                    <p class="text-xs text-neutral-500 truncate max-w-md">{{ $banner->subtitle }}</p>
                                    @if($banner->cta_link)
                                        <div class="text-[10px] text-neutral-400 font-mono mt-1">CTA: {{ $banner->cta_text ?: 'Shop Now' }} &rarr; {{ $banner->cta_link }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 self-end sm:self-center">
                                <span class="font-mono text-xs text-neutral-400">Sort: {{ $banner->sort_order }}</span>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" onsubmit="return confirm('Remove banner {{ $banner->title }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:text-rose-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-neutral-400 text-xs">No banners registered yet.</div>
                    @endforelse
                </div>
            </div>

            @if($banners->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
