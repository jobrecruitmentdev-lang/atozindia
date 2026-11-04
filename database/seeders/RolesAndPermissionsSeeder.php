<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'Dashboard'],
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'group' => 'Dashboard'],

            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'Products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'Products'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'Products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'Products'],

            // Categories & Brands
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'group' => 'Catalog'],
            ['name' => 'Manage Brands', 'slug' => 'brands.manage', 'group' => 'Catalog'],
            ['name' => 'Manage Attributes', 'slug' => 'attributes.manage', 'group' => 'Catalog'],

            // Inventory
            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'group' => 'Inventory'],
            ['name' => 'Adjust Inventory', 'slug' => 'inventory.adjust', 'group' => 'Inventory'],

            // Orders
            ['name' => 'View Orders', 'slug' => 'orders.view', 'group' => 'Orders'],
            ['name' => 'Update Orders', 'slug' => 'orders.update', 'group' => 'Orders'],

            // Customers
            ['name' => 'View Customers', 'slug' => 'customers.view', 'group' => 'Customers'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'group' => 'Customers'],

            // Marketing
            ['name' => 'Manage Coupons', 'slug' => 'coupons.manage', 'group' => 'Marketing'],
            ['name' => 'Manage Banners', 'slug' => 'banners.manage', 'group' => 'Marketing'],
            ['name' => 'Manage Reviews', 'slug' => 'reviews.manage', 'group' => 'Marketing'],

            // System
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'System'],
            ['name' => 'Manage Admins', 'slug' => 'admins.manage', 'group' => 'System'],
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'group' => 'System'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Create Super Admin Role
        $superAdminRole = Role::updateOrCreate(['slug' => 'super-admin'], [
            'name' => 'Super Administrator',
            'description' => 'Full unrestricted system access',
        ]);
        $superAdminRole->permissions()->sync(Permission::all());

        // Create Store Manager Role
        $managerRole = Role::updateOrCreate(['slug' => 'store-manager'], [
            'name' => 'Store Manager',
            'description' => 'Manages catalog, inventory, and orders',
        ]);
        $managerPermissions = Permission::whereNotIn('group', ['System'])->get();
        $managerRole->permissions()->sync($managerPermissions);

        // Create Inventory Specialist Role
        $inventoryRole = Role::updateOrCreate(['slug' => 'inventory-manager'], [
            'name' => 'Inventory Manager',
            'description' => 'Manages stock and movements only',
        ]);
        $inventoryPermissions = Permission::where('group', 'Inventory')->get();
        $inventoryRole->permissions()->sync($inventoryPermissions);
    }
}
