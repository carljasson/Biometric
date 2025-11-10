<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    // If your table has created_at, updated_at, Laravel will auto-manage them
    public $timestamps = false; // if you don't have updated_at

    // Cast the date column to Carbon automatically
    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
