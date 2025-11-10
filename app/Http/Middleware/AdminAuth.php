<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            // Allow login and register pages without redirect
            if ($request->is('admin/login') || $request->is('admin/register')) {
                return $next($request);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
