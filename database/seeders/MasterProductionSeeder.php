<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MasterProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info("Initializing directories for local media assets...");

        // 1. Ensure storage directories exist
        $bannerDir = storage_path('app/public/banners');
        $catDir = storage_path('app/public/categories');
        $productDir = storage_path('app/public/products');

        File::ensureDirectoryExists($bannerDir);
        File::ensureDirectoryExists($catDir);
        File::ensureDirectoryExists($productDir);

        // Helper to download asset safely or fallback
        $downloadAsset = function (string $url, string $destPath) {
            if (File::exists($destPath) && filesize($destPath) > 5000) {
                return;
            }
            try {
                $response = Http::timeout(15)->withoutVerifying()->get($url);
                if ($response->successful() && strlen($response->body()) > 5000) {
                    File::put($destPath, $response->body());
                    return;
                }
            } catch (\Throwable $e) {
                // If network fails, copy default placeholder
            }

            // Fallback: create high-res SVG canvas
            if (!File::exists($destPath)) {
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1000" height="1000" viewBox="0 0 1000 1000"><rect width="1000" height="1000" fill="#141413"/><circle cx="500" cy="500" r="300" fill="#1E1E1C" stroke="#2B2B28" stroke-width="4"/><text x="500" y="510" fill="#FFFFFF" font-family="system-ui, sans-serif" font-size="28" font-weight="700" letter-spacing="4" text-anchor="middle">ATOZ ATELIER</text></svg>';
                File::put($destPath, $svg);
            }
        };

        // 2. Download Category Images
        $categoriesData = [
            'audio-acoustics' => [
                'name' => 'Audio & Acoustics',
                'description' => 'Studio-grade headphones, earbuds, and spatial soundbars.',
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
                'file' => 'audio-acoustics.jpg',
            ],
            'wearables' => [
                'name' => 'Wearables & Horology',
                'description' => 'Titanium biometric smartwatches and smart rings.',
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80',
                'file' => 'wearables.jpg',
            ],
            'workspace-architecture' => [
                'name' => 'Desk Architecture',
                'description' => 'Magnetic docks, mechanical boards, and precision stands.',
                'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                'file' => 'workspace-architecture.jpg',
            ],
            'optics-cameras' => [
                'name' => 'Optics & Visuals',
                'description' => 'Rangefinder cameras and 5K minimalist studio monitors.',
                'image_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80',
                'file' => 'optics-cameras.jpg',
            ],
            'power-mobility' => [
                'name' => 'Power & Mobility',
                'description' => 'Aerospace GaN fast charging stations and billet power banks.',
                'image_url' => 'https://images.unsplash.com/photo-1622445262464-84b14e0745b1?auto=format&fit=crop&w=800&q=80',
                'file' => 'power-mobility.jpg',
            ],
            'smart-living' => [
                'name' => 'Smart Living & Studio',
                'description' => 'Atmospheric ambient diffusion and acoustic architecture.',
                'image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                'file' => 'smart-living.jpg',
            ],
        ];

        $categories = [];
        $sort = 1;
        foreach ($categoriesData as $slug => $cd) {
            $dest = "{$catDir}/{$cd['file']}";
            $downloadAsset($cd['image_url'], $dest);

            $categories[$slug] = Category::updateOrCreate(['slug' => $slug], [
                'name' => $cd['name'],
                'description' => $cd['description'],
                'image_path' => "/storage/categories/{$cd['file']}",
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => $sort++,
            ]);
        }

        // 3. Download Hero Banners
        $bannersData = [
            [
                'title' => 'ARCHITECTURAL ACOUSTICS & HAUTE GADGETRY',
                'subtitle' => 'Milled aerospace titanium, beryllium planar drivers, and minimalist desk objects assembled with zero compromise.',
                'badge_text' => '2026 ATELIER COLLECTION',
                'cta_text' => 'Explore Catalogue',
                'cta_link' => '/shop',
                'position' => 'hero',
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1400&q=85',
                'file' => 'hero-banner-1.jpg',
                'sort_order' => 1,
            ],
            [
                'title' => 'SOLID GRADE 5 TITANIUM HOROLOGY',
                'subtitle' => 'Biometric precision, sapphire crystal glass, and continuous biometric telemetry in pure milled titanium.',
                'badge_text' => 'CHRONOS SERIES',
                'cta_text' => 'Discover Timepieces',
                'cta_link' => '/shop?category=wearables',
                'position' => 'hero',
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1400&q=85',
                'file' => 'hero-banner-2.jpg',
                'sort_order' => 2,
            ],
        ];

        foreach ($bannersData as $b) {
            $dest = "{$bannerDir}/{$b['file']}";
            $downloadAsset($b['image_url'], $dest);

            Banner::updateOrCreate(['title' => $b['title']], [
                'subtitle' => $b['subtitle'],
                'badge_text' => $b['badge_text'],
                'cta_text' => $b['cta_text'],
                'cta_link' => $b['cta_link'],
                'position' => $b['position'],
                'desktop_image' => "/storage/banners/{$b['file']}",
                'mobile_image' => "/storage/banners/{$b['file']}",
                'sort_order' => $b['sort_order'],
                'is_active' => true,
            ]);
        }

        // 4. Brands
        $brandsData = [
            'aether-sound' => ['name' => 'Aether Sound', 'description' => 'Acoustic engineering and timeless industrial design.'],
            'chronos-labs' => ['name' => 'Chronos Labs', 'description' => 'Precision biometric micro-electronics and luxury titanium timepieces.'],
            'luminary-optics' => ['name' => 'Luminary Optics', 'description' => 'German-engineered glass optics and minimal sensors.'],
            'obsidian-desk' => ['name' => 'Obsidian Desk', 'description' => 'Architectural workspace essentials crafted from aerospace aluminum.'],
            'nordic-audio' => ['name' => 'Nordic Audio', 'description' => 'Scandinavian minimalism tuned for spatial sound precision.'],
        ];

        $brands = [];
        foreach ($brandsData as $slug => $b) {
            $brands[$slug] = Brand::updateOrCreate(['slug' => $slug], [
                'name' => $b['name'],
                'description' => $b['description'],
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        // 5. Attributes (Colour, Size, Storage, Switch)
        $attrColor = Attribute::updateOrCreate(['code' => 'color'], ['name' => 'Colour', 'type' => 'color']);
        $valObsidian = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Obsidian Black'], ['color_hex' => '#141413']);
        $valSilver = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Arctic Silver'], ['color_hex' => '#E5E7EB']);
        $valNavy = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => 'Midnight Navy'], ['color_hex' => '#1B263B']);
        $valGold = AttributeValue::updateOrCreate(['attribute_id' => $attrColor->id, 'value' => '18K Raw Gold'], ['color_hex' => '#D4AF37']);

        $attrSize = Attribute::updateOrCreate(['code' => 'size'], ['name' => 'Size', 'type' => 'select']);
        $val42mm = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => '42mm Case']);
        $val46mm = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => '46mm Case']);
        $valRing8 = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => 'Size 8 (18.1mm)']);
        $valRing9 = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => 'Size 9 (18.9mm)']);
        $valRing10 = AttributeValue::updateOrCreate(['attribute_id' => $attrSize->id, 'value' => 'Size 10 (19.8mm)']);

        // 6. Complete 18 Curated Luxury Products
        $productsCatalog = [
            // Product 1
            [
                'slug' => 'aether-one-anc-headphones',
                'name' => 'Aether One Active Noise Cancelling Headphones',
                'base_sku' => 'AET-ONE-BASE',
                'barcode' => '8901234567890',
                'brand' => 'aether-sound',
                'category' => 'audio-acoustics',
                'price' => 34999.00,
                'sale_price' => 29999.00,
                'short_description' => 'Precision beryllium acoustic drivers encased in anodized aerospace aluminum with active planar noise cancellation.',
                'description' => "Sound in its Purest Geometry. Engineered without compromise, the Aether One delivers transcendent acoustic clarity, deep harmonic resonance, and adaptive room-sensing cancellation. Hand-stitched lambskin earcups provide peerless comfort for extended listening sessions.",
                'specs' => [
                    'Driver Type' => '40mm Custom Beryllium Diaphragm',
                    'Frequency Response' => '10Hz – 45,000Hz',
                    'Battery Life' => '36 Hours with ANC Enabled',
                    'Connectivity' => 'Bluetooth 5.3 + Lossless USB-C Audio',
                    'Net Weight' => '285g',
                ],
                'material' => 'Anodized Aluminum & Lambskin Leather',
                'country_of_origin' => 'Germany',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1524678606370-a47ad25cb82a?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valNavy->id => [
                        'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'AET-ONE-BLK', 'attrs' => [$valObsidian->id], 'stock' => 28, 'price_override' => null],
                    ['sku' => 'AET-ONE-SLV', 'attrs' => [$valSilver->id], 'stock' => 14, 'price_override' => null],
                    ['sku' => 'AET-ONE-NVY', 'attrs' => [$valNavy->id], 'stock' => 8, 'price_override' => null],
                ],
            ],

            // Product 2
            [
                'slug' => 'aether-studio-reference-iems',
                'name' => 'Aether Studio Reference In-Ear Monitors',
                'base_sku' => 'AET-IEM-BASE',
                'barcode' => '8901234567891',
                'brand' => 'aether-sound',
                'category' => 'audio-acoustics',
                'price' => 18999.00,
                'sale_price' => 15999.00,
                'short_description' => 'Quad balanced armature architecture in medical-grade 3D printed resin acoustic chambers.',
                'description' => "Studio accuracy in an ultra-compact footprint. The Aether Studio IEMs deliver transparent midranges and sub-bass clarity tuned precisely for mixing engineers and discerning audiophiles.",
                'specs' => [
                    'Transducers' => '4 Balanced Armature Drivers per Ear',
                    'Impedance' => '16 Ohms @ 1kHz',
                    'Cable' => '8-Core Silver Plated Copper (MMCX)',
                    'Isolation' => '-26dB Passive Acoustic Seal',
                ],
                'material' => 'Medical Grade Resin & 6061 Aluminum Nozzle',
                'country_of_origin' => 'Japan',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'AET-IEM-BLK', 'attrs' => [$valObsidian->id], 'stock' => 35, 'price_override' => null],
                    ['sku' => 'AET-IEM-SLV', 'attrs' => [$valSilver->id], 'stock' => 20, 'price_override' => null],
                ],
            ],

            // Product 3
            [
                'slug' => 'nordic-spatial-acoustic-soundbar',
                'name' => 'Nordic Spatial Acoustic Soundbar',
                'base_sku' => 'NOR-SND-BASE',
                'barcode' => '8901234567892',
                'brand' => 'nordic-audio',
                'category' => 'audio-acoustics',
                'price' => 79999.00,
                'sale_price' => 69999.00,
                'short_description' => 'Dolby Atmos 7.1.4 spatial architectural soundbar draped in acoustic wool with touch-capacitive controls.',
                'description' => "Minimalist architectural sound for the modern interior. Features 11 dedicated class-D amplifiers powering discrete height, center, and side-firing drivers.",
                'specs' => [
                    'Output Power' => '450 Watts Peak',
                    'Channels' => '7.1.4 Spatial Atmos',
                    'Inputs' => 'HDMI eARC, Optical, AirPlay 2, Spotify Connect',
                    'Dimensions' => '1150 x 85 x 120 mm',
                ],
                'material' => 'Solid Oak & Kvadrat Wool Fabric',
                'country_of_origin' => 'Denmark',
                'is_variable' => false,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'NOR-SND-OAK', 'attrs' => [$valObsidian->id], 'stock' => 12, 'price_override' => null],
                ],
            ],

            // Product 4
            [
                'slug' => 'vanguard-desktop-dac-tube-amplifier',
                'name' => 'Vanguard Precision Desktop DAC & Tube Amplifier',
                'base_sku' => 'VAN-DAC-BASE',
                'barcode' => '8901234567893',
                'brand' => 'aether-sound',
                'category' => 'audio-acoustics',
                'price' => 45000.00,
                'sale_price' => 39999.00,
                'short_description' => 'Dual ES9038PRO DAC chips paired with matched Russian vacuum tubes in solid milled aluminum billet.',
                'description' => "Analog warmth meets hyper-digital precision. Supports Native DSD512 and 32-bit/768kHz PCM with dual high-output balanced XLR headphone outputs.",
                'specs' => [
                    'DAC Architecture' => 'Dual ESS Sabre ES9038PRO',
                    'Tubes' => 'Matched Pair Electro-Harmonix 6922',
                    'Output Impedance' => '< 0.1 Ohm',
                    'THD+N' => '0.00015%',
                ],
                'material' => 'Anodized Billet Aluminum & Brass Dial',
                'country_of_origin' => 'Germany',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1558089687-f282ffcbc126?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'VAN-DAC-SLV', 'attrs' => [$valSilver->id], 'stock' => 9, 'price_override' => null],
                ],
            ],

            // Product 5
            [
                'slug' => 'chronos-precision-smartwatch',
                'name' => 'Chronos Precision Titanium Smartwatch',
                'base_sku' => 'CHR-WAT-BASE',
                'barcode' => '8901234567894',
                'brand' => 'chronos-labs',
                'category' => 'wearables',
                'price' => 48999.00,
                'sale_price' => 42999.00,
                'short_description' => 'Grade 5 aerospace titanium smartwatch featuring LTPO AMOLED display, sapphire crystal, and medical ECG telemetry.',
                'description' => "Haute horology engineered for the biometric era. Features 14-day battery life, continuous heart-rate tracking, oxygen saturation sensors, and titanium link band.",
                'specs' => [
                    'Display' => '1.43” Sapphire Crystal LTPO AMOLED',
                    'Water Resistance' => '100m (10 ATM Diver Certified)',
                    'Sensors' => 'ECG, PPG, Dual-Frequency GPS, Skin Temp',
                    'Battery Life' => '14 Days Typical Usage',
                ],
                'material' => 'Grade 5 Titanium & Sapphire Crystal',
                'country_of_origin' => 'Switzerland',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'CHR-WAT-42-TI', 'attrs' => [$valSilver->id, $val42mm->id], 'stock' => 15, 'price_override' => 42999.00],
                    ['sku' => 'CHR-WAT-46-TI', 'attrs' => [$valSilver->id, $val46mm->id], 'stock' => 22, 'price_override' => 45999.00],
                    ['sku' => 'CHR-WAT-46-BLK', 'attrs' => [$valObsidian->id, $val46mm->id], 'stock' => 18, 'price_override' => 46999.00],
                ],
            ],

            // Product 6
            [
                'slug' => 'chronos-horizon-smart-ring',
                'name' => 'Chronos Horizon Titanium Smart Ring',
                'base_sku' => 'CHR-RNG-BASE',
                'barcode' => '8901234567895',
                'brand' => 'chronos-labs',
                'category' => 'wearables',
                'price' => 24999.00,
                'sale_price' => 21999.00,
                'short_description' => 'Lightweight biocompatible titanium ring with continuous sleep staging, HRV, and recovery tracking.',
                'description' => "Invisible telemetry. Weighing just 3 grams, the Chronos Ring monitors cardiovascular recovery, deep sleep, and skin temperature trends without any screen distraction.",
                'specs' => [
                    'Weight' => '2.9 Grams',
                    'Sensors' => 'Infrared Photoplethysmography, NTC Temp',
                    'Battery Life' => '7 Days Wireless Fast Charging',
                    'Water Resistance' => '100m Water Proof',
                ],
                'material' => 'Solid Medical-Grade Titanium with PVD Coating',
                'country_of_origin' => 'Finland',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valGold->id => [
                        'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'CHR-RNG-8-TI', 'attrs' => [$valObsidian->id, $valRing8->id], 'stock' => 14, 'price_override' => null],
                    ['sku' => 'CHR-RNG-9-TI', 'attrs' => [$valObsidian->id, $valRing9->id], 'stock' => 20, 'price_override' => null],
                    ['sku' => 'CHR-RNG-10-GLD', 'attrs' => [$valGold->id, $valRing10->id], 'stock' => 11, 'price_override' => 24999.00],
                ],
            ],

            // Product 7
            [
                'slug' => 'bespoke-tuscan-leather-strap',
                'name' => 'Bespoke Tuscan Calfskin Horology Strap',
                'base_sku' => 'STR-LEA-BASE',
                'barcode' => '8901234567896',
                'brand' => 'chronos-labs',
                'category' => 'wearables',
                'price' => 7499.00,
                'sale_price' => 5999.00,
                'short_description' => 'Hand-stitched vegetable-tanned Tuscan leather strap with titanium quick-release buckle.',
                'description' => "Crafted in Florence from full-grain vegetable-tanned leather that patinas beautifully over years of wear.",
                'specs' => [
                    'Width' => '22mm Standard Lug',
                    'Buckle' => 'Milled Grade 5 Titanium',
                    'Leather Origin' => 'Santa Croce sull’Arno, Italy',
                ],
                'material' => 'Full-Grain Italian Calfskin Leather',
                'country_of_origin' => 'Italy',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'STR-LEA-BRN', 'attrs' => [$valObsidian->id], 'stock' => 40, 'price_override' => null],
                ],
            ],

            // Product 8
            [
                'slug' => 'obsidian-precision-aluminum-stand',
                'name' => 'Obsidian Precision Aluminum MagSafe Stand',
                'base_sku' => 'OBS-STN-BASE',
                'barcode' => '8901234567897',
                'brand' => 'obsidian-desk',
                'category' => 'workspace-architecture',
                'price' => 8999.00,
                'sale_price' => 7499.00,
                'short_description' => 'Architectural aluminum phone and headphone stand milled from a single billet with integrated 15W Qi2 fast wireless charging.',
                'description' => "Workspace architecture redefined. Provides optimal viewing angles for FaceTime and StandBy mode while keeping high-fidelity headphones suspended on silicone cradles.",
                'specs' => [
                    'Charging' => '15W Certified Qi2 Magnetic Fast Charging',
                    'Weight' => '420g Weighted Solid Base',
                    'Cable' => 'Includes 2m Braided USB-C Cable',
                ],
                'material' => '6063 Anodized Aluminum & Liquid Silicone',
                'country_of_origin' => 'Taiwan',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'OBS-STN-BLK', 'attrs' => [$valObsidian->id], 'stock' => 50, 'price_override' => null],
                    ['sku' => 'OBS-STN-SLV', 'attrs' => [$valSilver->id], 'stock' => 30, 'price_override' => null],
                ],
            ],

            // Product 9
            [
                'slug' => 'apex-75-modular-mechanical-keyboard',
                'name' => 'Apex 75 Modular Mechanical Keyboard',
                'base_sku' => 'APX-75-BASE',
                'barcode' => '8901234567898',
                'brand' => 'obsidian-desk',
                'category' => 'workspace-architecture',
                'price' => 26999.00,
                'sale_price' => 22999.00,
                'short_description' => 'Custom gasket-mounted 75% mechanical keyboard with CNC aluminum chassis and programmable brass rotary dial.',
                'description' => "Unrivaled acoustics and tactile satisfaction. Equipped with sound-dampening IXPE foam, factory-lubed linear switches, and seamless Bluetooth/2.4GHz wireless connectivity.",
                'specs' => [
                    'Layout' => '75% Compact (82 Keys + Rotary Dial)',
                    'Mounting' => 'Double Gasket Mount with Brass Plate',
                    'Keycaps' => 'Custom Cherry Profile PBT Doubleshot',
                    'Battery' => '4000mAh (Up to 300 Hours)',
                ],
                'material' => 'CNC Anodized 6063 Aluminum & Brass',
                'country_of_origin' => 'Japan',
                'is_variable' => true,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80',
                    ],
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'APX-75-BLK', 'attrs' => [$valObsidian->id], 'stock' => 25, 'price_override' => null],
                    ['sku' => 'APX-75-WHT', 'attrs' => [$valSilver->id], 'stock' => 18, 'price_override' => null],
                ],
            ],

            // Product 10
            [
                'slug' => 'aeroform-leather-desk-blotter',
                'name' => 'Aeroform Full-Grain Leather Desk Blotter',
                'base_sku' => 'AER-BLT-BASE',
                'barcode' => '8901234567899',
                'brand' => 'obsidian-desk',
                'category' => 'workspace-architecture',
                'price' => 6499.00,
                'sale_price' => 4999.00,
                'short_description' => 'Top-grain bridle leather desk pad fused with dense German wool felt for supreme precision mouse tracking.',
                'description' => "Elevates any desk surface into an executive studio. The natural pull-up leather cushions wrists and develops rich patina with everyday work.",
                'specs' => [
                    'Dimensions' => '900 x 400 x 4.5 mm',
                    'Backing' => 'Non-Slip Micro-Suede & Wool Felt',
                    'Features' => 'Concealed Magnetic Cable Docking Channel',
                ],
                'material' => 'Vegetable-Tanned Bridle Leather',
                'country_of_origin' => 'United Kingdom',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'AER-BLT-BLK', 'attrs' => [$valObsidian->id], 'stock' => 45, 'price_override' => null],
                ],
            ],

            // Product 11
            [
                'slug' => 'orbit-brass-cable-anchors',
                'name' => 'Orbit Sculptural Solid Brass Cable Anchors',
                'base_sku' => 'ORB-CBL-BASE',
                'barcode' => '8901234567810',
                'brand' => 'obsidian-desk',
                'category' => 'workspace-architecture',
                'price' => 4299.00,
                'sale_price' => 3499.00,
                'short_description' => 'Set of 3 heavy solid brass geometric cable anchors with neodymium magnetic guides.',
                'description' => "Never chase dropped cables behind your desk again. Crafted from solid brass with micro-suction silicone bases.",
                'specs' => [
                    'Set Count' => '3 Geometric Units',
                    'Weight' => '180g Each Solid Brass',
                ],
                'material' => 'Solid Milled C360 Brass',
                'country_of_origin' => 'Japan',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valGold->id => [
                        'https://images.unsplash.com/photo-1558089687-f282ffcbc126?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'ORB-CBL-BRS', 'attrs' => [$valGold->id], 'stock' => 60, 'price_override' => null],
                ],
            ],

            // Product 12
            [
                'slug' => 'luminary-horizon-4k-rangefinder-camera',
                'name' => 'Luminary Horizon 4K Rangefinder Camera',
                'base_sku' => 'LUM-CAM-BASE',
                'barcode' => '8901234567811',
                'brand' => 'luminary-optics',
                'category' => 'optics-cameras',
                'price' => 145000.00,
                'sale_price' => 129999.00,
                'short_description' => 'Minimalist full-frame 48MP mirrorless digital rangefinder in matte magnesium alloy body.',
                'description' => "Pure photographic purity. Replaces cluttered touchscreen menus with mechanical brass exposure dials and an optical viewfinder with 0.73x magnification.",
                'specs' => [
                    'Sensor' => '48.3 Megapixel BSI CMOS Full-Frame Sensor',
                    'ISO Range' => '50 – 102,400',
                    'Storage' => 'Dual UHS-II SD + 64GB High-Speed Internal',
                    'Weight' => '640g with Battery',
                ],
                'material' => 'Magnesium Alloy & Vulcanite Leatherette',
                'country_of_origin' => 'Germany',
                'is_variable' => false,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'LUM-CAM-BLK', 'attrs' => [$valObsidian->id], 'stock' => 6, 'price_override' => null],
                ],
            ],

            // Product 13
            [
                'slug' => 'luminary-prime-35mm-cine-lens',
                'name' => 'Luminary Horizon Prime 35mm f/1.4 Lens',
                'base_sku' => 'LUM-LNS-BASE',
                'barcode' => '8901234567812',
                'brand' => 'luminary-optics',
                'category' => 'optics-cameras',
                'price' => 68000.00,
                'sale_price' => 59999.00,
                'short_description' => 'German optical glass prime lens featuring 10 elements in 8 groups with 12-blade rounded aperture.',
                'description' => "Uncompromising edge-to-edge sharpness and dreamy cinematic bokeh, enclosed in a weather-sealed brass barrel.",
                'specs' => [
                    'Focal Length' => '35mm Full Frame',
                    'Max Aperture' => 'f/1.4 (Clickless De-Clickable)',
                    'Mount' => 'M-Mount & E-Mount Compatible',
                ],
                'material' => 'Anodized Black Brass Barrel',
                'country_of_origin' => 'Germany',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'LUM-LNS-35', 'attrs' => [$valObsidian->id], 'stock' => 10, 'price_override' => null],
                ],
            ],

            // Product 14
            [
                'slug' => 'aura-dual-source-monitor-light-bar',
                'name' => 'Aura Minimalist Dual-Source Monitor Light Bar',
                'base_sku' => 'AUR-LGT-BASE',
                'barcode' => '8901234567813',
                'brand' => 'luminary-optics',
                'category' => 'optics-cameras',
                'price' => 12999.00,
                'sale_price' => 9999.00,
                'short_description' => 'Glare-free asymmetric monitor light with rear ambient studio halo and wireless magnetic dial.',
                'description' => "CRI 98 natural color rendering reduces eyestrain during late-night creative sessions with zero screen reflections.",
                'specs' => [
                    'Color Rendering' => 'Ra > 98 High Fidelity',
                    'Color Temp' => '2700K – 6500K Stepless Tuning',
                    'Control' => '2.4GHz Wireless Aluminum Rotary Puck',
                ],
                'material' => 'Space Gray Aluminum & Frosted Glass',
                'country_of_origin' => 'South Korea',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'AUR-LGT-GRY', 'attrs' => [$valSilver->id], 'stock' => 38, 'price_override' => null],
                ],
            ],

            // Product 15
            [
                'slug' => 'voltcraft-140w-gan-charging-station',
                'name' => 'VoltCraft 140W GaN Desktop Power Station',
                'base_sku' => 'VLT-140-BASE',
                'barcode' => '8901234567814',
                'brand' => 'obsidian-desk',
                'category' => 'power-mobility',
                'price' => 11999.00,
                'sale_price' => 9499.00,
                'short_description' => 'Gallium Nitride 140W fast desktop power hub with real-time digital OLED power telemetry screen.',
                'description' => "Powers MacBook Pro at full 140W speed while simultaneously fast-charging an iPhone and Apple Watch.",
                'specs' => [
                    'Max Output' => '140W Fast Charge (USB PD 3.1)',
                    'Ports' => '3x USB-C + 1x USB-A 3.0',
                    'Display' => 'Real-time Wattage and Temperature OLED',
                ],
                'material' => 'Fireproof Polycarbonate & Extruded Aluminum',
                'country_of_origin' => 'Taiwan',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1622445262464-84b14e0745b1?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'VLT-140-HUB', 'attrs' => [$valObsidian->id], 'stock' => 45, 'price_override' => null],
                ],
            ],

            // Product 16
            [
                'slug' => 'nomad-billet-laptop-power-bank',
                'name' => 'Nomad Billet 27,000mAh Laptop Power Bank',
                'base_sku' => 'NOM-27K-BASE',
                'barcode' => '8901234567815',
                'brand' => 'obsidian-desk',
                'category' => 'power-mobility',
                'price' => 14999.00,
                'sale_price' => 12499.00,
                'short_description' => 'FAA airline compliant 99.9Wh aerospace aluminum power bank supporting 140W bi-directional PD input/output.',
                'description' => "Milled from aircraft-grade aluminum with grooved heat dissipation fins for safe, sustained high-wattage charging on long flights.",
                'specs' => [
                    'Capacity' => '27,000mAh (99.9Wh TSA Approved)',
                    'Max Speed' => '140W Input & 140W Output',
                    'Weight' => '540g',
                ],
                'material' => 'Anodized Aircraft Aluminum Billet',
                'country_of_origin' => 'Taiwan',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1609592807904-8b5d3a56cf88?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'NOM-27K-SLV', 'attrs' => [$valSilver->id], 'stock' => 28, 'price_override' => null],
                ],
            ],

            // Product 17
            [
                'slug' => 'kevlar-armor-magnetic-cable-trio',
                'name' => 'Kevlar Armor USB4 Magnetic Cable Trio (3-Pack)',
                'base_sku' => 'KVL-CBL-BASE',
                'barcode' => '8901234567816',
                'brand' => 'obsidian-desk',
                'category' => 'power-mobility',
                'price' => 4999.00,
                'sale_price' => 3999.00,
                'short_description' => '240W 40Gbps USB4 cables woven from ballistic Dupont Kevlar with zinc alloy connectors.',
                'description' => "Engineered to withstand 50,000+ bends with zero fraying. Supports 8K@60Hz video display and 240W Power Delivery.",
                'specs' => [
                    'Standard' => 'USB4 (40Gbps Data + 240W Power)',
                    'Lengths' => '0.5m, 1.2m, and 2.0m Included',
                ],
                'material' => 'Dupont Kevlar & Zinc Alloy Enclosure',
                'country_of_origin' => 'USA',
                'is_variable' => false,
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new_arrival' => false,
                'galleries' => [
                    $valObsidian->id => [
                        'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'KVL-CBL-3PK', 'attrs' => [$valObsidian->id], 'stock' => 75, 'price_override' => null],
                ],
            ],

            // Product 18
            [
                'slug' => 'aura-glass-ambient-diffuser',
                'name' => 'Aura Glass Ambient Diffuser & Atmospheric Humidifier',
                'base_sku' => 'AUR-DIF-BASE',
                'barcode' => '8901234567817',
                'brand' => 'nordic-audio',
                'category' => 'smart-living',
                'price' => 9999.00,
                'sale_price' => 7999.00,
                'short_description' => 'Hand-blown frosted borosilicate glass ultrasonic ambient diffuser with warm candlelight illumination.',
                'description' => "Transforms your studio atmosphere with whisper-quiet ultrasonic atomization and customizable warm ambient glow.",
                'specs' => [
                    'Capacity' => '300ml (Up to 12 Hours Continuous)',
                    'Sound Level' => '< 19 dB Whisper Silent',
                    'Lighting' => '1800K Warm Candlelight Glow',
                ],
                'material' => 'Hand-Blown Borosilicate Glass & Beech Wood Base',
                'country_of_origin' => 'Sweden',
                'is_variable' => false,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'galleries' => [
                    $valSilver->id => [
                        'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                'variants' => [
                    ['sku' => 'AUR-DIF-GLS', 'attrs' => [$valSilver->id], 'stock' => 32, 'price_override' => null],
                ],
            ],
        ];

        // 7. Seed each product with local image files, variants, and stock
        $this->command?->info("Seeding 18 luxury products with local image sets...");

        foreach ($productsCatalog as $pData) {
            $prodDir = "{$productDir}/{$pData['slug']}";
            File::ensureDirectoryExists($prodDir);

            $p = Product::updateOrCreate(['slug' => $pData['slug']], [
                'name' => $pData['name'],
                'base_sku' => $pData['base_sku'],
                'barcode' => $pData['barcode'],
                'brand_id' => $brands[$pData['brand']]->id,
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'],
                'cost_price' => round($pData['price'] * 0.5),
                'tax_rate' => 18.00,
                'short_description' => $pData['short_description'],
                'description' => $pData['description'],
                'specifications_json' => $pData['specs'],
                'material' => $pData['material'],
                'country_of_origin' => $pData['country_of_origin'],
                'is_variable' => $pData['is_variable'],
                'status' => 'active',
                'is_featured' => $pData['is_featured'],
                'is_bestseller' => $pData['is_bestseller'],
                'is_new_arrival' => $pData['is_new_arrival'],
                'seo_title' => "{$pData['name']} — ATOZ GADGETZ",
                'seo_description' => $pData['short_description'],
            ]);

            // Sync category
            if (isset($categories[$pData['category']])) {
                $p->categories()->sync([$categories[$pData['category']]->id]);
            }

            // Download & Save Galleries Per Colour
            $imgSort = 1;
            foreach ($pData['galleries'] as $attrValId => $urls) {
                foreach ($urls as $uIdx => $u) {
                    $ext = 'jpg';
                    $localFileName = "img-{$attrValId}-{$uIdx}.{$ext}";
                    $localFilePath = "{$prodDir}/{$localFileName}";
                    
                    $downloadAsset($u, $localFilePath);
                    $webPath = "/storage/products/{$pData['slug']}/{$localFileName}";

                    ProductImage::updateOrCreate([
                        'product_id' => $p->id,
                        'image_path' => $webPath,
                    ], [
                        'attribute_value_id' => $attrValId,
                        'alt_text' => "{$p->name} in atelier angle {$imgSort}",
                        'sort_order' => $imgSort,
                        'is_primary' => ($imgSort === 1),
                    ]);
                    $imgSort++;
                }
            }

            // Sync Variants & Stock
            foreach ($pData['variants'] as $v) {
                $variant = ProductVariant::updateOrCreate(['sku' => $v['sku']], [
                    'product_id' => $p->id,
                    'price_override' => $v['price_override'],
                    'status' => 'active',
                ]);
                $variant->attributeValues()->sync($v['attrs']);

                $inv = Inventory::updateOrCreate(['product_variant_id' => $variant->id], [
                    'quantity' => $v['stock'],
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                ]);

                InventoryMovement::firstOrCreate([
                    'inventory_id' => $inv->id,
                    'type' => 'purchase',
                ], [
                    'change_amount' => $v['stock'],
                    'previous_qty' => 0,
                    'new_qty' => $v['stock'],
                    'admin_id' => 1,
                    'notes' => 'Master Catalogue Seeding Stock',
                ]);
            }

            // Verified Reviews
            Review::firstOrCreate([
                'product_id' => $p->id,
                'author_name' => 'Alexander V.',
            ], [
                'rating' => 5,
                'title' => 'Masterpiece of industrial geometry',
                'comment' => "The tactile finish and acoustic performance surpasses anything in this class. Truly exceptional craftsmanship.",
                'is_verified_purchase' => true,
                'is_approved' => true,
            ]);
        }

        $this->command?->info("Master Production Seeding completed successfully!");
    }
}
