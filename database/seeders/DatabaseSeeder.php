<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed core administrative user
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'محمود اسعد سعد سعيد',
                'password' => Hash::make('Admin@123'),
                'position' => 'إداري سيادي',
                'phone' => '0500000000',
            ]
        );

        $this->call([
            PermissionSeeder::class,
            SupplierSeeder::class,
            VehicleSeeder::class,
            CustomerSeeder::class,
            LeadSeeder::class,
            DealSeeder::class,
        ]);
    }
}
