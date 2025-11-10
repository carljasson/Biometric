<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use App\Models\LoginHistory;

class RecordSuccessfulLogin
{
   public function handle(Login $event)
{
    $user = $event->user;

    // Only log normal users
    if ($user instanceof \App\Models\User) {
        LoginHistory::create([
            'user_id'     => $user->id,
            'method'      => 'password',
            'device'      => substr(Request::header('User-Agent') ?? 'unknown', 0, 255),
            'ip'          => Request::ip(),
            'location'    => null,
            'session_id'  => session()->getId(),
            'logged_in_at'=> now(),
        ]);
    }
}

}
