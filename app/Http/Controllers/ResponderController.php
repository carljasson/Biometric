<?php

namespace App\Http\Controllers;

use App\Models\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ResponderController extends Controller
{
public function showLoginForm()
{
    return view('responder.login');
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = $request->email;
    $attemptsKey = 'responder_login_attempts_' . $email;
    $lockoutKey = 'responder_login_lockout_' . $email;

    // Check if user is currently locked out
    if (Cache::has($lockoutKey)) {
        $secondsLeft = Cache::get($lockoutKey) - time();
        return back()
            ->with('error', "Too many failed attempts. Please wait {$secondsLeft} seconds.")
            ->with('lockout', $secondsLeft);
    }

    $responder = Responder::where('email', $email)->first();

    // If responder not found
    if (!$responder) {
        $this->incrementAttempts($attemptsKey, $lockoutKey);
        return back()->with('error', 'Responder not found.');
    }

    // If password is incorrect
    if (!Hash::check($request->password, $responder->password)) {
        $this->incrementAttempts($attemptsKey, $lockoutKey);
        return back()->with('error', 'Incorrect password.');
    }

// Successful login: clear attempts
Cache::forget($attemptsKey);

// Generate 6-digit PIN
$pin = rand(100000, 999999);
Session::put('responder_pin', $pin);
Session::put('responder_id', $responder->id);


// Set expiration time (5 minutes from now)
Session::put('responder_pin_expires_at', Carbon::now()->addMinutes(5));

// Send PIN via email
Mail::raw("Your login PIN is: $pin", function($message) use ($responder) {
    $message->to($responder->email)
            ->subject('Your Responder Login PIN');
});

// Redirect to PIN entry page
return redirect()->route('responder.login.pin');

}


// Helper function to handle login attempts
protected function incrementAttempts($attemptsKey, $lockoutKey)
{
    $attempts = Cache::get($attemptsKey, 0) + 1;
    Cache::put($attemptsKey, $attempts, 120); // store attempts for 2 mins

    if ($attempts >= 3) {
        $lockoutTime = 120; // 2 minutes in seconds
        Cache::put($lockoutKey, time() + $lockoutTime, $lockoutTime);
    }
}

public function resendPin()
{
    if (!Session::has('responder_id')) {
        return redirect()->route('responder.login')->with('error', 'Please login first.');
    }

    $responderId = Session::get('responder_id');
    $responder = Responder::find($responderId);

    if (!$responder) {
        return redirect()->route('responder.login')->with('error', 'Responder not found.');
    }

    // Generate new PIN
    $pin = rand(100000, 999999);
    Session::put('responder_pin', $pin);

    // Set expiration time
Session::put('responder_pin_expires_at', Carbon::now()->addMinutes(5));

    // Send PIN via email
    Mail::raw("Your login PIN is: $pin", function($message) use ($responder) {
        $message->to($responder->email)
                ->subject('Your Responder Login PIN');
    });

    return back()->with('success', 'A new PIN has been sent to your email.');
}

 // Verify submitted PIN
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:6']);

        if (!session()->has('responder_id') || !session()->has('responder_pin')) {
            return redirect()->route('responder.login')->with('error', 'Session expired. Please login again.');
        }

        $expiresAt = session('responder_pin_expires_at');
        if (!$expiresAt || now()->greaterThan(Carbon::parse($expiresAt))) {
            session()->forget(['responder_id', 'responder_pin', 'responder_pin_expires_at']);
            return redirect()->route('responder.login')->with('error', 'PIN expired. Please login again.');
        }

        if ($request->pin != session('responder_pin')) {
            return back()->with('error', 'Incorrect PIN.')->withInput();
        }

        // Log in responder
        $responderId = session('responder_id');
        Auth::guard('responder')->loginUsingId($responderId);

        session()->forget(['responder_id', 'responder_pin', 'responder_pin_expires_at']);

        return redirect()->intended('/responder/dashboard')->with('success', 'Welcome back!');
    }

