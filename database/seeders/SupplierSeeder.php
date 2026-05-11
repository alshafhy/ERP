<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'شركة النور التجارية', 'phone' => '0501234567', 'email' => 'info@alnoor.com', 'address' => 'الرياض، شارع العليا', 'balance' => 15000.00],
            ['name' => 'مؤسسة الإمداد الحديث', 'phone' => '0502345678', 'email' => 'contact@modern-supply.com', 'address' => 'جدة، المنطقة الصناعية', 'balance' => 0.00],
            ['name' => 'العالمية للتوريدات', 'phone' => '0503456789', 'email' => 'sales@global-supplies.com', 'address' => 'الدمام، طريق الميناء', 'balance' => 5400.50],
            ['name' => 'التقنية المتقدمة', 'phone' => '0504567890', 'email' => 'tech@adv-tech.sa', 'address' => 'الرياض، حي السليمانية', 'balance' => -1200.00],
            ['name' => 'الخليج للمستلزمات', 'phone' => '0505678901', 'email' => 'info@gulf-items.com', 'address' => 'الخبر، العقربية', 'balance' => 25000.00],
            ['name' => 'شركة الوفاق اللوجستية', 'phone' => '0506789012', 'email' => 'ops@alwefaq.com', 'address' => 'جدة، حي الروضة', 'balance' => 3200.00],
            ['name' => 'مؤسسة التكامل العمراني', 'phone' => '0507890123', 'email' => 'admin@takmoul.com', 'address' => 'المدينة المنورة، حي سلطانة', 'balance' => 0.00],
            ['name' => 'الرواد للصناعة', 'phone' => '0508901234', 'email' => 'info@alrowad.com', 'address' => 'الجبيل، المنطقة الصناعية', 'balance' => 45000.00],
            ['name' => 'شركة المستقبل للتجارة', 'phone' => '0509012345', 'email' => 'future@trading.com', 'address' => 'بريدة، طريق الملك عبدالعزيز', 'balance' => 150.75],
            ['name' => 'مجموعة الصحراء المتحدة', 'phone' => '0500123456', 'email' => 'sales@united-sahara.com', 'address' => 'حائل، حي المحطة', 'balance' => 8900.00],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
