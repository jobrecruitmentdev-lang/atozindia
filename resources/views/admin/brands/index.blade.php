@extends('layouts.admin')

@section('page_title', 'Brand Portfolio')
@section('page_subtitle', 'Manage manufacture partners, brand marques, storytelling, and spotlight badges')

@section('content')
<div class="space-y-8" x-data="{ 
    editModalOpen: false, 
    editBrand: { id: null, name: '', website: '', logo_path: '', description: '', is_featured: false, is_active: true } 
}">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Add Brand Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm h-fit">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3 mb-6">
                Register New Brand
            </h3>

            <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Brand Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Bang & Olufsen" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Official Website URL</label>
                    <input type="url" name="website" placeholder="https://..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Brand Logo URL / Path</label>
                    <input type="text" name="logo_path" placeholder="/assets/brands/... or https://..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Brand Heritage Story</label>
                    <textarea name="description" rows="3" placeholder="Atelier history, origin, craftsmanship..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black"></textarea>
                </div>

                <div class="pt-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-neutral-300 text-black">
                        <span>Showcase in Homepage Brand Marquee</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-neutral-300 text-black">
                        <span>Active</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                    Register Brand
                </button>
            </form>
        </div>

        <!-- Right: Brands Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Brands Directory ({{ $brands->total() }})</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Manage certified brand profiles and product lines.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Brand</th>
                                <th class="p-4">Website</th>
                                <th class="p-4">Catalogue Size</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($brands as $brand)
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-neutral-900">{{ $brand->name }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono">{{ $brand->slug }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if($brand->website)
                                            <a href="{{ $brand->website }}" target="_blank" class="text-neutral-500 hover:text-black underline text-[11px] truncate max-w-xs block">
                                                {{ parse_url($brand->website, PHP_URL_HOST) ?? $brand->website }}
                                            </a>
                                        @else
                                            <span class="text-neutral-400 italic">--</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono font-semibold text-neutral-800">
                                        {{ $brand->products_count }} items
                                    </td>
                                    <td class="p-4">
                                        @if($brand->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 text-neutral-500">
                                                Inactive
                                            </span>
                                        @endif
                                        @if($brand->is_featured)
                                            <span class="ml-1 text-[9px] font-bold text-amber-700 uppercase bg-amber-50 px-1.5 py-0.5 rounded">Featured</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    @click="editBrand = {
                                                        id: {{ $brand->id }},
                                                        name: '{{ addslashes($brand->name) }}',
                                                        website: '{{ addslashes($brand->website ?? '') }}',
                                                        logo_path: '{{ addslashes($brand->logo_path ?? '') }}',
                                                        description: '{{ addslashes($brand->description ?? '') }}',
                                                        is_featured: {{ $brand->is_featured ? 'true' : 'false' }},
                                                        is_active: {{ $brand->is_active ? 'true' : 'false' }}
                                                    }; editModalOpen = true;"
                                                    class="p-1.5 text-neutral-500 hover:text-black">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('Delete brand {{ $brand->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-neutral-400">No brands registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($brands->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $brands->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative" @click.outside="editModalOpen = false">
            <button type="button" @click="editModalOpen = false" class="absolute top-6 right-6 text-neutral-400 hover:text-black text-xl">&times;</button>
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 mb-6">Edit Brand</h3>

            <form :action="'/admin/brands/' + editBrand.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Brand Name *</label>
                    <input type="text" name="name" x-model="editBrand.name" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Website</label>
                    <input type="url" name="website" x-model="editBrand.website" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Logo URL</label>
                    <input type="text" name="logo_path" x-model="editBrand.logo_path" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Description</label>
                    <textarea name="description" x-model="editBrand.description" rows="3" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black"></textarea>
                </div>

                <div class="pt-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_featured" value="1" x-model="editBrand.is_featured" class="rounded border-neutral-300 text-black">
                        <span>Showcase on Homepage</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" x-model="editBrand.is_active" class="rounded border-neutral-300 text-black">
                        <span>Active</span>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-semibold text-neutral-600 hover:text-black">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
