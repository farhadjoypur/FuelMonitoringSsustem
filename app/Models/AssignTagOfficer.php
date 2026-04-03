<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignTagOfficer extends Model
{
    protected $fillable = [
        'officer_id',
        'filling_station_id',
        'assign_date',
        'status',
        'remarks',
    ];

    public function dc()
    {
        return $this->belongsTo(User::class, 'dc_id');
    }
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function fillingStation()
    {
        return $this->belongsTo(FillingStation::class, 'filling_station_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
