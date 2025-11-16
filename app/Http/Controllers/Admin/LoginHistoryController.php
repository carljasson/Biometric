<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;

class LoginHistoryController extends Controller
{
    public function index()
    {
        // Separate paginated queries per role
        $admins = LoginHistory::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'admin'))
            ->orderBy('logged_in_at', 'desc')
            ->paginate(50, ['*'], 'admins'); // third param = query string for page

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
