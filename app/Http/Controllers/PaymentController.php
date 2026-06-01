<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealPayment;
use App\Models\DealInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Store a payment against a Deal.
     */
    public function storePayment(Request $request, Deal $deal)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bank_transfer,cheque,card',
            'reference' => 'nullable|string|max:100',
            'paid_at' => 'required|date',
        ]);

        $validated['deal_id'] = $deal->id;

        DealPayment::create($validated);

        return redirect()->route('deals.show', $deal)
            ->with('success', 'تم تسجيل سند القبض المالي بنجاح.');
    }

    /**
     * Build an installment plan for a Deal.
     * Generates down payment + N installments.
     */
    public function buildInstallmentPlan(Request $request, Deal $deal)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403);
        }

        $validated = $request->validate([
            'down_payment' => 'required|numeric|min:0',
            'number_of_installments' => 'required|integer|min:1|max:120',
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $downPayment = (float)$validated['down_payment'];
        $numInstallments = (int)$validated['number_of_installments'];
        $startDate = Carbon::parse($validated['start_date']);

        $remainingAmount = $deal->final_price - $downPayment;

        if ($remainingAmount < 0) {
            return back()->with('error', 'مبلغ الدفعة الأولى يتجاوز صافي قيمة الصفقة.');
        }

        DB::transaction(function () use ($deal, $downPayment, $numInstallments, $startDate, $remainingAmount) {
            // Delete old installments if any to allow rebuilding
            $deal->installments()->delete();

            // 1. Record down payment if > 0
            if ($downPayment > 0) {
                $payment = DealPayment::create([
                    'deal_id' => $deal->id,
                    'amount' => $downPayment,
                    'method' => 'cash',
                    'paid_at' => now(),
                    'reference' => 'دفعة أولى مقدمة لحساب العقد #' . $deal->id,
                ]);

                // Create downpayment installment immediately marked as paid
                DealInstallment::create([
                    'deal_id' => $deal->id,
                    'installment_number' => 0,
                    'amount' => $downPayment,
                    'due_at' => now()->toDateString(),
                    'status' => 'paid',
                    'paid_at' => now(),
                    'deal_payment_id' => $payment->id,
                ]);
            }

            // 2. Divide remaining amount among installments
            $installmentAmount = round($remainingAmount / $numInstallments, 2);
            $accumulated = 0;

            for ($i = 1; $i <= $numInstallments; $i++) {
                // Adjust last installment for rounding differences
                if ($i === $numInstallments) {
                    $installmentAmount = $remainingAmount - $accumulated;
                } else {
                    $accumulated += $installmentAmount;
                }

                DealInstallment::create([
                    'deal_id' => $deal->id,
                    'installment_number' => $i,
                    'amount' => $installmentAmount,
                    'due_at' => $startDate->copy()->addMonths($i - 1)->toDateString(),
                    'status' => 'upcoming',
                ]);
            }
        });

        return redirect()->route('deals.show', $deal)
            ->with('success', 'تم إنشاء خطة الأقساط وجدول السداد بنجاح.');
    }

    /**
     * Collect payment for a specific scheduled installment.
     */
    public function payInstallment(Request $request, DealInstallment $installment)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'super-admin', 'branch_manager', 'finance_officer'])) {
            abort(403);
        }

        $validated = $request->validate([
            'method' => 'required|in:cash,bank_transfer,cheque,card',
            'reference' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($installment, $validated) {
            // Record payment transaction
            $payment = DealPayment::create([
                'deal_id' => $installment->deal_id,
                'amount' => $installment->amount,
                'method' => $validated['method'],
                'paid_at' => now(),
                'reference' => $validated['reference'] ?? ('سداد القسط رقم #' . $installment->installment_number),
            ]);

            // Update installment
            $installment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'deal_payment_id' => $payment->id,
            ]);
        });

        return redirect()->back()
            ->with('success', 'تم استلام قيمة القسط رقم #' . $installment->installment_number . ' بنجاح وتحديث الجدول.');
    }
}
