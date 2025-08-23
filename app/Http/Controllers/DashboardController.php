<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // You can validate that the user is a patient if you have a role field
        if ($user->role !== 'patient') {
            return redirect('/login')->withErrors(['msg' => 'Unauthorized access.']);
        }

        $announcements = Announcement::latest()->get();

        // Pass user as 'patient' for compatibility with the Blade file
        return view('dashboard', [
            'patient' => $user,
            'announcements' => $announcements
        ]);
    }
}
