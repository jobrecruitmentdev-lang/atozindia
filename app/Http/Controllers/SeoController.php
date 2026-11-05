<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic XML Sitemap for search engines.
     */
    public function sitemap(): Response
    {
        $products = Product::where('status', 'active')
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->get();

        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $brands = Brand::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $xml = view('seo.sitemap', compact('products', 'categories', 'brands'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * Dynamic robots.txt fallback if routed through PHP.
     */
    public function robots(): Response
    {
        $content = "User-agent: *\n"
            . "Allow: /\n"
            . "Disallow: /admin/\n"
            . "Disallow: /cart\n"
            . "Disallow: /checkout\n"
            . "Disallow: /account/\n\n"
            . "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
