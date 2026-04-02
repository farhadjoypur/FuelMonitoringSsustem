<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'registration_number',
        'tax_vat_number',
        'contact_person',
        'phone',
        'email',
        'address',
        'established_date',
        'status',
        'description',
    ];
}
