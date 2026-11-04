<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'ATOZ GADGETZ', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_tagline', 'value' => 'Architectural Tech & Haute Gadgetry', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_email', 'value' => 'concierge@atozgadgetz.com', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_phone', 'value' => '+91 (0) 124 498 7000', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_address', 'value' => 'Skyview Towers, Tech Boulevard, Cyber City, Gurugram, India', 'group' => 'general', 'type' => 'text'],
            ['key' => 'currency', 'value' => 'INR', 'group' => 'pricing', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'pricing', 'type' => 'text'],
            ['key' => 'tax_rate', 'value' => '18', 'group' => 'pricing', 'type' => 'float'],
            ['key' => 'tax_included_in_price', 'value' => '1', 'group' => 'pricing', 'type' => 'boolean'],
            ['key' => 'free_shipping_threshold', 'value' => '2999', 'group' => 'shipping', 'type' => 'float'],
            ['key' => 'standard_shipping_rate', 'value' => '149', 'group' => 'shipping', 'type' => 'float'],
            ['key' => 'announcement_enabled', 'value' => '1', 'group' => 'announcement', 'type' => 'boolean'],
            ['key' => 'announcement_text', 'value' => 'Complimentary Express White-Glove Delivery on orders above ₹2,999', 'group' => 'announcement', 'type' => 'text'],
            ['key' => 'announcement_link', 'value' => '/shop', 'group' => 'announcement', 'type' => 'text'],
            ['key' => 'payment_cod_enabled', 'value' => '1', 'group' => 'payment', 'type' => 'boolean'],
            ['key' => 'payment_cod_min_order', 'value' => '499', 'group' => 'payment', 'type' => 'float'],
            ['key' => 'payment_cod_max_order', 'value' => '50000', 'group' => 'payment', 'type' => 'float'],
        ];

        foreach ($settings as $s) {
            StoreSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
