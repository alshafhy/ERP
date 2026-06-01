<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'شركة تويوتا عبد اللطيف جميل', 'contact' => 'تلفون: 0501234567، بريد: info@alj-toyota.sa', 'type' => 'brand', 'balance' => 0.00],
            ['name' => 'شركة المجدوعي للسيارات', 'contact' => 'تلفون: 0502345678، بريد: contact@almajdouie.com', 'type' => 'brand', 'balance' => 150000.00],
            ['name' => 'مزاد الرياض الدولي للسيارات', 'contact' => 'تلفون: 0503456789، بريد: sales@riyadh-auction.com', 'type' => 'auction', 'balance' => 0.00],
            ['name' => 'معرض صالح للسيارات المستعملة', 'contact' => 'تلفون: 0504567890، بريد: purchase@saleh-cars.sa', 'type' => 'private', 'balance' => -25000.00],
            ['name' => 'شركة توكيلات الجزيرة (فورد)', 'contact' => 'تلفون: 0505678901، بريد: info@aljazirah-ford.com', 'type' => 'brand', 'balance' => 75000.00],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
