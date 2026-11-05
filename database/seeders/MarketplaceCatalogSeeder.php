<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Top Brands Exist
        $brandsData = [
            'Aether Sound' => 'Haute acoustics and spatial sound systems.',
            'Chronos Labs' => 'Biometric precision horology and titanium timepieces.',
            'Luminary Optics' => 'German-engineered optical glass and full-frame sensors.',
            'Obsidian Desk' => 'Architectural aluminum workspace instruments.',
            'Nordic Audio' => 'Scandinavian minimalist acoustic spatial monitors.',
            'Atelier Couture' => 'Hand-stitched luxury apparel and bespoke outerwear.',
            'Florence Cordwainers' => 'Goodyear-welted Italian leather footwear.',
            'Kyoto Living' => 'Minimalist Japanese ceramics, teak furnishings, and ambient illumination.',
            'Aura Wellness' => 'Artisanal apothecary, rare botanicals, and dermatological care.',
            'Apex Velocity' => 'High-performance carbon sports apparatus and athletic recovery.',
        ];

        $brands = [];
        foreach ($brandsData as $name => $desc) {
            $slug = Str::slug($name);
            $brands[$slug] = Brand::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => $desc,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        // Available verified local product image assets
        $localImages = [
            '/storage/products/aether-one-anc-headphones/img-1-0.jpg',
            '/storage/products/aether-studio-reference-iems/img-1-0.jpg',
            '/storage/products/nordic-spatial-acoustic-soundbar/img-1-0.jpg',
            '/storage/products/vanguard-desktop-dac-tube-amplifier/img-1-0.jpg',
            '/storage/products/chronos-precision-smartwatch/img-11-0.jpg',
            '/storage/products/chronos-horizon-smart-ring/img-1-0.jpg',
            '/storage/products/bespoke-tuscan-leather-strap/img-1-0.jpg',
            '/storage/products/obsidian-precision-aluminum-stand/img-1-0.jpg',
            '/storage/products/apex-75-modular-mechanical-keyboard/img-1-0.jpg',
            '/storage/products/aeroform-leather-desk-blotter/img-1-0.jpg',
            '/storage/products/orbit-brass-cable-anchors/img-1-0.jpg',
            '/storage/products/luminary-horizon-4k-rangefinder-camera/img-1-0.jpg',
            '/storage/products/luminary-prime-35mm-cine-lens/img-1-0.jpg',
            '/storage/products/aura-dual-source-monitor-light-bar/img-1-0.jpg',
            '/storage/products/voltcraft-140w-gan-charging-station/img-1-0.jpg',
            '/storage/products/nomad-billet-laptop-power-bank/img-11-0.jpg',
            '/storage/products/kevlar-armor-magnetic-cable-trio/img-1-0.jpg',
            '/storage/products/aura-glass-ambient-diffuser/img-1-0.jpg',
            '/storage/categories/audio-acoustics.jpg',
            '/storage/categories/wearables.jpg',
            '/storage/categories/workspace-architecture.jpg',
            '/storage/categories/optics-cameras.jpg',
            '/storage/categories/power-mobility.jpg',
            '/storage/categories/smart-living.jpg',
        ];

        // 2. Comprehensive Multi-Category Product Catalog Definitions
        $catalog = [
            // FASHION & CLOTHING
            [
                'name' => 'Vanguard Sartorial Cashmere Overcoat',
                'slug' => 'vanguard-sartorial-cashmere-overcoat',
                'brand' => 'atelier-couture',
                'price' => 45999.00,
                'sale_price' => 39999.00,
                'short' => 'Double-faced Mongolian cashmere tailored for cold-weather elegance.',
                'desc' => 'Handcrafted in limited seasonal editions using 100% pure grade-A Mongolian cashmere. Features horn buttons, cupro lining, and precision structured shoulders.',
                'categories' => ['fashion-clothing', 'mens-fashion', 'luxury-fashion', 'winter-wear'],
                'image_idx' => 6,
                'hsn' => '6101',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Aurelia Pure Banarasi Zari Silk Saree',
                'slug' => 'aurelia-pure-banarasi-zari-silk-saree',
                'brand' => 'atelier-couture',
                'price' => 38500.00,
                'sale_price' => 34999.00,
                'short' => 'Authentic hand-loomed mulberry silk with electroplated real silver zari motifs.',
                'desc' => 'Woven by master artisans in Varanasi taking over 120 hours. Certified silk mark with certified silver content in zari weave.',
                'categories' => ['fashion-clothing', 'womens-fashion', 'ethnic-wear', 'bridal-wear', 'traditional-clothing'],
                'image_idx' => 7,
                'hsn' => '5007',
                'featured' => true,
                'bestseller' => false,
            ],
            [
                'name' => 'Aethelgard Raw Japanese Selvedge Denim',
                'slug' => 'aethelgard-raw-japanese-selvedge-denim',
                'brand' => 'atelier-couture',
                'price' => 14999.00,
                'sale_price' => null,
                'short' => '14.5oz shuttle-loomed Kurabo cotton denim with custom solid copper hardware.',
                'desc' => 'Unsanforized raw indigo denim woven on vintage Toyoda shuttle looms. Fades uniquely to the collector silhouette over years of wear.',
                'categories' => ['fashion-clothing', 'mens-fashion', 'western-wear'],
                'image_idx' => 8,
                'hsn' => '6203',
                'featured' => false,
                'bestseller' => true,
            ],
            [
                'name' => 'Astral Seamless Performance Compression Suit',
                'slug' => 'astral-seamless-performance-compression-suit',
                'brand' => 'apex-velocity',
                'price' => 8999.00,
                'sale_price' => 7499.00,
                'short' => 'Graduated ergonomic compression engineered for athletic speed and recovery.',
                'desc' => 'Polyamide elastane matrix with anti-microbial silver-ion thread technology for maximum breathability during marathon training.',
                'categories' => ['fashion-clothing', 'activewear', 'sportswear'],
                'image_idx' => 16,
                'hsn' => '6114',
                'featured' => false,
                'bestseller' => false,
            ],

            // FOOTWEAR
            [
                'name' => 'Florence Oxford Goodyear-Welted Leather Shoes',
                'slug' => 'florence-oxford-goodyear-welted-leather-shoes',
                'brand' => 'florence-cordwainers',
                'price' => 24999.00,
                'sale_price' => 21999.00,
                'short' => 'Full-grain French calfskin with bark-tanned leather sole and closed-channel stitching.',
                'desc' => 'Traditional 360-degree Goodyear welt construction that can be re-soled for a lifetime of formal use. Hand-burnished deep obsidian finish.',
                'categories' => ['footwear', 'mens-shoes', 'formal-shoes', 'luxury-footwear'],
                'image_idx' => 6,
                'hsn' => '6403',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'AeroStryke Carbon-Plated Marathon Runners',
                'slug' => 'aerostryke-carbon-plated-marathon-runners',
                'brand' => 'apex-velocity',
                'price' => 18999.00,
                'sale_price' => 16499.00,
                'short' => 'Curved full-length carbon fibre propulsion plate with supercritical nitrogen foam.',
                'desc' => 'Engineered for sub-3-hour marathoners. Offers 85% energy return with featherlight 195g racing chassis.',
                'categories' => ['footwear', 'sports-shoes', 'sneakers'],
                'image_idx' => 0,
                'hsn' => '6404',
                'featured' => true,
                'bestseller' => false,
            ],
            [
                'name' => 'Tuscan Suede Chelsea Boots',
                'slug' => 'tuscan-suede-chelsea-boots',
                'brand' => 'florence-cordwainers',
                'price' => 21500.00,
                'sale_price' => null,
                'short' => 'Italian water-repellent oiled suede with crepe rubber cushioned sole.',
                'desc' => 'Timeless equestrian silhouette with reinforced elasticated side gussets and pull tabs. Handcrafted in Tuscany.',
                'categories' => ['footwear', 'mens-shoes', 'boots', 'casual-shoes'],
                'image_idx' => 6,
                'hsn' => '6403',
                'featured' => false,
                'bestseller' => true,
            ],
            [
                'name' => 'Monolith Minimalist Nappa Leather Sneakers',
                'slug' => 'monolith-minimalist-nappa-leather-sneakers',
                'brand' => 'florence-cordwainers',
                'price' => 16499.00,
                'sale_price' => 13999.00,
                'short' => 'Buttery soft Italian nappa leather with Margom vulcanized rubber cupsole.',
                'desc' => 'No logos, no branding — pure geometric minimalism. Calfskin leather lining with memory foam insole.',
                'categories' => ['footwear', 'sneakers', 'casual-shoes', 'luxury-footwear'],
                'image_idx' => 7,
                'hsn' => '6403',
                'featured' => false,
                'bestseller' => true,
            ],

            // WATCHES & JEWELLERY
            [
                'name' => 'Chronos Apex Tourbillon Skeleton Timepiece',
                'slug' => 'chronos-apex-tourbillon-skeleton-timepiece',
                'brand' => 'chronos-labs',
                'price' => 145000.00,
                'sale_price' => 129999.00,
                'short' => 'Hand-wound flying tourbillon in grade-5 titanium with dual anti-reflective sapphire crystal.',
                'desc' => 'A triumph of micro-mechanics. 72-hour power reserve with exposed column wheel and guilloche anthracite bridge plates.',
                'categories' => ['watches-jewellery', 'luxury-watches', 'mens-watches'],
                'image_idx' => 4,
                'hsn' => '9102',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Solitaire 1.5ct Certified Lab Diamond Ring',
                'slug' => 'solitaire-15ct-certified-lab-diamond-ring',
                'brand' => 'atelier-couture',
                'price' => 89999.00,
                'sale_price' => 79999.00,
                'short' => 'IGI-certified VVS1 clarity, D colour oval brilliant diamond in 18K solid white gold.',
                'desc' => 'Conflict-free sustainable luxury. Set in an ultra-fine 4-prong architectural cathedral mount.',
                'categories' => ['watches-jewellery', 'diamond-jewellery', 'bridal-jewellery', 'gold-jewellery'],
                'image_idx' => 5,
                'hsn' => '7113',
                'featured' => true,
                'bestseller' => false,
            ],
            [
                'name' => 'Auric 18K Solid Gold Minimalist Cuban Link Chain',
                'slug' => 'auric-18k-solid-gold-minimalist-cuban-link-chain',
                'brand' => 'atelier-couture',
                'price' => 74500.00,
                'sale_price' => null,
                'short' => '22-inch solid yellow gold diamond-cut curb chain with security safety clasp.',
                'desc' => 'BIS Hallmarked 750 purity. Substantial 18g weight with mirror polished beveled facets.',
                'categories' => ['watches-jewellery', 'gold-jewellery', 'mens-jewellery'],
                'image_idx' => 5,
                'hsn' => '7113',
                'featured' => false,
                'bestseller' => true,
            ],

            // FASHION ACCESSORIES
            [
                'name' => 'Aero Titanium Polarized Aviator Sunglasses',
                'slug' => 'aero-titanium-polarized-aviator-sunglasses',
                'brand' => 'obsidian-desk',
                'price' => 16999.00,
                'sale_price' => 14499.00,
                'short' => 'Ultra-flexible beta-titanium wireframe with Zeiss polarized emerald glass lenses.',
                'desc' => 'Weighs only 16 grams. 100% UV400 protection with oleophobic and anti-scratch nano coatings.',
                'categories' => ['fashion-accessories', 'sunglasses', 'eyewear', 'mens-accessories'],
                'image_idx' => 11,
                'hsn' => '9004',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Verona Full-Grain Leather Executive Briefcase',
                'slug' => 'verona-full-grain-leather-executive-briefcase',
                'brand' => 'florence-cordwainers',
                'price' => 32500.00,
                'sale_price' => 28999.00,
                'short' => 'Hand-stitched vegetable-tanned bridle leather with padded 16-inch laptop chamber.',
                'desc' => 'Solid brass hardware with Swiss Amiet combination key lock. Designed to patina gracefully over decades.',
                'categories' => ['fashion-accessories', 'handbags', 'wallets', 'mens-accessories', 'bags-travel'],
                'image_idx' => 6,
                'hsn' => '4202',
                'featured' => false,
                'bestseller' => true,
            ],

            // ELECTRONICS & TECHNOLOGY
            [
                'name' => 'Aetherbook Pro 16 M3 Max Ultra-Precision Laptop',
                'slug' => 'aetherbook-pro-16-m3-max-ultra-precision-laptop',
                'brand' => 'obsidian-desk',
                'price' => 219999.00,
                'sale_price' => 199999.00,
                'short' => 'CNC machined unibody aluminium with 16-core CPU, 40-core GPU, and Liquid Retina XDR.',
                'desc' => 'The ultimate portable creative powerhouse. 64GB Unified Memory and 1TB PCIe Gen4 NVMe storage.',
                'categories' => ['electronics-technology', 'laptops', 'desktop-computers'],
                'image_idx' => 8,
                'hsn' => '8471',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Horizon 4K Cine Drone with Hasselblad Optical Gimbal',
                'slug' => 'horizon-4k-cine-drone-with-hasselblad-optical-gimbal',
                'brand' => 'luminary-optics',
                'price' => 112000.00,
                'sale_price' => null,
                'short' => 'Omnidirectional obstacle sensing with 46-minute flight time and 10-bit D-Log M.',
                'desc' => '4/3 CMOS Hasselblad camera recording 5.1K/50fps. 15km transmission range with O3+ protocol.',
                'categories' => ['electronics-technology', 'drones', 'cameras-photography'],
                'image_idx' => 11,
                'hsn' => '8806',
                'featured' => true,
                'bestseller' => false,
            ],
            [
                'name' => 'Zephyr OLED Curved 34-Inch Ultra-Wide Monitor',
                'slug' => 'zephyr-oled-curved-34-inch-ultra-wide-monitor',
                'brand' => 'obsidian-desk',
                'price' => 84999.00,
                'sale_price' => 76999.00,
                'short' => 'Quantum-dot OLED with 175Hz refresh, 0.03ms response, and 99.3% DCI-P3 color gamut.',
                'desc' => 'Perfect 1800R curve with true black infinite contrast ratio. USB-C 90W single-cable power delivery.',
                'categories' => ['electronics-technology', 'computer-accessories', 'gaming-accessories', 'televisions'],
                'image_idx' => 13,
                'hsn' => '8528',
                'featured' => false,
                'bestseller' => true,
            ],

            // HOME & LIVING
            [
                'name' => 'Eames Style Sculptural Top-Grain Leather Lounge Chair',
                'slug' => 'eames-style-sculptural-top-grain-leather-lounge-chair',
                'brand' => 'kyoto-living',
                'price' => 78999.00,
                'sale_price' => 69999.00,
                'short' => 'Molded walnut plywood shell with aniline black leather upholstery and matching ottoman.',
                'desc' => 'An icon of 20th-century modernism. Ergonomically calibrated for perpetual reading and audiophile auditioning.',
                'categories' => ['home-living', 'living-room-furniture', 'home-furniture'],
                'image_idx' => 17,
                'hsn' => '9401',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Lumina Ambient Alabaster Stone Architectural Floor Lamp',
                'slug' => 'lumina-ambient-alabaster-stone-architectural-floor-lamp',
                'brand' => 'kyoto-living',
                'price' => 26500.00,
                'sale_price' => null,
                'short' => 'Hand-turned solid Spanish alabaster with brushed unlacquered brass pedestal.',
                'desc' => 'Emits a soft celestial translucent glow. Integrated capacitive step-less touch dimmer with CRI 98 LED.',
                'categories' => ['home-living', 'home-lighting', 'home-decor'],
                'image_idx' => 13,
                'hsn' => '9405',
                'featured' => false,
                'bestseller' => false,
            ],
            [
                'name' => 'Kyoto Hand-Poured Ceramic Tea & Dining Set',
                'slug' => 'kyoto-hand-poured-ceramic-tea-dining-set',
                'brand' => 'kyoto-living',
                'price' => 14999.00,
                'sale_price' => 12499.00,
                'short' => '16-piece high-fire stoneware set with matte charcoal and reactive bronze glaze.',
                'desc' => 'Each piece is hand-thrown on the potter wheel in Gifu, Japan. Microwave, oven, and dishwasher safe.',
                'categories' => ['home-living', 'kitchen-dining', 'home-decor'],
                'image_idx' => 17,
                'hsn' => '6912',
                'featured' => false,
                'bestseller' => true,
            ],

            // LIFESTYLE & PERSONAL CARE
            [
                'name' => 'Oud Royal & Ambergris Extrait de Parfum (100ml)',
                'slug' => 'oud-royal-ambergris-extrait-de-parfum-100ml',
                'brand' => 'aura-wellness',
                'price' => 19500.00,
                'sale_price' => 16999.00,
                'short' => 'Concentrated 35% oil extrait featuring aged Assam agarwood and coastal gray ambergris.',
                'desc' => 'Macerated for 18 months. Complex notes of smoky birch tar, saffron, Bulgarian rose, and sensual woods.',
                'categories' => ['lifestyle-personal-care', 'perfumes-fragrances', 'personal-care'],
                'image_idx' => 17,
                'hsn' => '3303',
                'featured' => true,
                'bestseller' => true,
            ],
            [
                'name' => 'Botanical Stem-Cell Cellular Rejuvenation Serum',
                'slug' => 'botanical-stem-cell-cellular-rejuvenation-serum',
                'brand' => 'aura-wellness',
                'price' => 8999.00,
                'sale_price' => null,
                'short' => 'Swiss alpine rose stem cells and quadruple molecular-weight hyaluronic acid.',
                'desc' => 'Clinical dermatological elixir formulated to boost collagen synthesis and environmental barrier resilience.',
                'categories' => ['lifestyle-personal-care', 'skincare', 'beauty-makeup'],
                'image_idx' => 17,
                'hsn' => '3304',
                'featured' => false,
                'bestseller' => true,
            ],

            // SPORTS & FITNESS
            [
                'name' => 'Apex Aero Carbon Fibre 22-Speed Road Bicycle',
                'slug' => 'apex-aero-carbon-fibre-22-speed-road-bicycle',
                'brand' => 'apex-velocity',
                'price' => 185000.00,
                'sale_price' => 169999.00,
                'short' => 'Toray T800 monocoque carbon frame with Shimano Ultegra Di2 electronic shifting.',
                'desc' => 'Wind-tunnel optimized aerodynamic profile. Tubeless-ready 50mm carbon deep-section wheelset weighing 7.4kg.',
                'categories' => ['sports-fitness', 'cycling', 'outdoor-sports', 'sports-equipment'],
                'image_idx' => 1,
                'hsn' => '8712',
                'featured' => true,
                'bestseller' => false,
            ],
            [
                'name' => 'Smart Magnetic Adjustable Quick-Dial Dumbbells (Pair)',
                'slug' => 'smart-magnetic-adjustable-quick-dial-dumbbells-pair',
                'brand' => 'apex-velocity',
                'price' => 36999.00,
                'sale_price' => 31999.00,
                'short' => 'Adjusts seamlessly from 2kg to 32kg per dumbbell with a twist of the knurled metal handle.',
                'desc' => 'Replaces 16 pairs of standard weights in an ultra-compact footprint. Precision laser-cut steel plates.',
                'categories' => ['sports-fitness', 'gym-fitness', 'sports-equipment'],
                'image_idx' => 14,
                'hsn' => '9506',
                'featured' => false,
                'bestseller' => true,
            ],

            // KITCHEN, GROCERY & FOOD
            [
                'name' => 'Estate Arabica Single-Origin Specialty Coffee Box',
                'slug' => 'estate-arabica-single-origin-specialty-coffee-box',
                'brand' => 'kyoto-living',
                'price' => 3499.00,
                'sale_price' => null,
                'short' => 'Araku Valley micro-lot natural process beans roasted fresh to order with SCA score of 88+.',
                'desc' => 'Notes of wild blackberry, cacao nibs, and orange blossom. Sealed with degassing nitrogen flush valve.',
                'categories' => ['kitchen-grocery-food', 'tea-coffee', 'grocery', 'food-gourmet-products'],
                'image_idx' => 10,
                'hsn' => '0901',
                'featured' => false,
                'bestseller' => true,
            ],
            [
                'name' => 'Royal Kashmiri Mongra Grade-A+ Organic Saffron (10g)',
                'slug' => 'royal-kashmiri-mongra-grade-a-organic-saffron-10g',
                'brand' => 'kyoto-living',
                'price' => 4999.00,
                'sale_price' => 4499.00,
                'short' => '100% pure thick red stigma threads harvested by hand in Pampore with intense safranal aroma.',
                'desc' => 'GI Tagged certified crop. Packed in an air-tight light-shielded violet glass flacon.',
                'categories' => ['kitchen-grocery-food', 'organic-products', 'cooking-essentials'],
                'image_idx' => 10,
                'hsn' => '0910',
                'featured' => true,
                'bestseller' => false,
            ],

            // BAGS & TRAVEL
            [
                'name' => 'Aerospace Aluminum Cabin Trolley Suitcase',
                'slug' => 'aerospace-aluminum-cabin-trolley-suitcase',
                'brand' => 'obsidian-desk',
                'price' => 38999.00,
                'sale_price' => 34999.00,
                'short' => 'High-grade anodized aluminium chassis with dual TSA butterfly latches and silent 360 wheels.',
                'desc' => 'Indestructible protection for long-haul globe-trotters. Includes Italian leather baggage tags and compression organizers.',
                'categories' => ['bags-travel', 'suitcases-luggage', 'travel-bags', 'travel-essentials'],
                'image_idx' => 15,
                'hsn' => '4202',
                'featured' => true,
                'bestseller' => true,
            ],

            // AUTOMOTIVE
            [
                'name' => 'Smart AI Dual 4K Dashcam with Night Horizon Sensor',
                'slug' => 'smart-ai-dual-4k-dashcam-with-night-horizon-sensor',
                'brand' => 'luminary-optics',
                'price' => 19999.00,
                'sale_price' => 17499.00,
                'short' => 'Sony Starvis-2 sensor recording front 4K HDR and rear 1080p with 24h buffered parking guard.',
                'desc' => 'Integrated 5GHz Wi-Fi, GPS tracking, supercapacitor resilience to high cabin heat, and collision auto-lock.',
                'categories' => ['automotive', 'automotive-electronics', 'car-accessories'],
                'image_idx' => 11,
                'hsn' => '8525',
                'featured' => true,
                'bestseller' => true,
            ],

            // BABY, KIDS & TOYS
            [
                'name' => 'AeroGlide Carbon-Frame Modular Baby Stroller',
                'slug' => 'aeroglide-carbon-frame-modular-baby-stroller',
                'brand' => 'apex-velocity',
                'price' => 39999.00,
                'sale_price' => 34999.00,
                'short' => 'One-hand compact gravity fold stroller with all-wheel suspension and UPF 50+ canopy.',
                'desc' => 'Ultra-lightweight 5.8kg aerospace composite frame. Airline cabin overhead locker approved.',
                'categories' => ['baby-kids-toys', 'baby-gear', 'baby-products'],
                'image_idx' => 3,
                'hsn' => '8715',
                'featured' => false,
                'bestseller' => false,
            ],
        ];

        DB::transaction(function () use ($catalog, $brands, $localImages) {
            foreach ($catalog as $item) {
                $brand = $brands[$item['brand']] ?? null;

                $product = Product::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'brand_id' => $brand?->id,
                        'name' => $item['name'],
                        'base_sku' => 'ATOZ-' . strtoupper(Str::random(6)),
                        'barcode' => '890' . random_int(1000000000, 9999999999),
                        'price' => $item['price'],
                        'sale_price' => $item['sale_price'],
                        'cost_price' => round($item['price'] * 0.55, 2),
                        'short_description' => $item['short'],
                        'description' => $item['desc'],
                        'tax_rate' => 18.00,
                        'weight' => 1.25,
                        'weight_unit' => 'kg',
                        'is_variable' => false,
                        'status' => 'active',
                        'is_featured' => $item['featured'] ?? false,
                        'is_bestseller' => $item['bestseller'] ?? false,
                        'is_new_arrival' => true,
                        'seo_title' => "{$item['name']} — Buy Online | ATOZ GADGETZ",
                        'seo_description' => "Shop {$item['name']} online at ATOZ GADGETZ with 100% genuine warranty, white-glove insured delivery, and 2FA secure checkout.",
                    ]
                );

                // Attach to categories
                $catIds = Category::whereIn('slug', $item['categories'])->pluck('id')->toArray();
                if (!empty($catIds)) {
                    $product->categories()->syncWithoutDetaching($catIds);
                }

                // Add Primary Image
                $imgUrl = $localImages[$item['image_idx'] % count($localImages)];
                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image_path' => $imgUrl,
                    ],
                    [
                        'alt_text' => $product->name,
                        'sort_order' => 1,
                        'is_primary' => true,
                    ]
                );

                // Create Default Variant & In-Stock Inventory
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $product->base_sku . '-STD'],
                    [
                        'product_id' => $product->id,
                        'barcode' => $product->barcode,
                        'price_override' => null,
                        'sale_price_override' => null,
                        'status' => 'active',
                    ]
                );

                $inv = Inventory::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    [
                        'quantity' => random_int(18, 45),
                        'reserved_quantity' => 0,
                        'low_stock_threshold' => 5,
                    ]
                );

                InventoryMovement::create([
                    'inventory_id' => $inv->id,
                    'change_amount' => $inv->quantity,
                    'previous_qty' => 0,
                    'new_qty' => $inv->quantity,
                    'type' => 'purchase',
                    'notes' => 'Marketplace expansion initial intake',
                ]);
            }
        });
    }
}
