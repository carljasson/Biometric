<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    // If your table has created_at, updated_at columns
    public $timestamps = true; // set to true since your table has them

    protected $casts = [
        'logged_in_at' => 'datetime',
        'location' => 'array', // cast JSON to array
    ];

    // ✅ Allow mass assignment for these columns
    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'method',
        'device',
        'ip',
        'location',
        'session_id',
        'logged_in_at',
    ];

    // Polymorphic relationship to User/Admin/Responder
    public function loggable()
    {
        return $this->morphTo();
    }
}
