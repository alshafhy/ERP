<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function getAllPurchaseOrders($search = null)
    {
        return PurchaseOrder::with(['supplier', 'vehicle'])
            ->when($search, function ($query, $search) {
                $query->whereHas('supplier', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('vehicle', function ($q) use ($search) {
                    $q->where('make', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('vin', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function createPurchaseOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $data['supplier_id'],
                'vehicle_id' => $data['vehicle_id'],
                'purchase_price' => $data['purchase_price'],
                'purchased_at' => $data['purchased_at'] ?? now(),
            ]);

            // Set vehicle status to in_transit when purchased
            $vehicle = Vehicle::find($data['vehicle_id']);
            if ($vehicle) {
                $vehicle->update(['status' => 'in_transit']);
            }

            return $purchaseOrder;
        });
    }

    public function updateStatus(PurchaseOrder $purchaseOrder, string $status)
    {
        return DB::transaction(function () use ($purchaseOrder, $status) {
            if ($status === 'received') {
                $purchaseOrder->update(['delivered_at' => now()]);
                
                $vehicle = $purchaseOrder->vehicle;
                if ($vehicle) {
                    $vehicle->update(['status' => 'available']);
                }
            }
            
            return $purchaseOrder;
        });
    }
}
