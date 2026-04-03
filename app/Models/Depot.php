<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    protected $fillable = [
        'depot_name',
        'depot_code',
        'district',
        'full_address',
        'contact_number',
        'email',
        'capacity',
        'number_of_tanks',
        'status',
        'remarks'
    ];
    public function fillingStations()
{
    return $this->hasMany(FillingStation::class, 'linked_depot');
}
}
