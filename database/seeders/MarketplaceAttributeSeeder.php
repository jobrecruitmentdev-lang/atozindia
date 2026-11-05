<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class MarketplaceAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Colour Attribute
        $attrColor = Attribute::firstOrCreate(['code' => 'color'], [
            'name' => 'Colour',
            'type' => 'color',
        ]);

        $colors = [
            ['value' => 'Obsidian Black', 'hex' => '#111110', 'sort' => 1],
            ['value' => 'Pure White', 'hex' => '#FFFFFF', 'sort' => 2],
            ['value' => 'Charcoal Grey', 'hex' => '#374151', 'sort' => 3],
            ['value' => 'Navy Blue', 'hex' => '#1E3A8A', 'sort' => 4],
            ['value' => 'Crimson Red', 'hex' => '#DC2626', 'sort' => 5],
            ['value' => 'Olive Green', 'hex' => '#4D7C0F', 'sort' => 6],
            ['value' => 'Beige / Khaki', 'hex' => '#D6C0B3', 'sort' => 7],
            ['value' => 'Classic Brown', 'hex' => '#78350F', 'sort' => 8],
            ['value' => 'Rose Gold', 'hex' => '#B76E79', 'sort' => 9],
            ['value' => 'Alabaster Silver', 'hex' => '#E5E7EB', 'sort' => 10],
            ['value' => 'Champagne Gold', 'hex' => '#D4AF37', 'sort' => 11],
        ];

        foreach ($colors as $c) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $attrColor->id, 'value' => $c['value']],
                ['color_hex' => $c['hex'], 'sort_order' => $c['sort']]
            );
        }

        // 2. Size Attribute
        $attrSize = Attribute::firstOrCreate(['code' => 'size'], [
            'name' => 'Size',
            'type' => 'button',
        ]);

        $sizes = [
            // Apparel & Clothing (e.g. Socks, Shirts, Kurtas)
            ['value' => 'Free Size', 'sort' => 1],
            ['value' => 'XS', 'sort' => 2],
            ['value' => 'S', 'sort' => 3],
            ['value' => 'M', 'sort' => 4],
            ['value' => 'L', 'sort' => 5],
            ['value' => 'XL', 'sort' => 6],
            ['value' => 'XXL', 'sort' => 7],
            // Footwear
            ['value' => 'UK 6', 'sort' => 10],
            ['value' => 'UK 7', 'sort' => 11],
            ['value' => 'UK 8', 'sort' => 12],
            ['value' => 'UK 9', 'sort' => 13],
            ['value' => 'UK 10', 'sort' => 14],
            ['value' => 'UK 11', 'sort' => 15],
            // Packs & Quantities
            ['value' => 'Standard', 'sort' => 20],
            ['value' => 'Pack of 2', 'sort' => 21],
            ['value' => 'Pack of 3', 'sort' => 22],
            ['value' => 'Pack of 5', 'sort' => 23],
            ['value' => '250g', 'sort' => 30],
            ['value' => '500g', 'sort' => 31],
            ['value' => '1 kg', 'sort' => 32],
        ];

        foreach ($sizes as $s) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $attrSize->id, 'value' => $s['value']],
                ['sort_order' => $s['sort']]
            );
        }

        // 3. Storage Attribute (Electronics & Gadgets only)
        $attrStorage = Attribute::firstOrCreate(['code' => 'storage'], [
            'name' => 'Storage',
            'type' => 'button',
        ]);

        $storages = [
            ['value' => '64 GB', 'sort' => 1],
            ['value' => '128 GB', 'sort' => 2],
            ['value' => '256 GB', 'sort' => 3],
            ['value' => '512 GB', 'sort' => 4],
            ['value' => '1 TB', 'sort' => 5],
            ['value' => '2 TB', 'sort' => 6],
        ];

        foreach ($storages as $st) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $attrStorage->id, 'value' => $st['value']],
                ['sort_order' => $st['sort']]
            );
        }
    }
}
