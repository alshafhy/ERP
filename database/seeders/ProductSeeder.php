<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'إلكترونيات' => ['لابتوب برو', 'ماوس لاسلكي', 'شاشة HD', 'لوحة مفاتيح ميكانيكية', 'قاعدة توصيل USB-C', 'كاميرا ويب 4K', 'سماعات عازلة للضوضاء', 'ساعة ذكية'],
            'مواد خام' => ['صفيحة فولاذ 2مم', 'قضيب ألمنيوم', 'سلك نحاس 100م', 'حبيبات بلاستيك', 'لوح زجاج 4x4', 'خشب بلوط', 'قضيب حديد', 'لفة قماش قطن'],
            'أدوات مكتبية' => ['صندوق ورق A4', 'حبر طابعة أسود', 'مجموعة أقلام جيل', 'ملف مستندات أزرق', 'دباسة شديدة التحمل', 'ملاحظات لاصقة', 'دفتر كبير', 'قلم سبورة'],
            'إكسسوارات' => ['حقيبة لابتوب', 'بساط مكتب كبير', 'مسند هاتف', 'مسند شاشة', 'منظم كابلات', 'لوحة ماوس', 'مسند سماعة', 'مسند قدم مريح'],
            'مواد تغليف' => ['صندوق كرتون متوسط', 'لفة فقاعات هوائية', 'فيلم تغليف', 'شريط لاصق 50م', 'ملصقات شحن', 'مغلف بلاستيكي', 'مغلف مبطن', 'طبلية خشبية'],
        ];

        foreach ($categories as $category => $items) {
            foreach ($items as $index => $name) {
                Product::create([
                    'sku' => strtoupper(substr(Str::slug($category), 0, 1) ?: 'P') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4)),
                    'name' => $name,
                    'category' => $category,
                    'unit' => $this->getUnit($category),
                    'cost_price' => rand(10, 500) + (rand(0, 99) / 100),
                    'sell_price' => rand(600, 1500) + (rand(0, 99) / 100),
                    'stock_qty' => rand(0, 50),
                    'min_stock' => rand(20, 30),
                ]);
            }
        }
    }

    private function getUnit($category)
    {
        return match ($category) {
            'إلكترونيات', 'إكسسوارات', 'أدوات مكتبية' => 'قطعة',
            'مواد خام' => 'كجم',
            'مواد تغليف' => 'لفة',
            default => 'وحدة',
        };
    }
}
