<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('users_id')) {
            return redirect('/')->with('error', 'You must be logged in.');
        }

        return $next($request);
    }
}
