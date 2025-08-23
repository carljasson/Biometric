<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthResponder
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('responder')->check()) {
            return redirect()->route('responder.login');
        }

        return $next($request);
    }
}
