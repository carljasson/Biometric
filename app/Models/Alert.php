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
        'patient_id',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function user()
{
    return $this->belongsTo(User::class, 'patient_id');
}

}
