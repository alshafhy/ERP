<?php

namespace App\Http\Controllers;

use App\Models\VehicleStatusLog;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = VehicleStatusLog::with(['vehicle', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('vehicle', function ($q) use ($search) {
                    $q->where('make', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('vin', 'like', "%{$search}%");
                });
            })
            ->latest('changed_at')
            ->paginate(20);

        return view('inventory-movements.index', compact('movements'));
    }
}
