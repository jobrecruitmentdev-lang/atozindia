<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $payload = Cache::remember('store_home_payload', 3600, function () {
            $heroBanners = Banner::active()->position('hero')->orderBy('sort_order')->get();
            $categories = Category::whereNull('parent_id')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->take(6)
                ->get();

            $featuredProducts = Product::active()
                ->with(['brand', 'images.colorValue', 'variants.attributeValues'])
                ->featured()
                ->take(8)
                ->get();

            $bestsellers = Product::active()
                ->with(['brand', 'images.colorValue', 'variants.attributeValues'])
                ->bestseller()
                ->take(8)
                ->get();

            $newArrivals = Product::active()
                ->with(['brand', 'images.colorValue', 'variants.attributeValues'])
                ->newArrival()
                ->take(8)
                ->get();

            $brands = Brand::where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->take(8)->get();

            return compact(
                'heroBanners',
                'categories',
                'featuredProducts',
                'bestsellers',
                'newArrivals',
                'brands'
            );
        });

        return view('shop.home', $payload);
    }
}
