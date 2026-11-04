<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::active()
            ->with([
                'brand',
                'categories',
                'images.colorValue',
                'variants.attributeValues.attribute',
                'variants.inventory',
                'reviews' => function ($q) {
                    $q->latest();
                },
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // 1. Structure Color-Specific Galleries for Alpine.js
        $generalImages = [];
        $colorGalleries = [];

        foreach ($product->images as $img) {
            $item = [
                'id' => $img->id,
                'path' => $img->image_path,
                'alt' => $img->alt_text ?? $product->name,
                'is_primary' => $img->is_primary,
            ];

            if ($img->attribute_value_id) {
                $colorGalleries[$img->attribute_value_id][] = $item;
            } else {
                $generalImages[] = $item;
            }
        }

        // 2. Prepare Variants payload for Dynamic Variant Selector
        $variantsData = $product->variants->map(function ($v) {
            $attrMap = [];
            foreach ($v->attributeValues as $av) {
                if ($av->attribute) {
                    $attrMap[$av->attribute->code] = $av->id;
                }
            }

            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'barcode' => $v->barcode,
                'price' => (float) $v->effective_price,
                'formatted_price' => '₹' . number_format($v->effective_price, 2),
                'original_price' => ($v->sale_price_override && $v->price_override) ? '₹' . number_format($v->price_override, 2) : null,
                'stock' => $v->stock_quantity,
                'is_in_stock' => $v->is_in_stock,
                'attributes' => $attrMap,
                'summary' => $v->attributes_summary,
            ];
        });

        // 3. Extract distinct attributes for selection UI
        $attributeGroups = [];
        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $val) {
                if (!$val->attribute) continue;
                $code = $val->attribute->code;
                if (!isset($attributeGroups[$code])) {
                    $attributeGroups[$code] = [
                        'code' => $code,
                        'name' => $val->attribute->name,
                        'type' => $val->attribute->type,
                        'values' => collect(),
                    ];
                }
                if (!$attributeGroups[$code]['values']->contains('id', $val->id)) {
                    $attributeGroups[$code]['values']->push($val);
                }
            }
        }

        // 4. Related Products
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->with(['brand', 'images.colorValue'])
            ->take(4)
            ->get();

        return view('shop.product', compact(
            'product',
            'generalImages',
            'colorGalleries',
            'variantsData',
            'attributeGroups',
            'relatedProducts'
        ));
    }

    public function storeReview(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'author_name' => 'required|string|max:100',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|max:2000',
        ]);

        $data['product_id'] = $product->id;
        $data['user_id'] = auth()->id();
        $data['is_verified_purchase'] = auth()->check() ? auth()->user()->orders()->whereHas('items', function ($q) use ($product) {
            $q->where('product_id', $product->id);
        })->exists() : false;
        $data['is_approved'] = true;

        Review::create($data);

        return back()->with('success', 'Thank you! Your verified review has been published.');
    }
}
