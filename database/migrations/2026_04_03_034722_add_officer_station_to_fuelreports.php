<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuelreports', function (Blueprint $table) {
 
            // tag_officer_id — যে Officer report করেছে
            $table->unsignedBigInteger('tag_officer_id')
                  ->nullable()
                  ->after('id');
 
            // station_id — কোন Filling Station এর report
            $table->unsignedBigInteger('station_id')
                  ->nullable()
                  ->after('tag_officer_id');
 
            // Foreign Keys
            $table->foreign('tag_officer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
 
            $table->foreign('station_id')
                  ->references('id')
                  ->on('filling_stations')
                  ->onDelete('set null');
 
            // Index — দুটো দিয়ে একসাথে query fast করার জন্য
            $table->index(['tag_officer_id', 'station_id']);
        });
    }
 
    public function down(): void
    {
        Schema::table('fuelreports', function (Blueprint $table) {
            $table->dropForeign(['tag_officer_id']);
            $table->dropForeign(['station_id']);
            $table->dropIndex(['tag_officer_id', 'station_id']);
            $table->dropColumn(['tag_officer_id', 'station_id']);
        });
    }
};
