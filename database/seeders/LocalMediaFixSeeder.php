<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalMediaFixSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Fix Categories & Category Images
            $chargingImg = storage_path('app/public/products/voltcraft-140w-gan-charging-station/img-1-0.jpg');
            if (file_exists($chargingImg)) {
                copy($chargingImg, storage_path('app/public/categories/power-mobility.jpg'));
            }

            $headphoneImg = storage_path('app/public/products/aether-one-anc-headphones/img-1-0.jpg');
            if (file_exists($headphoneImg)) {
                copy($headphoneImg, storage_path('app/public/categories/wireless-headphones.jpg'));
            }

            $earbudsImg = storage_path('app/public/products/aether-studio-reference-iems/img-1-0.jpg');
            if (file_exists($earbudsImg)) {
                copy($earbudsImg, storage_path('app/public/categories/true-wireless-earbuds.jpg'));
            }

            Category::where('slug', 'wireless-headphones')->update(['image_path' => '/storage/categories/wireless-headphones.jpg']);
            Category::where('slug', 'true-wireless-earbuds')->update(['image_path' => '/storage/categories/true-wireless-earbuds.jpg']);
            Category::where('slug', 'power-mobility')->update(['image_path' => '/storage/categories/power-mobility.jpg']);

            // 2. Fix Banners
            Banner::where('id', 1)->update([
                'desktop_image' => '/storage/banners/hero-banner-1.jpg',
                'mobile_image' => '/storage/banners/hero-banner-1.jpg'
            ]);
            Banner::where('id', 2)->update([
                'desktop_image' => '/storage/banners/hero-banner-2.jpg',
                'mobile_image' => '/storage/banners/hero-banner-2.jpg'
            ]);

            // 3. Purge all external Unsplash images from product_images
            $unsplashImages = ProductImage::where('image_path', 'like', '%unsplash.com%')->get();
            foreach ($unsplashImages as $img) {
                $img->delete();
            }

            // 4. Ensure products 1-5 have proper local product_images
            $productsToFix = [
                'aether-one-anc-headphones' => [
                    ['/storage/products/aether-one-anc-headphones/img-1-0.jpg', true, 0],
                    ['/storage/products/aether-one-anc-headphones/img-1-1.jpg', false, 1],
                    ['/storage/products/aether-one-anc-headphones/img-1-2.jpg', false, 2],
                    ['/storage/products/aether-one-anc-headphones/img-11-0.jpg', false, 3],
                    ['/storage/products/aether-one-anc-headphones/img-11-1.jpg', false, 4],
                    ['/storage/products/aether-one-anc-headphones/img-3-0.jpg', false, 5],
                ],
                'chronos-vii-titanium-timepiece' => [
                    ['/storage/products/chronos-precision-smartwatch/img-11-0.jpg', true, 0],
                    ['/storage/products/chronos-precision-smartwatch/img-11-1.jpg', false, 1],
                    ['/storage/products/chronos-precision-smartwatch/img-11-2.jpg', false, 2],
                ],
                'luminary-m5-minimal-rangefinder' => [
                    ['/storage/products/luminary-horizon-4k-rangefinder-camera/img-1-0.jpg', true, 0],
                    ['/storage/products/luminary-horizon-4k-rangefinder-camera/img-1-1.jpg', false, 1],
                ],
                'obsidian-gravity-magnetic-stand' => [
                    ['/storage/products/obsidian-precision-aluminum-stand/img-1-0.jpg', true, 0],
                    ['/storage/products/obsidian-precision-aluminum-stand/img-1-1.jpg', false, 1],
                    ['/storage/products/obsidian-precision-aluminum-stand/img-11-0.jpg', false, 2],
                ],
                'nordic-spatial-acoustic-soundbar' => [
                    ['/storage/products/nordic-spatial-acoustic-soundbar/img-1-0.jpg', true, 0],
                    ['/storage/products/nordic-spatial-acoustic-soundbar/img-1-1.jpg', false, 1],
                ],
            ];

            foreach ($productsToFix as $slug => $images) {
                $p = Product::where('slug', $slug)->first();
                if ($p) {
                    // Reset existing images if needed
                    ProductImage::where('product_id', $p->id)->where('image_path', 'like', '%unsplash.com%')->delete();

                    foreach ($images as $imgData) {
                        ProductImage::firstOrCreate(
                            [
                                'product_id' => $p->id,
                                'image_path' => $imgData[0],
                            ],
                            [
                                'is_primary' => $imgData[1],
                                'sort_order' => $imgData[2],
                                'alt_text' => $p->name,
                            ]
                        );
                    }

                    // Set first image as primary
                    $firstImg = ProductImage::where('product_id', $p->id)->orderBy('sort_order')->first();
                    if ($firstImg) {
                        ProductImage::where('product_id', $p->id)->update(['is_primary' => false]);
                        $firstImg->update(['is_primary' => true]);
                    }
                }
            }

            // 5. Ensure EVERY product has a primary image set
            $allProducts = Product::with('images')->get();
            foreach ($allProducts as $p) {
                if ($p->images->isNotEmpty()) {
                    $hasPrimary = $p->images->contains('is_primary', true);
                    if (!$hasPrimary) {
                        $p->images->first()->update(['is_primary' => true]);
                    }
                }
            }
        });
    }
}
