<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['brand', 'categories', 'images.colorValue', 'variants.attributeValues']);

        // Search Keyword
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('base_sku', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhereHas('brand', function ($b) use ($q) {
                        $b->where('name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('categories', function ($c) use ($q) {
                        $c->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $catSlug = $request->category;
            $query->whereHas('categories', function ($c) use ($catSlug) {
                $c->where('slug', $catSlug)
                    ->orWhereHas('parent', function ($p) use ($catSlug) {
                        $p->where('slug', $catSlug);
                    });
            });
        }

        // Brand Filter
        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('brand', function ($b) use ($brands) {
                $b->whereIn('slug', $brands);
            });
        }

        // Price Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Colour Filter
        if ($request->filled('color')) {
            $colors = is_array($request->color) ? $request->color : [$request->color];
            $query->where(function ($w) use ($colors) {
                $w->whereHas('images.colorValue', function ($cv) use ($colors) {
                    $cv->whereIn('value', $colors);
                })->orWhereHas('variants.attributeValues', function ($av) use ($colors) {
                    $av->whereIn('value', $colors);
                });
            });
        }

        // In Stock Only
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants.inventory', function ($inv) {
                $inv->where('quantity', '>', 0)->orWhere('allow_backorders', true);
            });
        }

        // Discount Filter
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'bestseller':
                $query->orderBy('is_bestseller', 'desc')->latest();
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->latest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Data for Filters
        $allCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->withCount('products');
            }])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        $allBrands = Brand::where('is_active', true)->withCount('products')->orderBy('name')->get();

        $availableColors = AttributeValue::whereHas('attribute', function ($q) {
            $q->where('type', 'color');
        })->whereNotNull('color_hex')->get();

        $priceMin = (float) Product::active()->min('price') ?: 0;
        $priceMax = (float) Product::active()->max('price') ?: 100000;

        return view('shop.index', compact(
            'products',
            'allCategories',
            'allBrands',
            'availableColors',
            'priceMin',
            'priceMax',
            'sort'
        ));
    }

    /**
     * Live search autocomplete endpoint.
     */
    public function suggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['products' => [], 'categories' => [], 'brands' => []]);
        }

        $products = Product::active()
            ->with('brand')
            ->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('base_sku', 'like', "%{$q}%");
            })
            ->take(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand' => $p->brand?->name,
                    'slug' => $p->slug,
                    'price' => '₹' . number_format($p->effective_price, 2),
                    'original_price' => $p->sale_price ? '₹' . number_format($p->price, 2) : null,
                    'image' => $p->primary_image,
                    'url' => route('product.show', $p->slug),
                ];
            });

        $categories = Category::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($c) {
                return [
                    'name' => $c->name,
                    'url' => route('shop.index', ['category' => $c->slug]),
                ];
            });

        $brands = Brand::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($b) {
                return [
                    'name' => $b->name,
                    'url' => route('shop.index', ['brand' => $b->slug]),
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
