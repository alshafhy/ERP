<?php

namespace App\Actions;

use App\Models\PurchaseOrder;
use App\Models\JournalEntry;
use App\Models\VehicleStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceivePurchaseOrderAction
{
    public function execute(PurchaseOrder $purchaseOrder)
    {
        return DB::transaction(function () use ($purchaseOrder) {
            if ($purchaseOrder->delivered_at !== null) {
                throw new \Exception('أمر الشراء هذا مستلم بالفعل.');
            }

            // 1. Mark as delivered
            $purchaseOrder->update([
                'delivered_at' => now(),
            ]);

            // 2. Update vehicle status to available
            $vehicle = $purchaseOrder->vehicle;
            if ($vehicle) {
                $oldStatus = $vehicle->status;
                $vehicle->update(['status' => 'available']);

                // Create status log
                VehicleStatusLog::create([
                    'vehicle_id' => $vehicle->id,
                    'status_from' => $oldStatus,
                    'status_to' => 'available',
                    'changed_by' => Auth::id() ?? 1,
                    'notes' => 'تم استلام السيارة وتحديث حالتها إلى متاحة بموجب أمر الشراء رقم #' . $purchaseOrder->id,
                    'changed_at' => now(),
                ]);
            }

            // 3. Create one JournalEntry (Debit: المخزون / Credit: الذمم الدائنة)
            JournalEntry::create([
                'entry_number' => 'JE-' . date('Ymd') . '-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT),
                'reference_type' => 'purchase_order',
                'reference_id' => $purchaseOrder->id,
                'description' => "استلام أمر شراء سيارة رقم #{$purchaseOrder->id}",
                'debit_account' => 'المخزون',
                'credit_account' => 'الذمم الدائنة',
                'amount' => $purchaseOrder->purchase_price,
            ]);

            // 4. Increment supplier balance
            if ($purchaseOrder->supplier) {
                $purchaseOrder->supplier->increment('balance', $purchaseOrder->purchase_price);
            }

            return $purchaseOrder;
        });
    }
}
