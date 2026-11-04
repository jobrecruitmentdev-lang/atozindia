<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Brands
        $brands = [
            ['name' => 'Aether Sound', 'slug' => 'aether-sound', 'description' => 'Acoustic engineering and timeless industrial design.'],
            ['name' => 'Chronos Labs', 'slug' => 'chronos-labs', 'description' => 'Precision biometric micro-electronics and luxury titanium timepieces.'],
            ['name' => 'Luminary Optics', 'slug' => 'luminary-optics', 'description' => 'German-engineered glass optics and minimal sensors.'],
            ['name' => 'Obsidian Desk', 'slug' => 'obsidian-desk', 'description' => 'Architectural workspace essentials crafted from aerospace aluminum.'],
            ['name' => 'Nordic Audio', 'slug' => 'nordic-audio', 'description' => 'Scandinavian minimalism tuned for spatial sound precision.'],
        ];

        $brandModels = [];
        foreach ($brands as $b) {
            $brandModels[$b['slug']] = Brand::updateOrCreate(['slug' => $b['slug']], array_merge($b, [
                'is_featured' => true,
                'is_active' => true,
            ]));
        }

        // 2. Create Categories & Nested Subcategories
        $catAudio = Category::updateOrCreate(['slug' => 'audio-acoustics'], [
            'name' => 'Audio & Acoustics',
            'description' => 'Studio-grade headphones, earbuds, and spatial soundbars.',
            'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $catEarphones = Category::updateOrCreate(['slug' => 'wireless-headphones'], [
            'parent_id' => $catAudio->id,
            'name' => 'Over-Ear Headphones',
            'description' => 'Noise-cancelling circumaural headphones.',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $catEarbuds = Category::updateOrCreate(['slug' => 'true-wireless-earbuds'], [
            'parent_id' => $catAudio->id,
            'name' => 'True Wireless Earbuds',
            'description' => 'Ultra-compact high-fidelity in-ear monitors.',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $catWearables = Category::updateOrCreate(['slug' => 'wearables'], [
            'name' => 'Wearables & Timepieces',
            'description' => 'Titanium biometric smartwatches and smart rings.',
            'image_path' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $catDesk = Category::updateOrCreate(['slug' => 'workspace-architecture'], [
            'name' => 'Desk Architecture',
            'description' => 'Magnetic docks, mechanical boards, and precision stands.',
            'image_path' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $catOptics = Category::updateOrCreate(['slug' => 'optics-cameras'], [
            'name' => 'Optics & Visuals',
            'description' => 'Rangefinder cameras and 5K minimalist studio monitors.',
            'image_path' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // 3. Create Attributes
        $attrColor = Attribute::updateOrCreate(['code' => 'color'], [
            'name' => 'Colour',
            'type' => 'color',
        ]);

        $attrStorage = Attribute::updateOrCreate(['code' => 'storage'], [
            'name' => 'Storage',
            'type' => 'button',
        ]);

        $attrSize = Attribute::updateOrCreate(['code' => 'size'], [
            'name' => 'Size',
            'type' => 'button',
        ]);

        // Attribute Values
        $valObsidian = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Obsidian Black'], [
            'color_hex' => '#111110',
            'sort_order' => 1,
        ]);

        $valSilver = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Alabaster Silver'], [
            'color_hex' => '#E5E7EB',
            'sort_order' => 2,
        ]);

        $valNavy = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Midnight Navy'], [
            'color_hex' => '#1E293B',
            'sort_order' => 3,
        ]);

        $valTitanium = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Brushed Titanium'], [
            'color_hex' => '#64748B',
            'sort_order' => 4,
        ]);

        $valChampagne = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Champagne Gold'], [
            'color_hex' => '#D4AF37',
            'sort_order' => 5,
        ]);

        $valStorage128 = AttributeValue::updateOrCreate(['attribute_id' => $attrStorage->id, 'value' => '128 GB'], ['sort_order' => 1]);
        $valStorage256 = AttributeValue::updateOrCreate(['attribute_id' => $attrStorage->id, 'value' => '256 GB'], ['sort_order' => 2]);
        $valStorage512 = AttributeValue::updateOrCreate(['attribute_id' => $attrStorage->id, 'value' => '512 GB'], ['sort_order' => 3]);

        $valSizeSmall = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => 'Small (40mm)'], ['sort_order' => 1]);
        $valSizeLarge = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => 'Large (44mm)'], ['sort_order' => 2]);

        // 4. Products Creation
        // PRODUCT 1: Flagship Over-Ear Headphones (Variable, Multi-Colour with Independent Galleries)
        $pHeadphones = Product::updateOrCreate(['slug' => 'aether-one-anc-headphones'], [
            'brand_id' => $brandModels['aether-sound']->id,
            'name' => 'Aether One Active Noise Cancelling Headphones',
            'base_sku' => 'AET-ONE-BASE',
            'barcode' => '8901234567890',
            'short_description' => 'Precision beryllium acoustic drivers encased in anodized aerospace aluminum with active planar noise cancellation.',
            'description' => '<h3>Sound in its Purest Geometry</h3><p>Engineered without compromise, the Aether One delivers transcendent acoustic clarity, deep harmonic resonance, and adaptive room-sensing cancellation. Hand-stitched lambskin earcups provide peerless comfort for extended listening sessions.</p>',
            'specifications_json' => [
                'Driver Type' => '40mm Custom Beryllium Diaphragm',
                'Frequency Response' => '10Hz – 45,000Hz',
                'Battery Life' => '36 Hours with ANC Enabled',
                'Fast Charging' => '15 mins charge provides 6 hours playback',
                'Connectivity' => 'Bluetooth 5.3 + Lossless USB-C Audio',
                'Weight' => '285g',
            ],
            'price' => 34999.00,
            'sale_price' => 29999.00,
            'cost_price' => 16000.00,
            'tax_rate' => 18.00,
            'weight' => 0.65,
            'weight_unit' => 'kg',
            'material' => 'Anodized Aluminum & Lambskin Leather',
            'manufacturer' => 'Aether Acoustics AG',
            'country_of_origin' => 'Germany',
            'is_variable' => true,
            'status' => 'active',
            'is_featured' => true,
            'is_bestseller' => true,
            'is_new_arrival' => false,
            'seo_title' => 'Aether One ANC Wireless Headphones — ATOZ GADGETZ',
            'seo_description' => 'Experience studio-calibrated spatial audio and active noise cancellation in luxury aluminum.',
        ]);
        $pHeadphones->categories()->sync([$catAudio->id, $catEarphones->id]);

        // Independent Galleries for Product 1
        // Obsidian Black Gallery
        $blackImages = [
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1000&q=80',
        ];
        foreach ($blackImages as $idx => $url) {
            ProductImage::updateOrCreate([
                'product_id' => $pHeadphones->id,
                'image_path' => $url,
            ], [
                'attribute_value_id' => $valObsidian->id,
                'alt_text' => 'Aether One in Obsidian Black',
                'sort_order' => $idx + 1,
                'is_primary' => $idx === 0,
            ]);
        }

        // Alabaster Silver Gallery
        $silverImages = [
            'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1524678606370-a47ad25cb82a?auto=format&fit=crop&w=1000&q=80',
        ];
        foreach ($silverImages as $idx => $url) {
            ProductImage::updateOrCreate([
                'product_id' => $pHeadphones->id,
                'image_path' => $url,
            ], [
                'attribute_value_id' => $valSilver->id,
                'alt_text' => 'Aether One in Alabaster Silver',
                'sort_order' => $idx + 1,
                'is_primary' => false,
            ]);
        }

        // Midnight Navy Gallery
        $navyImages = [
            'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80',
        ];
        foreach ($navyImages as $idx => $url) {
            ProductImage::updateOrCreate([
                'product_id' => $pHeadphones->id,
                'image_path' => $url,
            ], [
                'attribute_value_id' => $valNavy->id,
                'alt_text' => 'Aether One in Midnight Navy',
                'sort_order' => $idx + 1,
                'is_primary' => false,
            ]);
        }

        // Create Variants for Product 1
        $v1Black = ProductVariant::updateOrCreate(['sku' => 'AET-ONE-BLK'], [
            'product_id' => $pHeadphones->id,
            'barcode' => '8901234567891',
            'price_override' => null,
            'sale_price_override' => null,
            'status' => 'active',
        ]);
        $v1Black->attributeValues()->sync([$valObsidian->id]);
        $inv1 = Inventory::updateOrCreate(['product_variant_id' => $v1Black->id], [
            'quantity' => 28,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ]);
        InventoryMovement::create([
            'inventory_id' => $inv1->id,
            'change_amount' => 28,
            'previous_qty' => 0,
            'new_qty' => 28,
            'type' => 'purchase',
            'notes' => 'Initial stock intake',
        ]);

        $v1Silver = ProductVariant::updateOrCreate(['sku' => 'AET-ONE-SLV'], [
            'product_id' => $pHeadphones->id,
            'barcode' => '8901234567892',
            'price_override' => null,
            'sale_price_override' => null,
            'status' => 'active',
        ]);
        $v1Silver->attributeValues()->sync([$valSilver->id]);
        $inv2 = Inventory::updateOrCreate(['product_variant_id' => $v1Silver->id], [
            'quantity' => 14,
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ]);

        $v1Navy = ProductVariant::updateOrCreate(['sku' => 'AET-ONE-NVY'], [
            'product_id' => $pHeadphones->id,
            'barcode' => '8901234567893',
            'price_override' => 36999.00,
            'sale_price_override' => 31999.00,
            'status' => 'active',
        ]);
        $v1Navy->attributeValues()->sync([$valNavy->id]);
        $inv3 = Inventory::updateOrCreate(['product_variant_id' => $v1Navy->id], [
            'quantity' => 4, // Low stock demo!
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
        ]);

        // PRODUCT 2: Chronos VII Titanium Smartwatch
        $pWatch = Product::updateOrCreate(['slug' => 'chronos-vii-titanium-timepiece'], [
            'brand_id' => $brandModels['chronos-labs']->id,
            'name' => 'Chronos VII Grade-5 Titanium Smartwatch',
            'base_sku' => 'CHR-VII-BASE',
            'barcode' => '8902345678901',
            'short_description' => 'Sculpted from aerospace Grade-5 titanium with a sapphire crystal AMOLED display and 14-day continuous biometric tracking.',
            'description' => '<h3>The Apex of Wearable Precision</h3><p>Chronos VII merges the legacy of Swiss horology with state-of-the-art optical photoplethysmography and continuous ECG heart telemetry. Rated to 10 ATM immersion.</p>',
            'specifications_json' => [
                'Case Material' => 'Grade-5 Brushed Titanium',
                'Glass' => 'Curved Synthetic Sapphire Crystal',
                'Display' => '1.43" Ultra-Bright AMOLED (1500 nits)',
                'Water Resistance' => '10 ATM (100 meters)',
                'Battery Life' => '14 Days Typical Use',
                'Sensors' => 'Optical ECG, SpO2, Skin Temperature, Barometer',
            ],
            'price' => 54999.00,
            'sale_price' => 49999.00,
            'cost_price' => 24000.00,
            'tax_rate' => 18.00,
            'weight' => 0.45,
            'is_variable' => true,
            'status' => 'active',
            'is_featured' => true,
            'is_bestseller' => false,
            'is_new_arrival' => true,
            'seo_title' => 'Chronos VII Titanium Smartwatch — ATOZ GADGETZ',
            'seo_description' => 'Swiss-inspired luxury smart timepiece crafted from aerospace grade titanium.',
        ]);
        $pWatch->categories()->sync([$catWearables->id]);

        // Titanium Images
        $watchTitaniumImages = [
            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?auto=format&fit=crop&w=1000&q=80',
        ];
        foreach ($watchTitaniumImages as $idx => $url) {
            ProductImage::updateOrCreate([
                'product_id' => $pWatch->id,
                'image_path' => $url,
            ], [
                'attribute_value_id' => $valTitanium->id,
                'alt_text' => 'Chronos VII in Brushed Titanium',
                'sort_order' => $idx + 1,
                'is_primary' => $idx === 0,
            ]);
        }

        // Champagne Gold Images
        $watchGoldImages = [
            'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1000&q=80',
        ];
        foreach ($watchGoldImages as $idx => $url) {
            ProductImage::updateOrCreate([
                'product_id' => $pWatch->id,
                'image_path' => $url,
            ], [
                'attribute_value_id' => $valChampagne->id,
                'alt_text' => 'Chronos VII in Champagne Gold',
                'sort_order' => $idx + 1,
                'is_primary' => false,
            ]);
        }

        // Combinations: Titanium + 44mm, Titanium + 40mm, Gold + 44mm
        $vWatch1 = ProductVariant::updateOrCreate(['sku' => 'CHR-VII-TI-44'], [
            'product_id' => $pWatch->id,
            'barcode' => '8902345678911',
            'price_override' => 54999.00,
            'sale_price_override' => 49999.00,
            'status' => 'active',
        ]);
        $vWatch1->attributeValues()->sync([$valTitanium->id, $valSizeLarge->id]);
        Inventory::updateOrCreate(['product_variant_id' => $vWatch1->id], ['quantity' => 19, 'low_stock_threshold' => 4]);

        $vWatch2 = ProductVariant::updateOrCreate(['sku' => 'CHR-VII-TI-40'], [
            'product_id' => $pWatch->id,
            'barcode' => '8902345678912',
            'price_override' => 51999.00,
            'sale_price_override' => 46999.00,
            'status' => 'active',
        ]);
        $vWatch2->attributeValues()->sync([$valTitanium->id, $valSizeSmall->id]);
        Inventory::updateOrCreate(['product_variant_id' => $vWatch2->id], ['quantity' => 11, 'low_stock_threshold' => 3]);

        $vWatch3 = ProductVariant::updateOrCreate(['sku' => 'CHR-VII-GLD-44'], [
            'product_id' => $pWatch->id,
            'barcode' => '8902345678913',
            'price_override' => 59999.00,
            'sale_price_override' => 54999.00,
            'status' => 'active',
        ]);
        $vWatch3->attributeValues()->sync([$valChampagne->id, $valSizeLarge->id]);
        Inventory::updateOrCreate(['product_variant_id' => $vWatch3->id], ['quantity' => 6, 'low_stock_threshold' => 2]);

        // PRODUCT 3: Luminary 50mm Rangefinder Camera (Simple product)
        $pCamera = Product::updateOrCreate(['slug' => 'luminary-m5-minimal-rangefinder'], [
            'brand_id' => $brandModels['luminary-optics']->id,
            'name' => 'Luminary M5 Full-Frame Minimalist Rangefinder',
            'base_sku' => 'LUM-M5-001',
            'barcode' => '8903456789012',
            'short_description' => 'A tactile mechanical rangefinder equipped with a bespoke 60MP monochrome-tuned BSI sensor and silent leaf shutter.',
            'description' => '<p>Designed for purist street and architectural photography. Built from a solid magnesium alloy block with diamond-knurled brass dials.</p>',
            'specifications_json' => [
                'Sensor' => '60.3 MP Full-Frame BSI CMOS',
                'Mount' => 'M-Bespoke Bayonet',
                'ISO Range' => '64 – 100,000',
                'Weight' => '590g with battery',
            ],
            'price' => 289999.00,
            'sale_price' => null,
            'cost_price' => 180000.00,
            'tax_rate' => 18.00,
            'is_variable' => false,
            'status' => 'active',
            'is_featured' => true,
            'is_bestseller' => false,
            'is_new_arrival' => true,
        ]);
        $pCamera->categories()->sync([$catOptics->id]);

        ProductImage::updateOrCreate([
            'product_id' => $pCamera->id,
            'image_path' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80',
        ], ['alt_text' => 'Luminary M5 Rangefinder Front', 'sort_order' => 1, 'is_primary' => true]);

        ProductImage::updateOrCreate([
            'product_id' => $pCamera->id,
            'image_path' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=1000&q=80',
        ], ['alt_text' => 'Luminary M5 Top Dial', 'sort_order' => 2, 'is_primary' => false]);

        $vCam = ProductVariant::updateOrCreate(['sku' => 'LUM-M5-001'], [
            'product_id' => $pCamera->id,
            'barcode' => '8903456789012',
            'price_override' => null,
            'status' => 'active',
        ]);
        Inventory::updateOrCreate(['product_variant_id' => $vCam->id], ['quantity' => 7, 'low_stock_threshold' => 2]);

        // PRODUCT 4: Obsidian Gravity Magnetic Charging Stand
        $pStand = Product::updateOrCreate(['slug' => 'obsidian-gravity-magnetic-stand'], [
            'brand_id' => $brandModels['obsidian-desk']->id,
            'name' => 'Obsidian Gravity MagSafe Floating Stand',
            'base_sku' => 'OBS-GRV-STD',
            'barcode' => '8904567890123',
            'short_description' => 'CNC machined from solid brass and coated in matte DLC obsidian. Counterbalanced 1.2kg solid base.',
            'description' => '<p>Allows one-handed phone detachment without base wobble. Dual 15W Qi2 fast wireless induction charging for phone and earbuds simultaneously.</p>',
            'specifications_json' => [
                'Base Weight' => '1.2 kg Solid Machined Alloy',
                'Charging Output' => '15W MagSafe + 5W Earbud Tray',
                'Finish' => 'Matte Diamond-Like Carbon (DLC)',
            ],
            'price' => 11999.00,
            'sale_price' => 9499.00,
            'cost_price' => 4500.00,
            'is_variable' => false,
            'status' => 'active',
            'is_featured' => true,
            'is_bestseller' => true,
            'is_new_arrival' => false,
        ]);
        $pStand->categories()->sync([$catDesk->id]);

        ProductImage::updateOrCreate([
            'product_id' => $pStand->id,
            'image_path' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80',
        ], ['alt_text' => 'Obsidian Gravity Stand', 'sort_order' => 1, 'is_primary' => true]);

        ProductImage::updateOrCreate([
            'product_id' => $pStand->id,
            'image_path' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
        ], ['alt_text' => 'Obsidian Gravity Side View', 'sort_order' => 2, 'is_primary' => false]);

        $vStand = ProductVariant::updateOrCreate(['sku' => 'OBS-GRV-STD'], [
            'product_id' => $pStand->id,
            'barcode' => '8904567890123',
            'status' => 'active',
        ]);
        Inventory::updateOrCreate(['product_variant_id' => $vStand->id], ['quantity' => 45, 'low_stock_threshold' => 10]);

        // PRODUCT 5: Nordic Spatial HiFi Soundbar
        $pSoundbar = Product::updateOrCreate(['slug' => 'nordic-spatial-acoustic-soundbar'], [
            'brand_id' => $brandModels['nordic-audio']->id,
            'name' => 'Nordic Spatial Room-Filling Studio Soundbar',
            'base_sku' => 'NOR-SPT-SND',
            'barcode' => '8905678901234',
            'short_description' => 'Kvadrat acoustic textile exterior with 11 custom-tuned coaxial drivers delivering Dolby Atmos 7.1.4 architecture.',
            'description' => '<p>Minimalist monolithic enclosure designed to sit flush under thin OLED displays. Includes wireless active calibration microphone.</p>',
            'specifications_json' => [
                'Amplification' => '650W Class-D Tri-Amplified',
                'Audio Formats' => 'Dolby Atmos, DTS:X, Hi-Res FLAC 24-bit/192kHz',
                'Fabric' => 'Custom Kvadrat Wool Blend',
            ],
            'price' => 79999.00,
            'sale_price' => 69999.00,
            'cost_price' => 38000.00,
            'is_variable' => false,
            'status' => 'active',
            'is_featured' => true,
            'is_bestseller' => true,
            'is_new_arrival' => false,
        ]);
        $pSoundbar->categories()->sync([$catAudio->id]);

        ProductImage::updateOrCreate([
            'product_id' => $pSoundbar->id,
            'image_path' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=1000&q=80',
        ], ['alt_text' => 'Nordic Spatial Soundbar', 'sort_order' => 1, 'is_primary' => true]);

        $vSoundbar = ProductVariant::updateOrCreate(['sku' => 'NOR-SPT-SND'], [
            'product_id' => $pSoundbar->id,
            'barcode' => '8905678901234',
            'status' => 'active',
        ]);
        Inventory::updateOrCreate(['product_variant_id' => $vSoundbar->id], ['quantity' => 12, 'low_stock_threshold' => 3]);

        // 5. Create Promotional Banners
        Banner::updateOrCreate(['title' => 'The Architecture of Pure Sound'], [
            'subtitle' => 'Meet Aether One. 40mm Beryllium Drivers in Anodized Obsidian.',
            'badge_text' => 'FLAGSHIP RELEASE',
            'cta_text' => 'Explore Aether One',
            'cta_link' => '/product/aether-one-anc-headphones',
            'desktop_image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1600&q=85',
            'mobile_image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=85',
            'position' => 'hero',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Banner::updateOrCreate(['title' => 'Horological Intelligence'], [
            'subtitle' => 'Chronos VII Grade-5 Titanium. Engineered for timeless precision.',
            'badge_text' => 'NEW GENERATION',
            'cta_text' => 'Discover Chronos VII',
            'cta_link' => '/product/chronos-vii-titanium-timepiece',
            'desktop_image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1600&q=85',
            'mobile_image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=85',
            'position' => 'hero',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 6. Create Coupons
        Coupon::updateOrCreate(['code' => 'WELCOME10'], [
            'type' => 'percentage',
            'value' => 10.00,
            'min_spend' => 4999.00,
            'max_discount' => 5000.00,
            'usage_limit_total' => 1000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'ATOZ2000'], [
            'type' => 'fixed',
            'value' => 2000.00,
            'min_spend' => 19999.00,
            'max_discount' => 2000.00,
            'usage_limit_total' => 500,
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);

        // 7. Create Reviews
        Review::updateOrCreate([
            'product_id' => $pHeadphones->id,
            'author_name' => 'Dr. Aris Thorne',
        ], [
            'rating' => 5,
            'title' => 'Unquestionably the finest acoustics I have ever heard.',
            'comment' => 'The separation between mid-tones and sub-bass harmonics is extraordinary. The aluminum build feels like high-end Leica or Apple hardware.',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        Review::updateOrCreate([
            'product_id' => $pWatch->id,
            'author_name' => 'Maya S. Laurent',
        ], [
            'rating' => 5,
            'title' => 'Grade-5 titanium makes it disappear on the wrist.',
            'comment' => 'Battery life genuinely lasts 14 days without charge. Beautiful minimalist watch faces that respect luxury design sensibilities.',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        // 8. Create Static Pages
        Page::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'About ATOZ GADGETZ',
            'content' => '<h2>Architectural Tech & Haute Gadgetry</h2><p>ATOZ GADGETZ curates the world’s most refined electronics, acoustic instruments, and desk architecture. We believe technology should not simply be functional; it should possess the poise, materials, and permanence of fine modern architecture.</p>',
            'is_active' => true,
        ]);

        Page::updateOrCreate(['slug' => 'shipping-returns'], [
            'title' => 'Shipping & Concierge Returns',
            'content' => '<h2>Complimentary White-Glove Delivery</h2><p>All orders over ₹2,999 are dispatched via insured express air courier. We offer 14-day hassle-free door-to-door concierge returns on all unopened or defective items.</p>',
            'is_active' => true,
        ]);
    }
}
