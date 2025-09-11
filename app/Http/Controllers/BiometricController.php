<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;


class BiometricController extends Controller
{
    // Show login form
// Show login form
public function showLoginForm()
{
    return view('login'); // resources/views/login.blade.php
}

// Handle login POST


// In BiometricController

// Handle login POST
public function login(Request $request)
{
   if ($response = $this->ensureIsNotRateLimited($request)) {
    return $response; // return redirect if locked
}


    $credentials = $request->only('email', 'password');

    if (auth()->attempt($credentials)) {
        RateLimiter::clear($this->throttleKey($request)); // reset counter
        return redirect()->intended('/dashboard');
    }

    // failed login → increase attempts
    RateLimiter::hit($this->throttleKey($request), 60); // block for 60s

    return back()->withErrors([
        'email' => 'Invalid credentials. Please try again.',
    ]);
}

protected function ensureIsNotRateLimited(Request $request)
{
    if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
        return;
    }

    $seconds = RateLimiter::availableIn($this->throttleKey($request));

    return redirect()->back()->with([
        'lockout' => $seconds,
        'error'   => "⛔ Too many login attempts. Please try again in {$seconds} seconds."
    ]);
}


protected function throttleKey(Request $request)
{
    return strtolower($request->input('email')).'|'.$request->ip();
}

    // Handle logout
    public function logout()
{
    Auth::logout(); // properly log out the user
    session()->flush(); // clear all session data

    return redirect('/')->with('success', 'Logged out successfully.');
}


    // Show dashboard
    public function dashboard()
    {
        $patient = Auth::user(); // this uses the "users" table by default
        $announcements = Announcement::latest()->get(); // fetch latest

        if (!$patient) {
            return redirect('/login')->with('error', 'Please login first.');
        }

        return view('dashboard', compact('patient', 'announcements'));
    }

    // Step 1: Personal Info
// Step 1: Personal Info
public function Step1(Request $request)
{
    if ($request->isMethod('post')) {
        $validated = $request->validate([
            'firstname'       => 'required|string|max:255',
            'middlename'      => 'nullable|string|max:255',

            'lastname'        => 'required|string|max:255',
            'birthday'        => 'required|date',
            'age'             => 'required|integer|min:1',
            'gender'          => 'required',
            'status'          => 'required',
            'phone'           => 'required|digits:11|unique:users,phone',
            'address'         => 'required|string|max:255',
            'contact_name'    => 'required|string|max:255',
            'contact_number'  => 'required|digits:11',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:8|max:16|confirmed',
        ]);

        $user = User::create([
            'firstname'       => $validated['firstname'],
            'middlename'     => $validated['middlename'] ?? null,

            'lastname'        => $validated['lastname'],
            'birthday'        => $validated['birthday'],
            'age'             => $validated['age'],
            'gender'          => $validated['gender'],
            'status'          => $validated['status'],
            'phone'           => $validated['phone'],
            'address'         => $validated['address'],
            'contact_name'    => $validated['contact_name'],
            'contact_number'  => $validated['contact_number'],
            'email'           => $validated['email'],
            'password'        => bcrypt($validated['password']),
        ]);

        session(['user_id' => $user->id]);

        return redirect()->route('register.step2')->with('success', 'Step 1 completed successfully!');
    }

    return view('register.step1');
}

// Step 2: Fingerprint
public function step2(Request $request)
{
    $user = User::find(session('user_id'));

    if (!$user) {
        return redirect('/register/step1')->with('error', 'Please complete Step 1 first.');
    }

    if ($request->isMethod('post')) {
        $request->validate([
            'fingerprint_data' => 'required|string|min:50' // enforce presence and minimal length
        ]);

        // Reject simulated or suspiciously short data
        if (str_starts_with($request->fingerprint_data, 'simulated-') || strlen($request->fingerprint_data) < 100) {
            return back()->with('error', 'Fingerprint scan failed or is invalid. Please try again.');
        }

        $user->fingerprint_data = $request->fingerprint_data;
        $user->save();

        return redirect('/register/step3');
    }

    return view('register.step2');
}



