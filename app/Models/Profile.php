<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'department',
        'division',
        'district',
        'upazila',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
