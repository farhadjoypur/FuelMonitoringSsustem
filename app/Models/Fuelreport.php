<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuelreport extends Model
{
    protected $fillable = [
        'tag_officer_id',        // TagOfficer → users.id
        'station_id',            // Filling Station → filling_stations.id
        'station_name',          // display purpose
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

    // ═══════════════════════════════════════════
    //  RELATIONSHIPS
    // ═══════════════════════════════════════════

    /** যে TagOfficer report submit করেছে */
    public function tagOfficer()
    {
        return $this->belongsTo(User::class, 'tag_officer_id');
    }

    /** যে Filling Station এর জন্য report */
    public function fillingStation()
    {
        return $this->belongsTo(FillingStation::class, 'station_id');
    }

    // ═══════════════════════════════════════════
    //  SCOPES
    // ═══════════════════════════════════════════

    /**
     * TagOfficer + Station দুটো দিয়ে filter
     * ব্যবহার: Fuelreport::byOfficerAndStation($officerId, $stationId)->...
     */
    public function scopeByOfficerAndStation($query, int $tagOfficerId, int $stationId)
    {
        return $query
            ->where('tag_officer_id', $tagOfficerId)
            ->where('station_id', $stationId);
    }

    // ═══════════════════════════════════════════
    //  STATIC HELPERS
    // ═══════════════════════════════════════════

    /**
     * সর্বশেষ report এর closing stock বের করা
     * পরের দিনের prev_stock হিসেবে ব্যবহার হবে
     */
    public static function getPreviousStocks(int $tagOfficerId, int $stationId): array
    {
        $lastReport = self::where('tag_officer_id', $tagOfficerId)
            ->where('station_id', $stationId)
            ->orderBy('report_date', 'desc')
            ->first();

        if (! $lastReport) {
            return ['petrol' => 0, 'diesel' => 0, 'octane' => 0];
        }

        return [
            'petrol' => $lastReport->petrol_closing_stock,
            'diesel' => $lastReport->diesel_closing_stock,
            'octane' => $lastReport->octane_closing_stock,
        ];
    }
}