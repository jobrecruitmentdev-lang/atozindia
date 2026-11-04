<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('name')->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'website' => 'nullable|url|max:255',
            'logo_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Brand::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }
        $data['slug'] = $slug;
        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_active'] = (bool) $request->boolean('is_active');

        $brand = Brand::create($data);
        AuditLog::log('created', 'Brand', $brand->id, null, $brand->toArray());

        return back()->with('success', "Brand '{$brand->name}' created successfully.");
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'website' => 'nullable|url|max:255',
            'logo_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_featured'] = (bool) $request->boolean('is_featured');
        $data['is_active'] = (bool) $request->boolean('is_active');

        $old = $brand->toArray();
        $brand->update($data);
        AuditLog::log('updated', 'Brand', $brand->id, $old, $brand->toArray());

        return back()->with('success', "Brand '{$brand->name}' updated successfully.");
    }

    public function destroy(Brand $brand)
    {
        $name = $brand->name;
        $brand->delete();
        AuditLog::log('deleted', 'Brand', $brand->id);

        return back()->with('success', "Brand '{$name}' deleted.");
    }
}
