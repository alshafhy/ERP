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
        Schema::create('deal_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('due_at');
            $table->string('status')->default('upcoming'); // upcoming, paid, overdue
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('deal_payment_id')->nullable()->constrained('deal_payments')->onDelete('set null');
            $table->timestamps();

            $table->index('deal_id');
            $table->index('status');
            $table->index('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_installments');
    }
};
