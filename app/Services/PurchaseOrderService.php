<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function getAllPurchaseOrders($search = null)
    {
        return PurchaseOrder::with('supplier')
            ->when($search, function ($query, $search) {
                $query->where('po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);
    }

    public function createPurchaseOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            $subtotal = 0;

            foreach ($items as $item) {
                $subtotal += $item['qty'] * $item['unit_price'];
            }

            $tax = $subtotal * 0.15; // Assuming 15% tax
            $total = $subtotal + $tax;

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => 'PO-' . strtoupper(Str::random(8)),
                'supplier_id' => $data['supplier_id'],
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'ordered_at' => now(),
            ]);

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['qty'] * $item['unit_price'],
                ]);
            }

            return $purchaseOrder;
        });
    }

    public function updateStatus(PurchaseOrder $purchaseOrder, string $status)
    {
        return DB::transaction(function () use ($purchaseOrder, $status) {
            $oldStatus = $purchaseOrder->status;
            $purchaseOrder->update(['status' => $status]);

            if ($status === 'received' && $oldStatus !== 'received') {
                $purchaseOrder->update(['received_at' => now()]);
                
                // Update product stock and supplier balance
                foreach ($purchaseOrder->items as $item) {
                    $item->product->increment('stock_qty', $item->qty);
                }
                
                $purchaseOrder->supplier->increment('balance', $purchaseOrder->total);
            }

            return $purchaseOrder;
        });
    }
}