public function step3()
{
    return view('register.step3'); // Blade file: resources/views/register/step3.blade.php
}

public function registerStep3(Request $request)
{
    $user = User::find(session('user_id')) ?? auth()->user();

    if (!$user) {
        return redirect('/register/step1')->with('error', 'User session not found.');
    }

    // Check if the user clicked "Skip"
    if ($request->input('action') === 'skip') {
        return redirect('/')->with('info', 'You skipped face scan. Welcome!');
    }

    // User clicked "Complete Face Scan"
    if ($request->input('action') === 'scan') {
        $descriptor = json_decode($request->face_descriptor, true);

        if (is_array($descriptor) && count($descriptor) === 128) {
            $user->face_descriptor = json_encode($descriptor);
            $user->save();

            return redirect('/')->with('success', 'Face scan saved! Welcome to the system.');
        } else {
            return redirect()->back()->with('error', 'Face scan failed. Please align your face properly.');
        }
    }

    // Fallback
    return redirect('/register/step3')->with('error', 'Invalid action.');
}


public function scanFingerprint(Request $request)
{
    $fingerprintData = $request->input('fingerprint_data');

    if (!$fingerprintData || strlen($fingerprintData) < 100) {
        return back()->with('not_found', 'Invalid or missing fingerprint data.');
    }

    $user = User::where('fingerprint_data', $fingerprintData)->first();

    if ($user) {
        return redirect()->back()->with('matched_user', $user);
    }

    return redirect()->back()->with('not_found', 'No match found for this fingerprint.');
}


public function scanPage()
{
    return view('scan'); // or 'scan.blade.php' if in `resources/views`
}

    public function edit()
    {
        $patient = auth()->user();
        return view('patient.edit', compact('patient'));
    }

    public function update(Request $request)
    {
        $patient = auth()->user();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthday' => 'required|date',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'contact_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $patient->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthday' => $request->birthday,
            'age' => $request->age,
            'gender' => $request->gender,
            'status' => $request->status,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
        ]);

        return redirect('/dashboard')->with([
            'success' => 'Edit successful!',
            'showProfileModal' => true
        ]);
    }

   public function storeFingerprint(Request $request)
{
    // Use session user ID instead of authenticated user during registration
    $user = User::find(session('user_id'));

    if (!$user) {
        return redirect('/register/step1')->with('error', 'Please complete Step 1 first.');
    }

    $user->fingerprint_data = $request->input('fingerprint_data');
    $user->save();

    return redirect('/register/step3')->with('success', 'Fingerprint saved!');
}
public function submitScan(Request $request)
{
    $fingerprint = $request->input('fingerprint');
    $face = $request->input('face');

    if (!$fingerprint && !$face) {
        return back()->with('error', 'No biometric data received.');
    }

    $user = null;

    // Try matching fingerprint first
    if ($fingerprint && strlen($fingerprint) > 50) {
        $user = \App\Models\User::where('fingerprint_data', $fingerprint)->first();
    }

    // If no match, try face recognition
    if (!$user && $face && strlen($face) > 50) {
        $user = \App\Models\User::where('face_descriptor', $face)->first();
    }

    if ($user) {
        return redirect()->back()->with('matched_user', $user);
    } else {
        return back()->with('not_found', 'No match found with the provided biometric data.');
    }
}


    public function store(Request $request)
    {
        $request->validate([
            'fingerprint_data' => 'required|string',
        ]);

        // Example: store fingerprint data in biometric_system table
        $record = BiometricSystem::create([
            'name' => 'Test User', // replace with actual user info
            'fingerprint_registered' => $request->fingerprint_data,
        ]);

        return redirect('/register/step3')->with('success', 'Fingerprint saved!');
    }

 public function capture(Request $request)
    {
        $user = Auth::user();

        // This will come from scanner service (see below)
        $fingerprintData = $request->input('fingerprint_data');

        if (!$fingerprintData) {
            return response()->json(['success' => false, 'message' => 'No fingerprint data received.']);
        }

        // Save into DB
        $user->fingerprints = $fingerprintData;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Fingerprint saved successfully.']);
    }

}
