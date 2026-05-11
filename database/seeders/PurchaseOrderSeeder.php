<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 15; $i++) {
            $status = ['draft', 'approved', 'received'][rand(0, 2)];
            $orderedAt = Carbon::now()->subDays(rand(1, 30));
            $receivedAt = $status === 'received' ? $orderedAt->copy()->addDays(rand(1, 7)) : null;

            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'supplier_id' => $suppliers->random()->id,
                'status' => $status,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'ordered_at' => $orderedAt,
                'received_at' => $receivedAt,
            ]);

            $subtotal = 0;
            $itemsCount = rand(1, 5);
            
            // Handle cases where products count might be less than rand(1, 5)
            $count = min($itemsCount, $products->count());
            $selectedProducts = $products->random($count);

            foreach ($selectedProducts as $product) {
                $qty = rand(10, 100);
                $unitPrice = $product->cost_price;
                $total = $qty * $unitPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ]);

                $subtotal += $total;
            }

            $tax = $subtotal * 0.15; // 15% VAT
            $po->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        }
    }
}
