<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Lead;
use App\Models\Supplier;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $reservedVehicles = Vehicle::where('status', 'reserved')->count();
        $soldVehicles = Vehicle::where('status', 'sold')->count();

        // Dealership inventory value
        $inventoryValue = Vehicle::whereIn('status', ['available', 'in_transit', 'reserved'])->sum('cost_price');

        // CRM Leads Count
        $leadsQuery = Lead::query();
        if (Auth::user()->hasRole('sales_agent')) {
            $leadsQuery->where('assigned_to', Auth::id());
        }
        $totalLeads = (clone $leadsQuery)->count();
        $activeLeads = (clone $leadsQuery)->whereIn('status', ['new', 'contacted', 'qualified'])->count();

        // Follow-up reminders: overdue follow-ups where follow_up_at is past and status is not lost/converted
        $overdueFollowUps = (clone $leadsQuery)
            ->whereIn('status', ['new', 'contacted', 'qualified'])
            ->whereNotNull('follow_up_at')
            ->where('follow_up_at', '<', now())
            ->with(['customer', 'vehicle'])
            ->orderBy('follow_up_at', 'asc')
            ->get();

        // Recent Deals
        $recentDeals = Deal::with(['customer', 'vehicle'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalVehicles',
            'availableVehicles',
            'reservedVehicles',
            'soldVehicles',
            'inventoryValue',
            'totalLeads',
            'activeLeads',
            'overdueFollowUps',
            'recentDeals'
        ));
    }
}
