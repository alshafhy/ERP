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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            // A vehicle belongs to a deal (no unique index if we want history of aborted/cancelled deals, but standard foreign key is fine)
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('salesperson_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('deal_type', ['cash', 'installment', 'financing', 'trade_in'])->default('cash');
            $table->decimal('agreed_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            
            // Trade-in vehicle support
            $table->string('trade_in_make')->nullable();
            $table->string('trade_in_model')->nullable();
            $table->integer('trade_in_year')->nullable();
            $table->string('trade_in_vin')->nullable();
            $table->decimal('trade_in_value', 15, 2)->default(0);

            // Final calculated price: agreed_price - discount - trade_in_value
            $table->decimal('final_price', 15, 2)->default(0);

            // Deal Status: draft, pending_approval, approved, contract_signed, delivered, closed
            $table->string('status')->default('draft');
            
            $table->timestamps();

            // Indexes for sales analysis and operations
            $table->index('salesperson_id');
            $table->index('status');
            $table->index('deal_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
