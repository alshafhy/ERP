<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Vehicle;
use App\Models\Lead;
use App\Models\DealPayment;
use App\Models\DealInstallment;
use App\Models\User;
use App\Models\Branch;
use App\Exports\SalesReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Reports and Dashboard Hub.
     */
    public function index(Request $request)
    {
        // Enforce administrative access
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager', 'finance_officer'])) {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة التقارير.');
        }

        // Date Range Filters: Default to current month if empty
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfMonth();

        // 1. KPI Metric Calculations
        // Vehicles Stock Count
        $totalInStock = Vehicle::where('status', 'available')->count();
        $soldThisMonth = Vehicle::whereHas('deal', function ($q) use ($startDate, $endDate) {
            $q->whereIn('status', ['delivered', 'closed'])
              ->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        // Financial metrics
        $revenueCollected = DealPayment::whereBetween('paid_at', [$startDate, $endDate])->sum('amount');
        
        $activeDeals = Deal::whereIn('status', ['approved', 'contract_signed', 'delivered', 'closed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $totalDealValue = $activeDeals->sum('final_price');
        $outstandingPayments = max(0, $totalDealValue - DealPayment::sum('amount'));

        // Lead metrics
        $newLeadsThisWeek = Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedLeads = Lead::where('status', 'converted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        // Top Performing Salesperson
        $topAgentQuery = Deal::select('salesperson_id', DB::raw('SUM(final_price) as total_sales'), DB::raw('COUNT(id) as deals_count'))
            ->whereIn('status', ['delivered', 'closed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('salesperson_id')
            ->orderBy('total_sales', 'desc')
            ->first();

        $topSalesperson = null;
        if ($topAgentQuery) {
            $topSalesperson = [
                'user' => User::find($topAgentQuery->salesperson_id),
                'total_sales' => $topAgentQuery->total_sales,
                'deals_count' => $topAgentQuery->deals_count,
            ];
        }

        // 2. Charts Data Compilation
        // Monthly Sales Bar Chart (last 6 months)
        $salesMonths = [];
        $salesValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthValue = Deal::whereIn('status', ['approved', 'contract_signed', 'delivered', 'closed'])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('final_price');

            $salesMonths[] = $monthStart->format('Y-M');
            $salesValues[] = (float)$monthValue;
        }

        // Lead status funnel counts
        $funnelStatuses = ['new', 'contacted', 'qualified', 'lost', 'converted'];
        $funnelData = [];
        foreach ($funnelStatuses as $status) {
            $funnelData[] = Lead::where('status', $status)->count();
        }

        // 3. Tabular reports preparation
        // Sales report
        $salesDeals = Deal::with(['customer', 'vehicle', 'salesperson', 'branch'])
            ->whereIn('status', ['delivered', 'closed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Inventory report
        $inventoryVehicles = Vehicle::with(['branch'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Payment report
        $overdueInstallments = DealInstallment::with(['deal.customer', 'deal.vehicle'])
            ->where('status', 'overdue')
            ->orderBy('due_at', 'asc')
            ->limit(10)
            ->get();

        // Lead source funnel conversions
        $leadSourceStats = Lead::select('source', DB::raw('COUNT(id) as total'), DB::raw('SUM(CASE WHEN status="converted" THEN 1 ELSE 0 END) as converted'))
            ->groupBy('source')
            ->get();

        // Dropdowns for filters
        $agents = User::role('sales_agent')->get();
        $branches = Branch::all();

        return view('reports.index', compact(
            'startDate', 'endDate',
            'totalInStock', 'soldThisMonth',
            'revenueCollected', 'outstandingPayments',
            'newLeadsThisWeek', 'conversionRate',
            'topSalesperson',
            'salesMonths', 'salesValues',
            'funnelStatuses', 'funnelData',
            'salesDeals', 'inventoryVehicles',
            'overdueInstallments', 'leadSourceStats',
            'agents', 'branches'
        ));
    }

    /**
     * Trigger Excel download for Sales Report.
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $agentId = $request->input('agent_id');
        $branchId = $request->input('branch_id');

        return Excel::download(
            new SalesReportExport($startDate, $endDate, $agentId, $branchId),
            'sales_report_' . date('Y-m-d') . '.xlsx'
        );
    }
}
