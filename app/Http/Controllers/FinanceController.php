<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealInstallment;
use App\Models\DealPayment;
use App\Models\User;
use App\Jobs\CheckOverdueInstallments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    /**
     * Display the Finance & Payments Dashboard.
     */
    public function index(Request $request)
    {
        // Enforce administrative or financial access controls
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403, 'غير مصرح لك بالوصول إلى الوحدة المالية.');
        }

        // Closed/Delivered/Approved Deals represent real revenue
        $revenueDeals = Deal::whereIn('status', ['approved', 'contract_signed', 'delivered', 'closed'])->get();
        $totalRevenue = $revenueDeals->sum('final_price');

        // Total payments collected
        $totalCollected = DealPayment::sum('amount');
        
        // Outstanding amount
        $totalOutstanding = max(0, $totalRevenue - $totalCollected);

        // Installment metrics
        $overdueCount = DealInstallment::where('status', 'overdue')->count();
        $paidInstallmentsValue = DealInstallment::where('status', 'paid')->sum('amount');
        $upcomingInstallmentsValue = DealInstallment::where('status', 'upcoming')->sum('amount');

        // Chronological list of installments (filtered by status)
        $installmentsQuery = DealInstallment::with(['deal.customer', 'deal.vehicle']);
        if ($request->filled('status')) {
            $installmentsQuery->where('status', $request->input('status'));
        }
        $installments = $installmentsQuery->orderBy('due_at', 'asc')->paginate(15)->withQueryString();

        // Commission Rates: cash = 1.5%, financing/trade_in = 2.0%, installment = 3.5%
        $commissions = [];
        $salespersons = User::role(['sales_agent', 'branch_manager', 'super_admin', 'super-admin'])->with(['deals' => function ($q) {
            $q->whereIn('status', ['delivered', 'closed']);
        }])->get();

        foreach ($salespersons as $person) {
            $dealsClosed = $person->deals;
            $closedCount = $dealsClosed->count();
            $totalSalesValue = $dealsClosed->sum('final_price');
            $calculatedCommission = 0;

            foreach ($dealsClosed as $d) {
                $rate = 0.015; // default 1.5%
                if ($d->deal_type === 'installment') {
                    $rate = 0.035; // 3.5%
                } elseif ($d->deal_type === 'financing' || $d->deal_type === 'trade_in') {
                    $rate = 0.02; // 2.0%
                }
                $calculatedCommission += ($d->final_price * $rate);
            }

            if ($closedCount > 0) {
                $commissions[] = [
                    'salesperson' => $person,
                    'closed_count' => $closedCount,
                    'total_sales' => $totalSalesValue,
                    'commission' => $calculatedCommission,
                ];
            }
        }

        return view('finance.index', compact(
            'totalRevenue',
            'totalCollected',
            'totalOutstanding',
            'overdueCount',
            'paidInstallmentsValue',
            'upcomingInstallmentsValue',
            'installments',
            'commissions'
        ));
    }

    /**
     * Trigger manual scan and update overdue installments.
     */
    public function scanOverdue()
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403);
        }

        // Run scanning job synchronously
        $job = new CheckOverdueInstallments();
        $job->handle();

        return redirect()->route('finance.index')
            ->with('success', 'تم تشغيل فاحص الأقساط بنجاح، وتحديث الحالات المتأخرة تلقائياً في قاعدة البيانات وإرسال التنبيهات.');
    }

    /**
     * Download entire Deal Invoice showing all payment traces and installment progress.
     */
    public function downloadInvoice(Deal $deal)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403);
        }

        $deal->load(['customer', 'vehicle', 'payments', 'installments']);

        $pdf = Pdf::loadView('deals.invoice_pdf', compact('deal'));
        
        return $pdf->download("invoice_deal_{$deal->id}.pdf");
    }
}
