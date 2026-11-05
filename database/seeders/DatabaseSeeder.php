<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            StoreSettingsSeeder::class,
            CatalogSeeder::class,
            ComprehensiveCategorySeeder::class,
            MarketplaceCatalogSeeder::class,
        ]);
    }
}
