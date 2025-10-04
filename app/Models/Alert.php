<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'latitude',
        'longitude',
        'address',
        'destination',
        'photo',
        'user_id',
        'status',
    ];


    public function user()
{
    return $this->belongsTo(User::class, 'patient_id');
}

}
