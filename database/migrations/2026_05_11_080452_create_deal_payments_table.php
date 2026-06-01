<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deal_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('method'); // e.g., Cash, Bank Transfer, Cheque, Card, Finance Payout
            $table->timestamp('paid_at')->nullable();
            $table->string('reference')->nullable(); // e.g., bank transaction id, cheque number
            $table->timestamps();

            // Indexes for accounting and auditing
            $table->index('paid_at');
            $table->index('method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_payments');
    }
};
