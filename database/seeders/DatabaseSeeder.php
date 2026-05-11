<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'محمود اسعد سعد سعيد',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'position' => 'إداري سيادي',
            'phone' => '0500000000',
        ]);

        $this->call([
            PermissionSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            PurchaseOrderSeeder::class,
            JournalEntrySeeder::class,
        ]);
    }
}
