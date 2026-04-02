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
        Schema::create('depots', function (Blueprint $table) {
            $table->id();

            $table->string('depot_name');
            $table->string('depot_code')->unique();
            $table->string('district');
            $table->text('full_address')->nullable();
            $table->string('contact_number');
            $table->string('email')->nullable();
        
            $table->decimal('capacity', 10, 2); // liters
            $table->integer('number_of_tanks')->nullable();
        
            $table->enum('status', ['active', 'inactive'])->default('active');
        
            $table->text('remarks')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
