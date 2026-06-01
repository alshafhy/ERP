<?php

namespace App\Jobs;

use App\Models\DealInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOverdueInstallments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('App Job: Starting CheckOverdueInstallments scan...');

        $overdueCount = 0;

        // Fetch installments where due date is past today and is not marked paid
        $installments = DealInstallment::where('due_at', '<', now()->toDateString())
            ->where('status', '!=', 'paid')
            ->with(['deal.customer'])
            ->get();

        foreach ($installments as $installment) {
            if ($installment->status !== 'overdue') {
                $installment->update(['status' => 'overdue']);
                $overdueCount++;

                $customerName = $installment->deal->customer->name;
                $phone = $installment->deal->customer->phone;
                $amount = number_format($installment->amount, 2);

                // Queue/simulate notification SMS
                Log::warning("CRM PAYMENT ALERT: Installment #{$installment->installment_number} for customer {$customerName} ({$phone}) of amount {$amount} SAR is OVERDUE since {$installment->due_at->format('Y-m-d')}. Alert sent.");
            }
        }

        Log::info("App Job: Scan complete. Identified and updated {$overdueCount} overdue installments.");
    }
}
