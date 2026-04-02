<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FillingStation extends Model
{
    protected $fillable = [
        'company_id',
        'station_name',
        'station_code',
        'owner_name',
        'owner_phone',
        'division',
        'district',
        'upazila',
        'address',
        'linked_depot',
        'tank_capacity',
        'fuel_types',
        'license_file',
        'status',
        'type',
    ];

    protected $casts = [
        'fuel_types' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
