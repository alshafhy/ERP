<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'فيصل بن خالد العتيبي',
                'national_id' => '1029384756',
                'phone' => '0555555555',
                'email' => 'faisal@gmail.com',
                'address' => 'الرياض، المملكة العربية السعودية',
                'notes' => 'عميل يبحث عن سيارة عائلية فاخرة ويفضل الدفع نقداً.',
                'type' => 'individual',
            ],
            [
                'name' => 'شركة الوفاق لتأجير السيارات',
                'national_id' => '1010203040',
                'phone' => '0112223333',
                'email' => 'info@alwefaq.com',
                'address' => 'جدة، طريق المدينة',
                'notes' => 'حساب شركات مستمر، مهتمون بطلب أسطول سيارات دفع رباعي.',
                'type' => 'corporate',
            ],
            [
                'name' => 'سارة بنت محمد القحطاني',
                'national_id' => '1098765432',
                'phone' => '0544444444',
                'email' => 'sara.q@outlook.com',
                'address' => 'الدمام، حي الشاطئ',
                'notes' => 'ترغب في تمويل سيارة رياضية مع دفعة أولى 30%.',
                'type' => 'individual',
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
