<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Services\PurchaseOrderService;
use App\Actions\ReceivePurchaseOrderAction;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService,
        protected ReceivePurchaseOrderAction $receivePurchaseOrderAction
    ) {}

    public function index(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderService->getAllPurchaseOrders($request->search);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        // Vehicles that are not purchased yet or are new
        $vehicles = Vehicle::whereNotIn('id', PurchaseOrder::pluck('vehicle_id'))->get();
        return view('purchase-orders.create', compact('suppliers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'vehicle_id' => 'required|exists:vehicles,id|unique:purchase_orders,vehicle_id',
            'purchase_price' => 'required|numeric|min:0',
            'purchased_at' => 'nullable|date',
        ]);

        $this->purchaseOrderService->createPurchaseOrder($validated);
        return redirect()->route('purchase-orders.index')->with('success', 'تم إنشاء أمر الشراء بنجاح.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'vehicle']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        try {
            $this->receivePurchaseOrderAction->execute($purchaseOrder);
            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'تم استلام أمر الشراء بنجاح. تم تحديث المخزون والرصيد.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
