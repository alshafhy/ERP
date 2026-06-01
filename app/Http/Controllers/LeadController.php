<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Deal;
use App\Models\User;
use App\Models\VehicleStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Display a Kanban pipeline or table listing of leads.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Lead::class);

        $query = Lead::with(['customer', 'vehicle', 'assignedAgent']);

        // Scope leads for sales agents
        if (Auth::user()->hasRole('sales_agent')) {
            $query->where('assigned_to', Auth::id());
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // We will fetch leads group by status for a beautiful Kanban board view
        $allLeads = $query->orderBy('updated_at', 'desc')->get();

        $pipeline = [
            'new' => $allLeads->where('status', 'new'),
            'contacted' => $allLeads->where('status', 'contacted'),
            'qualified' => $allLeads->where('status', 'qualified'),
            'lost' => $allLeads->where('status', 'lost'),
            'converted' => $allLeads->where('status', 'converted'),
        ];

        return view('leads.index', compact('pipeline'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        Gate::authorize('create', Lead::class);

        $customers = Customer::orderBy('name')->get();
        // Show only available/transit vehicles for interest selection
        $vehicles = Vehicle::whereIn('status', ['available', 'in_transit'])->get();
        // Salespersons
        $salespersons = User::role(['sales_agent', 'branch_manager', 'super_admin', 'super-admin'])->get();

        return view('leads.create', compact('customers', 'vehicles', 'salespersons'));
    }

    /**
     * Store a newly created lead in CRM.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Lead::class);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'source' => 'required|in:walk-in,website,referral,call',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:new,contacted,qualified,lost,converted',
            'follow_up_at' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Auto-assign to logged-in sales agent if they are creating the lead
        if (Auth::user()->hasRole('sales_agent')) {
            $validated['assigned_to'] = Auth::id();
        }

        $lead = Lead::create($validated);

        // Create first activity note
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'note',
            'description' => 'تم إنشاء الفرصة البيعية وإضافتها للنظام. ' . ($lead->notes ?? ''),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم إنشاء الفرصة البيعية بنجاح.');
    }

    /**
     * Display a specific lead with details, reminders, and activity timeline.
     */
    public function show(Lead $lead)
    {
        Gate::authorize('view', $lead);

        $lead->load(['customer', 'vehicle', 'assignedAgent', 'activities.creator']);

        return view('leads.show', compact('lead'));
    }

    /**
     * Show form to edit a lead.
     */
    public function edit(Lead $lead)
    {
        Gate::authorize('update', $lead);

        $customers = Customer::orderBy('name')->get();
        $vehicles = Vehicle::whereIn('status', ['available', 'in_transit'])->orWhere('id', $lead->vehicle_id)->get();
        $salespersons = User::role(['sales_agent', 'branch_manager', 'super_admin', 'super-admin'])->get();

        return view('leads.edit', compact('lead', 'customers', 'vehicles', 'salespersons'));
    }

    /**
     * Update the lead details.
     */
    public function update(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'source' => 'required|in:walk-in,website,referral,call',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:new,contacted,qualified,lost,converted',
            'follow_up_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        if ($oldStatus !== $lead->status) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'note',
                'description' => "تم تغيير حالة الفرصة البيعية من {$oldStatus} إلى {$lead->status}.",
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تحديث بيانات الفرصة البيعية.');
    }

    /**
     * Delete a lead.
     */
    public function destroy(Lead $lead)
    {
        Gate::authorize('delete', $lead);

        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'تم حذف الفرصة البيعية.');
    }

    /**
     * Log activities like call, message, note, meeting.
     */
    public function logActivity(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'type' => 'required|in:call,message,meeting,note',
            'description' => 'required|string|max:1000',
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['created_by'] = Auth::id();

        LeadActivity::create($validated);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تسجيل النشاط بنجاح.');
    }

    /**
     * Show form to convert lead to deal.
     */
    public function showConvertForm(Lead $lead)
    {
        Gate::authorize('update', $lead);

        if (!$lead->vehicle_id) {
            return redirect()->route('leads.show', $lead)
                ->with('error', 'يجب تحديد سيارة الاهتمام قبل تحويل الفرصة إلى صفقة.');
        }

        return view('leads.convert', compact('lead'));
    }

    /**
     * One-click (or simplified single-form submission) convert lead to closed/pending Deal.
     */
    public function convertToDeal(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'deal_type' => 'required|in:cash,installment,financing',
            'agreed_price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Create deal
        $deal = Deal::create([
            'vehicle_id' => $lead->vehicle_id,
            'customer_id' => $lead->customer_id,
            'salesperson_id' => $lead->assigned_to ?? Auth::id(),
            'deal_type' => $validated['deal_type'],
            'agreed_price' => $validated['agreed_price'],
            'discount' => $validated['discount'],
            'status' => $validated['status'],
        ]);

        // Reserve vehicle status
        $vehicle = $lead->vehicle;
        $vehicle->update(['status' => 'reserved']);

        // Log vehicle status log
        VehicleStatusLog::create([
            'vehicle_id' => $vehicle->id,
            'status_from' => 'available',
            'status_to' => 'reserved',
            'changed_by' => Auth::id(),
            'notes' => 'تم تغيير حالة المركبة إلى محجوزة بسبب تحويل الفرصة البيعية رقم #' . $lead->id . ' إلى صفقة.',
            'changed_at' => now(),
        ]);

        // Update Lead status to converted
        $lead->update(['status' => 'converted']);

        // Log conversion activity
        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'note',
            'description' => "🎉 تهانينا! تم تحويل الفرصة البيعية بنجاح إلى صفقة جديدة رقم #{$deal->id}.",
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('leads.show', $lead)
            ->with('success', 'تم تحويل العميل بنجاح إلى صفقة بيعية وتوثيق الحركة.');
    }
}
