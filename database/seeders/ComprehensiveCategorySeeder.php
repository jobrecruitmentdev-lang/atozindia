<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComprehensiveCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxonomy = [
            [
                'name' => 'Fashion & Clothing',
                'slug' => 'fashion-clothing',
                'description' => 'Curated luxury apparel, bespoke couture, ethnic weaves, and contemporary global wardrobe.',
                'is_featured' => true,
                'subcategories' => [
                    "Men's Fashion",
                    "Women's Fashion",
                    "Kids' Fashion",
                    "Baby Fashion",
                    "Unisex Fashion",
                    "Ethnic Wear",
                    "Western Wear",
                    "Luxury Fashion",
                    "Designer Fashion",
                    "Plus Size Fashion",
                    "Sportswear",
                    "Activewear",
                    "Innerwear & Lingerie",
                    "Sleepwear & Nightwear",
                    "Winter Wear",
                    "Summer Wear",
                    "Maternity Wear",
                    "Bridal Wear",
                    "Party Wear",
                    "Traditional Clothing",
                ],
            ],
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Handcrafted cordwainery, precision engineered athletic trainers, and artisanal leather shoes.',
                'is_featured' => true,
                'subcategories' => [
                    "Men's Shoes",
                    "Women's Shoes",
                    "Kids' Shoes",
                    "Sneakers",
                    "Sports Shoes",
                    "Casual Shoes",
                    "Formal Shoes",
                    "Boots",
                    "Sandals & Slippers",
                    "Luxury Footwear",
                ],
            ],
            [
                'name' => 'Watches & Jewellery',
                'slug' => 'watches-jewellery',
                'description' => 'Haute horology timepieces, fine certified diamond adornments, and precious metals.',
                'is_featured' => true,
                'subcategories' => [
                    "Men's Watches",
                    "Women's Watches",
                    "Luxury Watches",
                    "Smartwatches",
                    "Fitness Bands",
                    "Men's Jewellery",
                    "Women's Jewellery",
                    "Gold Jewellery",
                    "Silver Jewellery",
                    "Diamond Jewellery",
                    "Fashion Jewellery",
                    "Bridal Jewellery",
                    "Couple Jewellery",
                ],
            ],
            [
                'name' => 'Fashion Accessories',
                'slug' => 'fashion-accessories',
                'description' => 'Designer eyewear, Italian calfskin belts, silk scarves, and sartorial accents.',
                'is_featured' => false,
                'subcategories' => [
                    "Men's Accessories",
                    "Women's Accessories",
                    "Sunglasses",
                    "Eyewear",
                    "Handbags",
                    "Backpacks",
                    "Wallets",
                    "Belts",
                    "Caps & Hats",
                    "Scarves & Stoles",
                    "Hair Accessories",
                    "Travel Accessories",
                    "Fashion Accessories Collection",
                ],
            ],
            [
                'name' => 'Electronics & Technology',
                'slug' => 'electronics-technology',
                'description' => 'State-of-the-art computational hardware, flagship smartphones, audiophile systems, and smart automation.',
                'is_featured' => true,
                'subcategories' => [
                    "Mobile Phones",
                    "Mobile Accessories",
                    "Laptops",
                    "Desktop Computers",
                    "Tablets",
                    "Computer Accessories",
                    "Gaming Accessories",
                    "Gaming Consoles",
                    "Televisions",
                    "Audio & Speakers",
                    "Headphones & Earphones",
                    "Cameras & Photography",
                    "Drones",
                    "Smart Home Devices",
                    "Networking Devices",
                    "Storage Devices",
                    "Printers & Scanners",
                    "Electronic Gadgets",
                    "Wearable Technology",
                ],
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Architectural interior decor, designer furniture, ambient illumination, and bespoke tableware.',
                'is_featured' => true,
                'subcategories' => [
                    "Home Decor",
                    "Home Furnishing",
                    "Home Furniture",
                    "Living Room Furniture",
                    "Bedroom Furniture",
                    "Office Furniture",
                    "Kitchen & Dining",
                    "Kitchen Appliances",
                    "Home Appliances",
                    "Bathroom Accessories",
                    "Home Storage & Organization",
                    "Home Improvement",
                    "Home Essentials",
                    "Home Lighting",
                    "Smart Home & Automation",
                ],
            ],
            [
                'name' => 'Lifestyle & Personal Care',
                'slug' => 'lifestyle-personal-care',
                'description' => 'Artisanal fragrances, clean dermatological skincare, luxury apothecary, and wellness grooming.',
                'is_featured' => false,
                'subcategories' => [
                    "Beauty & Makeup",
                    "Skincare",
                    "Haircare",
                    "Personal Care",
                    "Men's Grooming",
                    "Women's Grooming",
                    "Perfumes & Fragrances",
                    "Bath & Body",
                    "Beauty Tools",
                    "Health & Wellness",
                ],
            ],
            [
                'name' => 'Sports & Fitness',
                'slug' => 'sports-fitness',
                'description' => 'Commercial-grade athletic apparatus, performance cycling, yoga sanctuaries, and wellness recovery.',
                'is_featured' => false,
                'subcategories' => [
                    "Sports Equipment",
                    "Gym & Fitness",
                    "Yoga & Meditation",
                    "Outdoor Sports",
                    "Indoor Games",
                    "Cycling",
                    "Running Accessories",
                    "Camping & Hiking",
                    "Swimming Accessories",
                    "Sports Nutrition",
                ],
            ],
            [
                'name' => 'Baby, Kids & Toys',
                'slug' => 'baby-kids-toys',
                'description' => 'Organic newborn care, educational STEM discoveries, nursery furnishings, and family board games.',
                'is_featured' => false,
                'subcategories' => [
                    "Baby Products",
                    "Baby Care",
                    "Baby Feeding",
                    "Baby Gear",
                    "Kids Toys",
                    "Educational Toys",
                    "Board Games & Puzzles",
                    "Kids School Supplies",
                    "Kids Furniture",
                    "Kids Room Decor",
                ],
            ],
            [
                'name' => 'Kitchen, Grocery & Food',
                'slug' => 'kitchen-grocery-food',
                'description' => 'Estate single-origin roasts, organic harvest staples, culinary instruments, and artisanal confectionery.',
                'is_featured' => false,
                'subcategories' => [
                    "Grocery",
                    "Organic Products",
                    "Packaged Food",
                    "Snacks & Beverages",
                    "Dry Fruits & Nuts",
                    "Tea & Coffee",
                    "Chocolates & Sweets",
                    "Cooking Essentials",
                    "Kitchen Tools",
                    "Food & Gourmet Products",
                ],
            ],
            [
                'name' => 'Bags & Travel',
                'slug' => 'bags-travel',
                'description' => 'Aerospace aluminium hard-shell luggage, ballistic nylon weekender duffels, and executive folios.',
                'is_featured' => false,
                'subcategories' => [
                    "Travel Bags",
                    "Suitcases & Luggage",
                    "Laptop Bags",
                    "School Bags",
                    "Office Bags",
                    "Handbags & Purses",
                    "Gym Bags",
                    "Travel Essentials",
                ],
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Electric mobility accessories, ceramic vehicle care, automotive electronics, and bespoke cockpit refinements.',
                'is_featured' => false,
                'subcategories' => [
                    "Car Accessories",
                    "Bike Accessories",
                    "Car Care Products",
                    "Automotive Electronics",
                    "Automotive Parts",
                    "Two-Wheeler Accessories",
                    "EV Accessories",
                    "Automobile Lifestyle Products",
                ],
            ],
            [
                'name' => 'Books, Education & Stationery',
                'slug' => 'books-education-stationery',
                'description' => 'Limited edition monographs, fountain pen stationery, archival art supplies, and musical instruments.',
                'is_featured' => false,
                'subcategories' => [
                    "Books",
                    "E-Books",
                    "Educational Products",
                    "Office Supplies",
                    "Stationery",
                    "Art & Craft Supplies",
                    "Musical Instruments",
                ],
            ],
            [
                'name' => 'Garden & Outdoor Living',
                'slug' => 'garden-outdoor-living',
                'description' => 'Teak patio architecture, botanical specimens, ambient firecraft, and precision gardening instruments.',
                'is_featured' => false,
                'subcategories' => [
                    "Plants & Gardening",
                    "Garden Accessories",
                    "Outdoor Furniture",
                    "Outdoor Lighting",
                    "Gardening Tools",
                    "Outdoor Living Spaces",
                ],
            ],
            [
                'name' => 'Pets',
                'slug' => 'pets',
                'description' => 'Heritage leather harnesses, veterinary organic nutrition, comfort bedding, and marine aquascaping.',
                'is_featured' => false,
                'subcategories' => [
                    "Pet Supplies",
                    "Dog Products",
                    "Cat Products",
                    "Pet Food",
                    "Pet Accessories",
                    "Aquarium & Fish Supplies",
                ],
            ],
            [
                'name' => 'Gifts & Special Collections',
                'slug' => 'gifts-special-collections',
                'description' => 'Bespoke corporate hampers, monogrammed heirlooms, festive ceremonial artifacts, and vintage rarities.',
                'is_featured' => false,
                'subcategories' => [
                    "Gifts & Hampers",
                    "Personalized Gifts",
                    "Wedding Gifts",
                    "Corporate Gifts",
                    "Handmade Products",
                    "Collectibles & Antiques",
                    "Festival & Celebration Products",
                    "Religious & Spiritual Products",
                ],
            ],
            [
                'name' => 'Tools & Industrial',
                'slug' => 'tools-industrial',
                'description' => 'German engineered hand tools, precision metrology, industrial PPE gear, and professional machinery.',
                'is_featured' => false,
                'subcategories' => [
                    "Hardware & Tools",
                    "Industrial Supplies",
                    "Electrical Supplies",
                    "Plumbing Supplies",
                    "Construction Materials",
                    "Safety Equipment",
                    "Machinery & Equipment",
                ],
            ],
            [
                'name' => 'Additional Shopping Categories',
                'slug' => 'additional-shopping-categories',
                'description' => 'Curated spotlight edits, certified circular refurbished rarities, and weekly atelier discoveries.',
                'is_featured' => false,
                'subcategories' => [
                    "Premium & Luxury Products",
                    "Eco-Friendly Products",
                    "Sustainable Products",
                    "Handmade & Artisan Products",
                    "Refurbished Products",
                    "Second-Hand Products",
                    "Seasonal Collections",
                    "Trending Products",
                    "New Arrivals",
                    "Best Sellers",
                ],
            ],
        ];

        DB::transaction(function () use ($taxonomy) {
            $sortOrder = 1;

            foreach ($taxonomy as $parentData) {
                $parentSlug = $parentData['slug'];

                // 1. Create or update Parent Category
                $parent = Category::updateOrCreate(
                    ['slug' => $parentSlug],
                    [
                        'parent_id' => null,
                        'name' => $parentData['name'],
                        'description' => $parentData['description'],
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                        'is_featured' => $parentData['is_featured'] ?? false,
                        'seo_title' => "Shop {$parentData['name']} Online — ATOZ GADGETZ",
                        'seo_description' => "Discover authentic luxury {$parentData['name']} with nationwide express white-glove delivery, genuine warranties, and concierge support.",
                    ]
                );

                // 2. Create or update Subcategories
                $subSort = 1;
                foreach ($parentData['subcategories'] as $subName) {
                    $baseSlug = Str::slug($subName);

                    // Check if slug is already used by another record (not this specific sub under parent)
                    $existingSub = Category::where('parent_id', $parent->id)->where('name', $subName)->first();
                    $candidateSlug = $baseSlug;

                    if ($candidateSlug === $parentSlug) {
                        $candidateSlug = $baseSlug . '-items';
                    }

                    // Ensure absolute uniqueness across the table
                    $query = Category::where('slug', $candidateSlug);
                    if ($existingSub) {
                        $query->where('id', '!=', $existingSub->id);
                    }
                    if ($query->exists()) {
                        $candidateSlug = $baseSlug . '-' . Str::slug($parentData['name']);
                        
                        $query2 = Category::where('slug', $candidateSlug);
                        if ($existingSub) {
                            $query2->where('id', '!=', $existingSub->id);
                        }
                        if ($query2->exists()) {
                            $candidateSlug = $baseSlug . '-' . Str::random(4);
                        }
                    }

                    if ($existingSub) {
                        $existingSub->update([
                            'slug' => $candidateSlug,
                            'description' => "Explore {$subName} in our {$parentData['name']} collection with premium quality guarantee.",
                            'sort_order' => $subSort++,
                            'is_active' => true,
                            'is_featured' => false,
                            'seo_title' => "Buy {$subName} Online — {$parentData['name']} | ATOZ GADGETZ",
                            'seo_description' => "Shop the finest selection of {$subName} online at ATOZ GADGETZ. Verified authentic instruments, express insured transit, and premium warranties.",
                        ]);
                    } else {
                        Category::create([
                            'parent_id' => $parent->id,
                            'name' => $subName,
                            'slug' => $candidateSlug,
                            'description' => "Explore {$subName} in our {$parentData['name']} collection with premium quality guarantee.",
                            'sort_order' => $subSort++,
                            'is_active' => true,
                            'is_featured' => false,
                            'seo_title' => "Buy {$subName} Online — {$parentData['name']} | ATOZ GADGETZ",
                            'seo_description' => "Shop the finest selection of {$subName} online at ATOZ GADGETZ. Verified authentic instruments, express insured transit, and premium warranties.",
                        ]);
                    }
                }
            }

            // 3. Smart Category Mapping for existing 21 Products
            $this->mapExistingProductsToNewCategories();

            // 4. Retire old legacy categories so exactly 18 Departments are active at root
            Category::whereIn('slug', [
                'audio-acoustics',
                'wearables',
                'workspace-architecture',
                'optics-cameras',
                'power-mobility',
                'smart-living',
                'wireless-headphones',
                'true-wireless-earbuds',
            ])->update(['is_active' => false]);
        });
    }

    /**
     * Map existing 21 curated products to their appropriate new subcategories.
     */
    protected function mapExistingProductsToNewCategories(): void
    {
        $categoryMap = [
            'audio-speakers' => Category::where('slug', 'audio-speakers')->value('id'),
            'headphones-earphones' => Category::where('slug', 'headphones-earphones')->value('id'),
            'smartwatches' => Category::where('slug', 'smartwatches')->value('id'),
            'computer-accessories' => Category::where('slug', 'computer-accessories')->value('id'),
            'gaming-accessories' => Category::where('slug', 'gaming-accessories')->value('id'),
            'cameras-photography' => Category::where('slug', 'cameras-photography')->value('id'),
            'mobile-accessories' => Category::where('slug', 'mobile-accessories')->value('id'),
            'smart-home-devices' => Category::where('slug', 'smart-home-devices')->value('id'),
            'wearable-technology' => Category::where('slug', 'wearable-technology')->value('id'),
            'electronic-gadgets' => Category::where('slug', 'electronic-gadgets')->value('id'),
            'luxury-watches' => Category::where('slug', 'luxury-watches')->value('id'),
            'mens-accessories' => Category::where('slug', 'mens-accessories')->value('id'),
            'home-lighting' => Category::where('slug', 'home-lighting')->value('id'),
            'home-decor' => Category::where('slug', 'home-decor')->value('id'),
            'office-furniture' => Category::where('slug', 'office-furniture')->value('id'),
            'smart-home-automation' => Category::where('slug', 'smart-home-automation')->value('id'),
        ];

        $productAssignments = [
            'aether-one-anc-headphones' => [$categoryMap['headphones-earphones'], $categoryMap['audio-speakers']],
            'chronos-vii-titanium-timepiece' => [$categoryMap['smartwatches'], $categoryMap['luxury-watches'], $categoryMap['wearable-technology']],
            'luminary-m5-minimal-rangefinder' => [$categoryMap['cameras-photography']],
            'obsidian-gravity-magnetic-stand' => [$categoryMap['mobile-accessories'], $categoryMap['computer-accessories']],
            'nordic-spatial-acoustic-soundbar' => [$categoryMap['audio-speakers'], $categoryMap['smart-home-devices']],
            'aether-studio-reference-iems' => [$categoryMap['headphones-earphones'], $categoryMap['audio-speakers']],
            'vanguard-desktop-dac-tube-amplifier' => [$categoryMap['audio-speakers'], $categoryMap['computer-accessories']],
            'chronos-precision-smartwatch' => [$categoryMap['smartwatches'], $categoryMap['luxury-watches'], $categoryMap['wearable-technology']],
            'chronos-horizon-smart-ring' => [$categoryMap['smartwatches'], $categoryMap['wearable-technology'], $categoryMap['electronic-gadgets']],
            'bespoke-tuscan-leather-strap' => [$categoryMap['luxury-watches'], $categoryMap['mens-accessories']],
            'obsidian-precision-aluminum-stand' => [$categoryMap['mobile-accessories'], $categoryMap['computer-accessories']],
            'apex-75-modular-mechanical-keyboard' => [$categoryMap['computer-accessories'], $categoryMap['gaming-accessories']],
            'aeroform-leather-desk-blotter' => [$categoryMap['computer-accessories'], $categoryMap['office-furniture']],
            'orbit-brass-cable-anchors' => [$categoryMap['computer-accessories'], $categoryMap['electronic-gadgets']],
            'luminary-horizon-4k-rangefinder-camera' => [$categoryMap['cameras-photography']],
            'luminary-prime-35mm-cine-lens' => [$categoryMap['cameras-photography']],
            'aura-dual-source-monitor-light-bar' => [$categoryMap['computer-accessories'], $categoryMap['home-lighting']],
            'voltcraft-140w-gan-charging-station' => [$categoryMap['mobile-accessories'], $categoryMap['computer-accessories']],
            'nomad-billet-laptop-power-bank' => [$categoryMap['mobile-accessories'], $categoryMap['computer-accessories']],
            'kevlar-armor-magnetic-cable-trio' => [$categoryMap['mobile-accessories'], $categoryMap['computer-accessories']],
            'aura-glass-ambient-diffuser' => [$categoryMap['smart-home-automation'], $categoryMap['home-decor']],
        ];

        foreach ($productAssignments as $productSlug => $categoryIds) {
            $product = Product::where('slug', $productSlug)->first();
            if ($product) {
                $validCategoryIds = array_filter($categoryIds);
                if (!empty($validCategoryIds)) {
                    $product->categories()->syncWithoutDetaching($validCategoryIds);
                }
            }
        }
    }
}
