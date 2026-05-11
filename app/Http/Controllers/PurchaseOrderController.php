<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseOrderService;
use App\Actions\ReceivePurchaseOrderAction;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected \App\Services\PurchaseOrderService $purchaseOrderService,
        protected \App\Actions\ReceivePurchaseOrderAction $receivePurchaseOrderAction
    ) {}

    public function index(Request $request)
    {
        $purchaseOrders = $this->purchaseOrderService->getAllPurchaseOrders($request->search);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        $products = \App\Models\Product::all();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(\App\Http\Requests\StorePurchaseOrderRequest $request)
    {
        $this->purchaseOrderService->createPurchaseOrder($request->validated());
        return redirect()->route('purchase-orders.index')->with('success', 'تم إنشاء أمر الشراء بنجاح.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate(['status' => 'required|in:draft,approved,received']);
        $this->purchaseOrderService->updateStatus($purchaseOrder, $request->status);
        return back()->with('success', 'تم تحديث الحالة بنجاح.');
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
