<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'children'])
            ->withCount('products')
            ->orderBy('sort_order')
            ->paginate(20);

        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }
        $data['slug'] = $slug;
        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_active'] = (bool) $request->boolean('is_active');

        $cat = Category::create($data);
        AuditLog::log('created', 'Category', $cat->id, null, $cat->toArray());

        return back()->with('success', "Category '{$cat->name}' created successfully.");
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_active'] = (bool) $request->boolean('is_active');

        $old = $category->toArray();
        $category->update($data);
        AuditLog::log('updated', 'Category', $category->id, $old, $category->toArray());

        return back()->with('success', "Category '{$category->name}' updated successfully.");
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();
        AuditLog::log('deleted', 'Category', $category->id);

        return back()->with('success', "Category '{$name}' deleted.");
    }
}
