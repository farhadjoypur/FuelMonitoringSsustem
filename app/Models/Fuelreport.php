<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuelreport extends Model
{
    protected $fillable = [
        'station_name',
        'thana_upazila',
        'district',
        'report_date',
 
        'petrol_prev_stock',
        'petrol_supply',
        'petrol_received',
        'petrol_difference',
        'petrol_sales',
        'petrol_closing_stock',
 
        'diesel_prev_stock',
        'diesel_supply',
        'diesel_received',
        'diesel_difference',
        'diesel_sales',
        'diesel_closing_stock',
 
        'octane_prev_stock',
        'octane_supply',
        'octane_received',
        'octane_difference',
        'octane_sales',
        'octane_closing_stock',
    ];
 
    protected $casts = [
        'report_date' => 'date',
    ];
 
    /**
     * গতকালের closing_stock থেকে আজকের previous_stock বের করা
     * এই method controller এ call হবে
     */
    public static function getPreviousStocks(string $stationName): array
    {
        $lastReport = self::where('station_name', $stationName)
            ->orderBy('report_date', 'desc')
            ->first();
 
        if (!$lastReport) {
            return [
                'petrol' => 0,
                'diesel' => 0,
                'octane' => 0,
            ];
        }
 
        return [
            'petrol' => $lastReport->petrol_closing_stock,
            'diesel' => $lastReport->diesel_closing_stock,
            'octane' => $lastReport->octane_closing_stock,
        ];
    }
}
