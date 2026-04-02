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
        Schema::create('filling_stations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->onDelete('cascade');
    
            $table->string('station_name');
            $table->string('station_code')->unique();
    
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();
    
            $table->string('division');
            $table->string('district')->nullable();
            $table->string('upazila')->nullable();
    
            $table->text('address')->nullable();
            $table->string('status')->default('active');
            $table->string('linked_depot')->nullable();
    
            $table->integer('tank_capacity')->nullable(); // e.g. 10000
    
            $table->json('fuel_types')->nullable(); 
            // ["Petrol","Diesel","Octane"]
            $table->string('type')->nullable();
    
            $table->string('license_file')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filling_stations');
    }
};
