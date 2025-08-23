<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    
    protected $fillable = [
        'name',
        'age',
        'gender',
        'condition',
        'admit_date',
        'room_number',
    ];
}
