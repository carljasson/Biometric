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

        // store a record (truncate user-agent to avoid overly long strings)
        LoginHistory::create([
            'user_id'     => $user ? $user->id : null,
'method' => 'password', // since you log in by email/password
            'device'      => substr(Request::header('User-Agent') ?? 'unknown', 0, 255),
            'ip'          => Request::ip(),
            'location'    => null,
            'session_id'  => session()->getId(),
            'logged_in_at'=> now(),
        ]);
    }
}
