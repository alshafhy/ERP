<?php

namespace App\Actions;

use App\Models\PurchaseOrder;
use App\Models\InventoryMovement;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceivePurchaseOrderAction
{
    public function execute(PurchaseOrder $purchaseOrder)
    {
        return DB::transaction(function () use ($purchaseOrder) {
            if ($purchaseOrder->status !== 'approved') {
                throw new \Exception('Only approved purchase orders can be received.');
            }

            // 1. Set PO status = received, received_at = now()
            $purchaseOrder->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            // 2. Process each product
            foreach ($purchaseOrder->items as $item) {
                $product = $item->product;
                
                // Increment stock_qty on each product
                $product->increment('stock_qty', $item->qty);

                // Create InventoryMovement records (type=in)
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $purchaseOrder->id,
                    'qty' => $item->qty,
                    'balance_after' => $product->stock_qty,
                ]);
            }

            // 3. Create one JournalEntry (Debit: Inventory / Credit: Accounts Payable)
            JournalEntry::create([
                'entry_number' => 'JE-' . strtoupper(Str::random(8)),
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $purchaseOrder->id,
                'description' => "استلام أمر شراء رقم {$purchaseOrder->po_number}",
                'debit_account' => 'Inventory',
                'credit_account' => 'Accounts Payable',
                'amount' => $purchaseOrder->total,
            ]);

            // 4. Increment supplier balance
            $purchaseOrder->supplier->increment('balance', $purchaseOrder->total);

            return $purchaseOrder;
        });
    }
}
