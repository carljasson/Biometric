<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmergencyAlert extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'latitude',
        'longitude',
        'status',
         'patient_id',
         'address',
        'destination',
        'photo',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
