<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PatientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'patient') {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}
