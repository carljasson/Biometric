<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;

class LoginHistoryController extends Controller
{
    // protect with middleware('auth') and authorization as needed
    public function index()
    {
        // paginate or limit as needed
        $entries = LoginHistory::with('user')->orderBy('logged_in_at', 'desc')->paginate(50);
return view('admin.login-history', compact('entries'));

    }
}
