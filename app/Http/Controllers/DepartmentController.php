<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    /**
     * Display the full marketplace departments directory.
     */
    public function index()
    {
        $departments = Cache::remember('full_departments_directory_v2', 86400, function () {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => function ($q) {
                    $q->where('is_active', true)->withCount('products')->orderBy('sort_order');
                }])
                ->withCount('products')
                ->orderBy('sort_order')
                ->get();
        });

        // Fetch top featured products for each department (by department or its subcategories)
        $departmentProducts = [];
        foreach ($departments as $dept) {
            $catIds = $dept->children->pluck('id')->prepend($dept->id);
            $departmentProducts[$dept->id] = Product::active()
                ->whereHas('categories', function ($q) use ($catIds) {
                    $q->whereIn('categories.id', $catIds);
                })
                ->with(['brand', 'images'])
                ->take(4)
                ->get();
        }

        $totalProductsCount = Product::active()->count();
        $totalSubcategoriesCount = Category::whereNotNull('parent_id')->where('is_active', true)->count();

        return view('shop.departments', compact(
            'departments',
            'departmentProducts',
            'totalProductsCount',
            'totalSubcategoriesCount'
        ));
    }
}
