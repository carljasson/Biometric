<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';
    public $timestamps = false;

    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    // Allow mass assignment
    protected $fillable = [
        'user_id',
        'method',
        'device',
        'ip',
        'location',
        'session_id',
        'logged_in_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

