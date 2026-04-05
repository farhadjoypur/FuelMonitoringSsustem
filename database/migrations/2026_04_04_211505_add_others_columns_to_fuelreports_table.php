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
        Schema::table('fuelreports', function (Blueprint $table) {
            // ── Comment ─────────────────────────────────────────
            $table->text('comment')->nullable()->after('report_date');

            // ── Others ──────────────────────────────────────────
            $table->decimal('others_prev_stock',    10, 2)->default(0)->after('octane_closing_stock');
            $table->decimal('others_supply',        10, 2)->default(0)->after('others_prev_stock');
            $table->decimal('others_received',      10, 2)->default(0)->after('others_supply');
            $table->decimal('others_difference',    10, 2)->default(0)->after('others_received');
            $table->decimal('others_sales',         10, 2)->default(0)->after('others_difference');
            $table->decimal('others_closing_stock', 10, 2)->default(0)->after('others_sales');


            $table->string('octane_status')->nullable()->after('octane_closing_stock');
            $table->string('petrol_status')->nullable()->after('petrol_closing_stock');
            $table->string('diesel_status')->nullable()->after('diesel_closing_stock');
            $table->string('others_status')->nullable()->after('others_closing_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuelreports', function (Blueprint $table) {
            $table->dropColumn([
                'comment',
                'others_prev_stock',
                'others_supply',
                'others_received',
                'others_difference',
                'others_sales',
                'others_closing_stock',
            ]);
        });
    }
};
