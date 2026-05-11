<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ERPSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo user if not exists
        User::updateOrCreate(
            ['email' => 'admin@erp.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Create Suppliers
        $suppliers = [
            ['name' => 'Global Tech Solutions', 'email' => 'contact@globaltech.com', 'phone' => '+123456789', 'balance' => 1500.00],
            ['name' => 'Prime Logistics', 'email' => 'info@primelog.com', 'phone' => '+987654321', 'balance' => 0.00],
        ];

        foreach ($suppliers as $s) {
            Supplier::create($s);
        }

        // Create Products
        $products = [
            ['sku' => 'LAP-001', 'name' => 'MacBook Pro 14"', 'category' => 'Laptops', 'unit' => 'Pc', 'cost_price' => 1800.00, 'sell_price' => 2200.00, 'stock_qty' => 10, 'min_stock' => 5],
            ['sku' => 'MOU-002', 'name' => 'Logitech MX Master 3S', 'category' => 'Accessories', 'unit' => 'Pc', 'cost_price' => 80.00, 'sell_price' => 120.00, 'stock_qty' => 50, 'min_stock' => 10],
            ['sku' => 'MON-003', 'name' => 'Dell UltraSharp 27"', 'category' => 'Monitors', 'unit' => 'Pc', 'cost_price' => 450.00, 'sell_price' => 600.00, 'stock_qty' => 3, 'min_stock' => 5],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
