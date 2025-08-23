<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_id')) {
            if ($request->is('admin/login') || $request->is('admin/register')) {
                return $next($request);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
