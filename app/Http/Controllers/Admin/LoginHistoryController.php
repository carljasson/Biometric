<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;

class AdminController extends Controller
{
    public function loginHistory()
    {
        $admins = LoginHistory::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'admin'))
            ->orderBy('logged_in_at', 'desc')
            ->paginate(50, ['*'], 'admins');

        $responders = LoginHistory::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'responder'))
            ->orderBy('logged_in_at', 'desc')
            ->paginate(50, ['*'], 'responders');

        $users = LoginHistory::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->orderBy('logged_in_at', 'desc')
            ->paginate(50, ['*'], 'users');

        return view('admin.login-history', compact('admins', 'responders', 'users'));
    }
}

