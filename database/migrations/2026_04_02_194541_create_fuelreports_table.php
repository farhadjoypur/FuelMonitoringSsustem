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
        Schema::create('fuelreports', function (Blueprint $table) {
            $table->id();
 
            // Station Info
            $table->string('station_name');
            $table->string('thana_upazila');
            $table->string('district');
            $table->date('report_date');
 
            // Petrol
            $table->decimal('petrol_prev_stock', 10, 2)->default(0);
            $table->decimal('petrol_supply', 10, 2)->default(0);
            $table->decimal('petrol_received', 10, 2)->default(0);
            $table->decimal('petrol_difference', 10, 2)->default(0); // supply - received
            $table->decimal('petrol_sales', 10, 2)->default(0);
            $table->decimal('petrol_closing_stock', 10, 2)->default(0); // prev + received - sales
 
            // Diesel
            $table->decimal('diesel_prev_stock', 10, 2)->default(0);
            $table->decimal('diesel_supply', 10, 2)->default(0);
            $table->decimal('diesel_received', 10, 2)->default(0);
            $table->decimal('diesel_difference', 10, 2)->default(0);
            $table->decimal('diesel_sales', 10, 2)->default(0);
            $table->decimal('diesel_closing_stock', 10, 2)->default(0);
 
            // Octane
            $table->decimal('octane_prev_stock', 10, 2)->default(0);
            $table->decimal('octane_supply', 10, 2)->default(0);
            $table->decimal('octane_received', 10, 2)->default(0);
            $table->decimal('octane_difference', 10, 2)->default(0);
            $table->decimal('octane_sales', 10, 2)->default(0);
            $table->decimal('octane_closing_stock', 10, 2)->default(0);
 
            $table->timestamps();
 
            // একই তারিখে একই স্টেশনের duplicate entry রোধ করতে
            $table->unique(['station_name', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuelreports');
    }
};
