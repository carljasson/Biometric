<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Responder extends Authenticatable
{
    protected $table = 'responders'; // This must match your DB table
    protected $fillable = ['name', 'email', 'password'];
}

