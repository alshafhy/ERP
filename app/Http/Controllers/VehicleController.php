<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles with advanced search and filters.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Vehicle::class);

        $query = Vehicle::query();

        // Search by make/model/trim/VIN
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('trim', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('make')) {
            $query->where('make', $request->input('make'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->input('fuel_type'));
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->input('transmission'));
        }

        // Year Range Filter
        if ($request->filled('year_start')) {
            $query->where('year', '>=', $request->input('year_start'));
        }
        if ($request->filled('year_end')) {
            $query->where('year', '<=', $request->input('year_end'));
        }

        // Price Range Filter
        if ($request->filled('price_min')) {
            $query->where('listing_price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('listing_price', '<=', $request->input('price_max'));
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        $vehicles = $query->paginate(12)->withQueryString();

        // Distinct makes for filter dropdown
        $makes = Vehicle::distinct()->pluck('make');

        return view('vehicles.index', compact('vehicles', 'makes'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        Gate::authorize('create', Vehicle::class);

        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage with Spatie Media upload.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Vehicle::class);

        $validated = $request->validate([
            'vin' => 'required|string|size:17|unique:vehicles,vin',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'trim' => 'nullable|string|max:255',
            'color' => 'required|string|max:255',
            'mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|string|max:255',
            'transmission' => 'required|string|max:255',
            'condition' => 'required|in:new,used',
            'status' => 'required|in:available,reserved,sold,in_transit,damaged',
            'cost_price' => 'required|numeric|min:0',
            'listing_price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|max:4096', // Max 4MB per image
        ]);

        $vehicle = Vehicle::create($validated);

        // Upload images to Spatie Media Library
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $vehicle->addMedia($image)->toMediaCollection('images');
            }
        }

        // Log initial status change
        VehicleStatusLog::create([
            'vehicle_id' => $vehicle->id,
            'status_from' => null,
            'status_to' => $vehicle->status,
            'changed_by' => Auth::id(),
            'notes' => 'تم إضافة المركبة إلى المخزون كحالة ابتدائية.',
            'changed_at' => now(),
        ]);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'تم إضافة السيارة للمخزون بنجاح.');
    }

    /**
     * Display the specified vehicle with stock aging and status log.
     */
    public function show(Vehicle $vehicle)
    {
        Gate::authorize('view', $vehicle);

        $vehicle->load(['statusLogs.user']);

        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        Gate::authorize('update', $vehicle);

        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        Gate::authorize('update', $vehicle);

        $validated = $request->validate([
            'vin' => ['required', 'string', 'size:17', Rule::unique('vehicles', 'vin')->ignore($vehicle->id)],
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'trim' => 'nullable|string|max:255',
            'color' => 'required|string|max:255',
            'mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|string|max:255',
            'transmission' => 'required|string|max:255',
            'condition' => 'required|in:new,used',
            'status' => 'required|in:available,reserved,sold,in_transit,damaged',
            'cost_price' => 'required|numeric|min:0',
            'listing_price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $oldStatus = $vehicle->status;
        $vehicle->update($validated);

        // Upload images to Spatie Media Library
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $vehicle->addMedia($image)->toMediaCollection('images');
            }
        }

        // Log status change if updated
        if ($oldStatus !== $vehicle->status) {
            VehicleStatusLog::create([
                'vehicle_id' => $vehicle->id,
                'status_from' => $oldStatus,
                'status_to' => $vehicle->status,
                'changed_by' => Auth::id(),
                'notes' => 'تم تحديث حالة المركبة من شاشة تعديل المركبة.',
                'changed_at' => now(),
            ]);
        }

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'تم تعديل بيانات السيارة بنجاح.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        Gate::authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'تم حذف السيارة من المخزون بنجاح.');
    }

    /**
     * Dedicated action to update vehicle status (available -> reserved -> sold) with logs.
     */
    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        Gate::authorize('updateStatus', $vehicle);

        $validated = $request->validate([
            'status' => 'required|in:available,reserved,sold,in_transit,damaged',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $vehicle->status;

        if ($oldStatus === $validated['status']) {
            return redirect()->back()->with('error', 'الحالة الجديدة تطابق الحالة الحالية للمركبة.');
        }

        $vehicle->update(['status' => $validated['status']]);

        // Log status history
        VehicleStatusLog::create([
            'vehicle_id' => $vehicle->id,
            'status_from' => $oldStatus,
            'status_to' => $validated['status'],
            'changed_by' => Auth::id(),
            'notes' => $validated['notes'] ?? 'تم تغيير الحالة يدوياً من لوحة التحكم.',
            'changed_at' => now(),
        ]);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'تم تحديث حالة السيارة بنجاح وتوثيق التغيير.');
    }
}
