<?php

namespace Database\Seeders;

use App\Models\JournalEntry;
use Illuminate\Database\Seeder;

class JournalEntrySeeder extends Seeder
{
    public function run(): void
    {
        $accounts = ['Inventory', 'Accounts Payable', 'Cash', 'Purchases'];

        for ($i = 1; $i <= 30; $i++) {
            $debitAccount = $accounts[rand(0, 3)];
            $creditAccount = $accounts[rand(0, 3)];

            // Ensure debit and credit accounts are different
            while ($debitAccount === $creditAccount) {
                $creditAccount = $accounts[rand(0, 3)];
            }

            JournalEntry::create([
                'entry_number' => 'JE-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'reference_type' => null,
                'reference_id' => null,
                'description' => 'Generic transaction ' . $i,
                'debit_account' => $debitAccount,
                'credit_account' => $creditAccount,
                'amount' => rand(500, 5000) + (rand(0, 99) / 100),
            ]);
        }
    }
}
