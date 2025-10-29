<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id', 'method', 'device', 'ip', 'location', 'session_id', 'logged_in_at'
    ];

    protected $dates = ['logged_in_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
