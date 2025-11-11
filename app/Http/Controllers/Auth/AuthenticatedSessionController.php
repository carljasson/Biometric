<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Attempt to authenticate
        $request->authenticate();

        $user = Auth::user();

        // ✅ Generate and send PIN
        $pin = rand(100000, 999999);

        // Store PIN in session temporarily
        Session::put('login_pin', $pin);
        Session::put('pending_user_id', $user->id);

        // Send PIN via email
        Mail::raw("Your Biometric Medical Access login PIN is: {$pin}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your Login PIN - Biometric Medical Access');
        });

        // Log out user until PIN verified
        Auth::logout();

        // ✅ Redirect to PIN verification page (NOT dashboard)
        return redirect()->route('pin.login')
                         ->with('success', 'A PIN has been sent to your email.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
