<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Responder extends Authenticatable
{
    use Notifiable;

    protected $table = 'responders'; // 👈 Ensure this matches your DB

    protected $fillable = [
        'name',
        'email',
        'password',
        'location',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
