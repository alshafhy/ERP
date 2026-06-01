<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $riyadhBranch = Branch::where('code', 'RIYADH')->first();
        $branchId = $riyadhBranch ? $riyadhBranch->id : null;

        $customer = Customer::first();
        $vehicle = Vehicle::first();
        
        // Find Riyadh Agent
        $agent = User::where('email', 'agent_riyadh@gmail.com')->first() ?? User::first();

        if ($customer && $vehicle) {
            Lead::create([
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'source' => 'website',
                'assigned_to' => $agent ? $agent->id : null,
                'status' => 'new',
                'notes' => 'العميل مهتم جداً بـ فورد رابيتور الجديدة ولديه استفسار بخصوص الألوان المتاحة.',
                'follow_up_at' => now()->addDays(2),
                'branch_id' => $branchId,
            ]);

            $customer2 = Customer::skip(1)->first();
            $vehicle2 = Vehicle::skip(1)->first();
            if ($customer2 && $vehicle2) {
                Lead::create([
                    'customer_id' => $customer2->id,
                    'vehicle_id' => $vehicle2->id,
                    'source' => 'call',
                    'assigned_to' => $agent ? $agent->id : null,
                    'status' => 'contacted',
                    'notes' => 'تم الاتصال بالشركة لمناقشة تسعيرة أسطول سيارات لاندكروزر LC300.',
                    'follow_up_at' => now()->subDays(1), // Overdue for testing follow-up reminder
                    'branch_id' => $branchId,
                ]);
            }
        }
    }
}
