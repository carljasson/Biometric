<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CustomLoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate 6-digit PIN
            $pin = rand(100000, 999999);

            // Store PIN temporarily (you can store in DB too)
            Session::put('login_pin', $pin);
            Session::put('pending_user_id', $user->id);

            // Send email (simple version)
            Mail::raw("Your Biometric Medical Access login PIN is: {$pin}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Your Login PIN - Biometric Medical Access');
            });

            Auth::logout(); // Logout until verified
            return redirect()->route('login.pin')->with('success', 'A PIN has been sent to your email.');
        }

        return back()->withErrors(['email' => 'Invalid email or password']);
    }

    public function showPinForm()
    {
        if (!session()->has('login_pin')) {
            return redirect('/login')->with('error', 'Session expired. Please login again.');
        }

        return view('auth.pin-login');
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
        ]);

        $storedPin = session('login_pin');
        $userId = session('pending_user_id');

        if ($request->pin == $storedPin && $userId) {
            Auth::loginUsingId($userId);

            // Clear PIN session
            session()->forget(['login_pin', 'pending_user_id']);

            return redirect()->intended('/dashboard')->with('success', 'Login successful!');
        }

        return back()->with('error', 'Invalid PIN. Please try again.');
    }

    public function resendPin()
    {
        $userId = session('pending_user_id');
        if (!$userId) {
            return redirect('/login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);
        $pin = rand(100000, 999999);

        Session::put('login_pin', $pin);

        Mail::raw("Your new login PIN is: {$pin}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your New Login PIN - Biometric Medical Access');
        });

        return back()->with('success', 'A new PIN has been sent to your email.');
    }
}
