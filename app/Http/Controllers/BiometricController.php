<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use Illuminate\Support\Str;

class BiometricController extends Controller
{
    // Show login form
    public function login()
    {
        return view('auth.login');
    }

    // Handle login
    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
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
    public function step1(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'firstname' => 'required',
                'middlename' => 'nullable',
                'lastname' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'phone' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
                'address' => 'required',
                'birthday' => 'required|date',
                'age' => 'required|numeric',
                'gender' => 'required|string',
                'status' => 'required|string',
                'contact_name' => 'required',
                'contact_number' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            ]);

            $middle = $validated['middlename'] ? ' ' . $validated['middlename'] : '';
            $validated['name'] = $validated['firstname'] . $middle . ' ' . $validated['lastname'];

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            session(['user_id' => $user->id]);

return redirect()->route('register.step2');
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


public function checkPhone(Request $request)
{
    $exists = User::where('phone', $request->phone)->exists();
    return response()->json(['exists' => $exists]);
}



}
