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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin')->unique();
            $table->string('make');
            $table->string('model');
            $table->year('year');
            $table->string('trim')->nullable();
            $table->string('color');
            $table->integer('mileage')->default(0);
            $table->string('fuel_type')->nullable(); // e.g. Petrol, Diesel, Electric, Hybrid
            $table->string('transmission')->nullable(); // e.g. Automatic, Manual
            $table->enum('condition', ['new', 'used'])->default('new');
            $table->enum('status', ['available', 'reserved', 'sold', 'in_transit', 'damaged'])->default('available');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('listing_price', 15, 2)->default(0);
            $table->timestamps();

            // Indexes for heavy search and filtering
            $table->index(['make', 'model']);
            $table->index('status');
            $table->index('condition');
            $table->index('listing_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
