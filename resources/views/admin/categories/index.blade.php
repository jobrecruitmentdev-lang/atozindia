@extends('layouts.admin')

@section('page_title', 'Category Architecture')
@section('page_subtitle', 'Curate parent departments, subcategories, imagery, and homepage feature flags')

@section('content')
<div class="space-y-8" x-data="{ 
    editModalOpen: false, 
    editCategory: { id: null, name: '', parent_id: '', description: '', image_path: '', sort_order: 0, is_featured: false, is_active: true } 
}">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Add Category Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm h-fit">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3 mb-6">
                Create New Category
            </h3>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Acoustic & Sound" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Parent Category (Optional)</label>
                    <select name="parent_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        <option value="">-- None (Top Level Department) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Editorial Description</label>
                    <textarea name="description" rows="3" placeholder="Brief summary for category headers" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Image / Banner URL</label>
                    <input type="text" name="image_path" placeholder="https://... or /assets/..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Display Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="pt-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-neutral-300 text-black">
                        <span>Show on Homepage Spotlight</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-neutral-300 text-black">
                        <span>Active (Publicly navigable)</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                    Create Category
                </button>
            </form>
        </div>

        <!-- Right Column: Categories Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Categories Directory ({{ $categories->total() }})</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Hierarchy and live product counts across the store.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Name & Slug</th>
                                <th class="p-4">Parent Dept</th>
                                <th class="p-4">Products</th>
                                <th class="p-4">Sort</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($categories as $category)
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-neutral-900">{{ $category->name }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono">{{ $category->slug }}</div>
                                    </td>
                                    <td class="p-4 text-neutral-600">
                                        @if($category->parent)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 text-neutral-800">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="text-neutral-400 italic">Top Level</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono font-semibold text-neutral-800">
                                        {{ $category->products_count }} items
                                    </td>
                                    <td class="p-4 font-mono text-neutral-500">
                                        {{ $category->sort_order }}
                                    </td>
                                    <td class="p-4">
                                        @if($category->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 text-neutral-500">
                                                Hidden
                                            </span>
                                        @endif
                                        @if($category->is_featured)
                                            <span class="ml-1 text-[9px] font-bold text-amber-700 uppercase bg-amber-50 px-1.5 py-0.5 rounded">Featured</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    @click="editCategory = {
                                                        id: {{ $category->id }},
                                                        name: '{{ addslashes($category->name) }}',
                                                        parent_id: '{{ $category->parent_id ?? '' }}',
                                                        description: '{{ addslashes($category->description ?? '') }}',
                                                        image_path: '{{ addslashes($category->image_path ?? '') }}',
                                                        sort_order: {{ $category->sort_order }},
                                                        is_featured: {{ $category->is_featured ? 'true' : 'false' }},
                                                        is_active: {{ $category->is_active ? 'true' : 'false' }}
                                                    }; editModalOpen = true;"
                                                    class="p-1.5 text-neutral-500 hover:text-black">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete category {{ $category->name }}?');">
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
                                    <td colspan="6" class="p-8 text-center text-neutral-400">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($categories->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative" @click.outside="editModalOpen = false">
            <button type="button" @click="editModalOpen = false" class="absolute top-6 right-6 text-neutral-400 hover:text-black text-xl">&times;</button>
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 mb-6">Edit Category</h3>

            <form :action="'/admin/categories/' + editCategory.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Category Name *</label>
                    <input type="text" name="name" x-model="editCategory.name" required class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Parent Category</label>
                    <select name="parent_id" x-model="editCategory.parent_id" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                        <option value="">-- None (Top Level) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Description</label>
                    <textarea name="description" x-model="editCategory.description" rows="3" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Image / Banner URL</label>
                    <input type="text" name="image_path" x-model="editCategory.image_path" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" x-model="editCategory.sort_order" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="pt-2 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_featured" value="1" x-model="editCategory.is_featured" class="rounded border-neutral-300 text-black">
                        <span>Show on Homepage Spotlight</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" x-model="editCategory.is_active" class="rounded border-neutral-300 text-black">
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
