<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSuppliers = Supplier::count();
        $totalProducts = Product::count();
        $inventoryValue = Product::select(DB::raw('SUM(stock_qty * cost_price) as value'))->value('value') ?? 0;
        $totalPOs = PurchaseOrder::count();
        $lowStockItems = Product::whereColumn('stock_qty', '<', 'min_stock')->count();
        $totalPayables = Supplier::sum('balance');

        $recentPOs = PurchaseOrder::with('supplier')->latest()->take(5)->get();

        // Monthly Purchases (Last 6 months)
        $monthlyPurchases = PurchaseOrder::select(
            DB::raw('SUM(total) as total'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
        ->where('status', 'received') // Only received POs count as purchases? Or all? User said "Monthly Purchases". Usually means realized.
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->take(6)
        ->get();

        // Inventory Movements (Last 30 days)
        $movementHistory = InventoryMovement::select(
            DB::raw('COUNT(*) as count'),
            DB::raw("DATE(created_at) as date")
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $lowStockProducts = Product::whereColumn('stock_qty', '<', 'min_stock')->get();

        return view('dashboard', compact(
            'totalSuppliers',
            'totalProducts',
            'inventoryValue',
            'totalPOs',
            'lowStockItems',
            'totalPayables',
            'recentPOs',
            'monthlyPurchases',
            'movementHistory',
            'lowStockProducts'
        ));
    }
}
