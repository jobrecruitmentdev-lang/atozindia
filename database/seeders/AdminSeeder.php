<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        // Super Admin Account
        Admin::updateOrCreate(
            ['email' => 'benzo@atozind.com'],
            [
                'name' => 'Benzo Administrator',
                'password' => Hash::make('Hostinger ki masi 4786'),
                'role_id' => $superAdminRole?->id,
                'is_super_admin' => true,
                'status' => 'active',
            ]
        );

        // Store Manager Account
        $managerRole = Role::where('slug', 'store-manager')->first();
        Admin::updateOrCreate(
            ['email' => 'manager@atozgadgetz.com'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('Manager@12345'),
                'role_id' => $managerRole?->id,
                'is_super_admin' => false,
                'status' => 'active',
            ]
        );

        // Test Customer Account
        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Julian Croft',
                'phone' => '+91 98765 43210',
                'password' => Hash::make('Customer@12345'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );

        // Create Customer Address
        $customer->addresses()->updateOrCreate(
            ['address_line_1' => 'Penthouse 14B, Skyview Towers'],
            [
                'type' => 'both',
                'first_name' => 'Julian',
                'last_name' => 'Croft',
                'company' => 'Aether Dynamics',
                'address_line_2' => 'Golf Course Extension Road',
                'landmark' => 'Grand Hyatt Residences',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'postal_code' => '122002',
                'country' => 'IN',
                'phone' => '+91 98765 43210',
                'is_default_shipping' => true,
                'is_default_billing' => true,
            ]
        );
    }
}
