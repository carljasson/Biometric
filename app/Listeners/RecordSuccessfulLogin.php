<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use App\Models\LoginHistory;

class RecordSuccessfulLogin
{
  public function handle($event)
{
    $user = $event->user;

    // Only log normal users
    if ($user instanceof \App\Models\User) {
        LoginHistory::create([
            'loggable_id' => $user->id,             // ✅ polymorphic
            'loggable_type' => get_class($user),   // App\Models\User
            'method'      => 'password',
            'device'      => substr(request()->header('User-Agent') ?? 'unknown', 0, 255),
            'ip'          => request()->ip(),
            'location'    => null,
            'session_id'  => session()->getId(),
            'logged_in_at'=> now(),
        ]);
    }
}

}
