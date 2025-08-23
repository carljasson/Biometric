<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminAuth;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register the middleware alias
        Route::aliasMiddleware('admin.auth', AdminAuth::class);

        // Share $admin globally in all views
        View::composer('*', function ($view) {
            $adminId = session('admin_id');

            if ($adminId) {
                $admin = Admin::find($adminId);
                $view->with('admin', $admin);
            } else {
                $view->with('admin', null);
            }
        });
    }
}
