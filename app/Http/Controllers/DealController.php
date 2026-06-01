<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class DealController extends Controller
{
    /**
     * Display a listing of the deals.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Deal::class);

        $query = Deal::with(['customer', 'vehicle', 'salesperson']);

        // Limit sales agents to seeing only their own deals
        if (Auth::user()->hasRole('sales_agent')) {
            $query->where('salesperson_id', Auth::id());
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->input('deal_type'));
        }

        $deals = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('deals.index', compact('deals'));
    }

    /**
     * Show the form for creating a new deal.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Deal::class);

        $customers = Customer::orderBy('name')->get();
        
        // Show available or in-transit vehicles.
        $vehicles = Vehicle::whereIn('status', ['available', 'in_transit'])->get();
        
        $salespersons = User::role(['sales_agent', 'branch_manager', 'super_admin', 'super-admin'])->get();

        $selectedVehicleId = $request->input('vehicle_id');

        return view('deals.create', compact('customers', 'vehicles', 'salespersons', 'selectedVehicleId'));
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Deal::class);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'salesperson_id' => 'nullable|exists:users,id',
            'deal_type' => 'required|in:cash,installment,financing,trade_in',
            'agreed_price' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            
            // Trade-in details
            'trade_in_make' => 'nullable|required_if:deal_type,trade_in|string|max:100',
            'trade_in_model' => 'nullable|required_if:deal_type,trade_in|string|max:100',
            'trade_in_year' => 'nullable|required_if:deal_type,trade_in|integer|min:1900|max:' . (date('Y') + 1),
            'trade_in_vin' => 'nullable|required_if:deal_type,trade_in|string|max:50',
            'trade_in_value' => 'nullable|required_if:deal_type,trade_in|numeric|min:0',

            'status' => 'required|in:draft,pending_approval',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Auto-assign salesperson if agent
        if (Auth::user()->hasRole('sales_agent')) {
            $validated['salesperson_id'] = Auth::id();
        } else {
            $validated['salesperson_id'] = $validated['salesperson_id'] ?? Auth::id();
        }

        // Calculate final price
        $agreed = (float)$validated['agreed_price'];
        $discount = (float)$validated['discount'];
        $tradeIn = $validated['deal_type'] === 'trade_in' ? (float)($validated['trade_in_value'] ?? 0) : 0;
        $validated['final_price'] = max(0, $agreed - $discount - $tradeIn);

        // Verify if vehicle is available
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->status !== 'available' && $vehicle->status !== 'in_transit') {
            return back()->withInput()->with('error', 'هذه المركبة محجوزة أو مباعة بالفعل.');
        }

        $deal = Deal::create($validated);

        // Reserve vehicle status automatically
        $vehicle->update(['status' => 'reserved']);
        VehicleStatusLog::create([
            'vehicle_id' => $vehicle->id,
            'status_from' => 'available',
            'status_to' => 'reserved',
            'changed_by' => Auth::id(),
            'notes' => 'تم حجز المركبة تلقائياً بسبب تسجيل الصفقة رقم #' . $deal->id,
            'changed_at' => now(),
        ]);

        return redirect()->route('deals.show', $deal)
            ->with('success', 'تم إنشاء الصفقة وحفظها كمستند ' . ($deal->status === 'draft' ? 'مسودة' : 'بانتظار الاعتماد') . ' بنجاح.');
    }

    /**
     * Display a specific deal with its specifications and history timeline.
     */
    public function show(Deal $deal)
    {
        Gate::authorize('view', $deal);

        $deal->load(['customer', 'vehicle', 'salesperson', 'payments']);

        return view('deals.show', compact('deal'));
    }

    /**
     * Update the deal status / process the approval workflow.
     */
    public function updateStatus(Request $request, Deal $deal)
    {
        $action = $request->input('action'); // e.g. approve, sign_contract, deliver, close, cancel

        if ($action === 'approve') {
            Gate::authorize('approve', $deal);

            $deal->update(['status' => 'approved']);

            // Send notification
            $this->sendDealNotifications($deal, 'approved');

            return redirect()->route('deals.show', $deal)
                ->with('success', 'تم اعتماد وموافقة الصفقة بنجاح وتم إرسال الإشعارات للعميل.');
        }

        if ($action === 'sign_contract') {
            Gate::authorize('update', $deal);
            $deal->update(['status' => 'contract_signed']);
            return redirect()->route('deals.show', $deal)
                ->with('success', 'تم توقيع العقد بنجاح وعقد الصفقة رسمياً.');
        }

        if ($action === 'deliver') {
            Gate::authorize('update', $deal);
            
            $deal->update(['status' => 'delivered']);
            
            // Deliver vehicle
            $vehicle = $deal->vehicle;
            $oldStatus = $vehicle->status;
            $vehicle->update(['status' => 'sold']);

            VehicleStatusLog::create([
                'vehicle_id' => $vehicle->id,
                'status_from' => $oldStatus,
                'status_to' => 'sold',
                'changed_by' => Auth::id(),
                'notes' => 'تم تسليم المركبة للعميل ونقل الملكية بنجاح عبر الصفقة رقم #' . $deal->id,
                'changed_at' => now(),
            ]);

            // Send notification
            $this->sendDealNotifications($deal, 'delivered');

            return redirect()->route('deals.show', $deal)
                ->with('success', 'تم تسليم السيارة للعميل وتغيير حالة المخزن إلى مباعة بنجاح.');
        }

        if ($action === 'close') {
            Gate::authorize('update', $deal);
            $deal->update(['status' => 'closed']);
            return redirect()->route('deals.show', $deal)
                ->with('success', 'تم إغلاق ملف الصفقة نهائياً بنجاح.');
        }

        if ($action === 'cancel') {
            Gate::authorize('update', $deal);
            
            $deal->update(['status' => 'draft']);
            
            // Release vehicle
            $vehicle = $deal->vehicle;
            if ($vehicle->status === 'reserved') {
                $vehicle->update(['status' => 'available']);
                VehicleStatusLog::create([
                    'vehicle_id' => $vehicle->id,
                    'status_from' => 'reserved',
                    'status_to' => 'available',
                    'changed_by' => Auth::id(),
                    'notes' => 'تم فك حجز المركبة وإتاحتها للبيع لإلغاء أو إعادة تعديل الصفقة رقم #' . $deal->id,
                    'changed_at' => now(),
                ]);
            }

            return redirect()->route('deals.show', $deal)
                ->with('success', 'تم إلغاء الصفقة وإعادتها كمسودة وتسهيل حجز السيارة بالمستودع.');
        }

        return back()->with('error', 'الإجراء المطلوب غير صالح.');
    }

    /**
     * Download contract PDF using Laravel DomPDF.
     */
    public function downloadContract(Deal $deal)
    {
        Gate::authorize('view', $deal);

        $deal->load(['customer', 'vehicle', 'salesperson']);

        // PDF configuration for premium typography
        $pdf = Pdf::loadView('deals.pdf', compact('deal'));
        
        return $pdf->download("contract_deal_{$deal->id}.pdf");
    }

    /**
     * Simulate email/SMS notifications.
     */
    protected function sendDealNotifications(Deal $deal, string $action)
    {
        $customerName = $deal->customer->name;
        $phone = $deal->customer->phone;
        $email = $deal->customer->email ?? 'no-email@domain.com';
        $vehicleInfo = "{$deal->vehicle->make} {$deal->vehicle->model} ({$deal->vehicle->year})";

        if ($action === 'approved') {
            $msgSMS = "عزيزي {$customerName}، يسعدنا إبلاغك بأنه تم اعتماد طلب شراء سيارة {$vehicleInfo} بنجاح. سنقوم بالتواصل معك لتوقيع العقد.";
            $msgEmail = "تمت الموافقة الرسمية على الصفقة رقم #{$deal->id} لشراء مركبة {$vehicleInfo} بقيمة إجمالية " . number_format($deal->final_price, 2) . " ريال.";
        } else {
            $msgSMS = "مرحباً {$customerName}، تهانينا! تم تسليم سيارتك {$vehicleInfo} بنجاح. شكراً لاختيارك معرض عماد الدين للسيارات.";
            $msgEmail = "تهانينا! تم تسليم المركبة بنجاح وإغلاق الصفقة رقم #{$deal->id}. نأمل لك قيادة آمنة وسعيدة.";
        }

        // Log the messages for audit
        Log::info("CRM Notification SMS sent to {$phone}: {$msgSMS}");
        Log::info("CRM Notification Email sent to {$email}: {$msgEmail}");

        // Flash a readable notice in the response session so the user can verify notifications worked!
        session()->flash('notification_sent', [
            'sms' => $msgSMS,
            'email' => $msgEmail,
        ]);
    }
}
