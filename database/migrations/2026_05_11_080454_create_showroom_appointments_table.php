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
        Schema::create('showroom_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade'); // Can be null if general showroom viewing
            $table->enum('type', ['viewing', 'test_drive'])->default('viewing');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('scheduled'); // e.g., Scheduled, Completed, Cancelled, No Show
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for appointment scheduling board
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showroom_appointments');
    }
};
