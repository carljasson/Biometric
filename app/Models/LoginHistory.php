<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    // If your table has created_at, updated_at columns
    public $timestamps = false; // set to true if you have them

    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    // ✅ Allow mass assignment for these columns
    protected $fillable = [
        'user_id',
        'method',
        'device',
        'ip',
        'location',
        'session_id',
        'logged_in_at',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
