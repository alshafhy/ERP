<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\DealPayment;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    public function run(): void
    {
        $riyadhBranch = Branch::where('code', 'RIYADH')->first();
        $branchId = $riyadhBranch ? $riyadhBranch->id : null;

        $vehicle = Vehicle::first();
        $customer = Customer::first();
        
        // Riyadh salesperson
        $salesperson = User::where('email', 'agent_riyadh@gmail.com')->first() ?? User::first();

        if ($vehicle && $customer && $salesperson) {
            // Reserve the vehicle
            $vehicle->update(['status' => 'reserved']);

            $deal = Deal::create([
                'vehicle_id' => $vehicle->id,
                'customer_id' => $customer->id,
                'salesperson_id' => $salesperson->id,
                'deal_type' => 'installment',
                'agreed_price' => 440000.00,
                'discount' => 5000.00,
                'trade_in_make' => 'تويوتا',
                'trade_in_model' => 'كامري',
                'trade_in_year' => 2018,
                'trade_in_vin' => '1YV1Y1Y1Y1Y1Y1Y1Y',
                'trade_in_value' => 35000.00,
                'final_price' => 400000.00, // 440000 - 5000 - 35000
                'status' => 'approved',
                'branch_id' => $branchId,
            ]);

            DealPayment::create([
                'deal_id' => $deal->id,
                'amount' => 100000.00,
                'method' => 'bank_transfer',
                'paid_at' => now()->subDays(5),
                'reference' => 'TXN-99887766',
            ]);

            DealPayment::create([
                'deal_id' => $deal->id,
                'amount' => 15000.00,
                'method' => 'cash',
                'paid_at' => now(),
                'reference' => 'CASH-REC-102',
            ]);
        }
    }
}