public function showPinForm()
{
    // If no PIN session, redirect back to login
    if (!Session::has('responder_id')) {
        return redirect()->route('responder.login')->with('error', 'Please login first.');
    }

    return view('responder.login-pin');
}

 // Logout
    public function logout()
    {
        Auth::guard('responder')->logout();
        return redirect()->route('responder.login');
    }

    // Dashboard
 public function dashboard()
{
    $announcements = Announcement::where('active', true)->latest()->get(); // optional: only active ones
    return view('responder.dashboard', compact('announcements'));
}

    // Optional: Insert a responder (for testing)
   public function insertDemoResponder()
{
    try {
        Responder::create([
            'name' => 'Responder 1',
            'email' => 'responder1@example.com',
            'password' => Hash::make('securepassword'),
        ]);
        return 'Demo responder created';
    } catch (\Exception $e) {
        Log::error('Responder insert failed: ' . $e->getMessage());
        return 'Insert failed: ' . $e->getMessage();
    }
}
public function fingerprintScan(Request $request)
{
    return view('responder.scan-fingerprint');
}

public function identifyFingerprint(Request $request)
{
    $request->validate([
        'fingerprint_data' => 'required|string'
    ]);

    $token = $request->fingerprint_data;

    // Match user by similar fingerprint (prefix-based for simulated token)
    $user = User::whereNotNull('fingerprint_data')
            ->where('fingerprint_data', $token)
            ->first();
    if ($user) {
        return back()->with('matched_user', $user);
    } else {
        return back()->with('not_found', 'No matching fingerprint found.');
    }
}
public function faceScan()
{
    return view('responder.scan-face'); // or whatever your Blade view is named
}

public function identifyFace(Request $request)
{
    $submitted = json_decode($request->face_descriptor);

    $users = User::whereNotNull('face_descriptor')->get();

    foreach ($users as $user) {
        $stored = json_decode($user->face_descriptor);

        if ($stored && $this->compareDescriptors($submitted, $stored) < 0.5) {
            // Match found, show user info
            return back()->with('identified_user', $user);
        }
    }

    return back()->with('not_identified', 'No match found. Please try again.');
}

private function compareDescriptors(array $desc1, array $desc2): float
{
    $sum = 0;
    for ($i = 0; $i < count($desc1); $i++) {
        $sum += pow($desc1[$i] - $desc2[$i], 2);
    }
    return sqrt($sum); // Euclidean distance
}

public function showScanPage()
{
    return view('responder.scan'); // Laravel looks for: resources/views/responder/scan.blade.php
}
public function profile()
{
    $responder = Auth::guard('responder')->user();
    return view('responder.profile', compact('responder'));
}

public function checkAlerts()
{
    // Get new/unseen emergency alerts for responders
    $alerts = EmergencyAlert::where('status', '!=', 'Resolved')
        ->where('notified_responder', false) // optional flag
        ->with('user')
        ->get();

    // Mark as notified to prevent repeated alerts
    foreach($alerts as $alert) {
        $alert->notified_responder = true;
        $alert->save();
    }

    // Return JSON
    return response()->json($alerts->map(function($alert){
        return [
            'id' => $alert->id,
            'sender_name' => $alert->user->firstname . ' ' . $alert->user->lastname,
            'sender_phone' => $alert->user->phone,
            'latitude' => $alert->latitude,
            'longitude' => $alert->longitude,
        ];
    }));
}
// Display all responders
    public function index()
    {
        $responders = Responder::all();
        return view('admin.addresponder', compact('responders'));
    }

    // Store new responder
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:responders,email',
            'location' => 'required|string|max:255',
            'password' => 'required|min:6'
        ]);

        Responder::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'location' => $request->location,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Responder added successfully!');
    }

    // Update responder
    public function update(Request $request, $id)
    {
        $responder = Responder::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:responders,email,{$id}",
            'location' => 'required|string|max:255',
            'password' => 'nullable|min:6'
        ]);

        $responder->name     = $request->name;
        $responder->email    = $request->email;
        $responder->location = $request->location;

        // Only update password if provided
        if ($request->password) {
            $responder->password = Hash::make($request->password);
        }

        $responder->save();

        return redirect()->back()->with('success', 'Responder updated successfully!');
    }

    // Delete responder
    public function destroy($id)
    {
        $responder = Responder::findOrFail($id);
        $responder->delete();

        return redirect()->back()->with('success', 'Responder deleted successfully!');
    }

    public function viewAlerts()
{
    $alerts = EmergencyAlert::latest()->get();
    return view('responder.alerts', compact('alerts'));
}

}

