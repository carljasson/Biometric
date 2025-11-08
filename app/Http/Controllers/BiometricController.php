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
use Illuminate\Support\Facades\Http;


class BiometricController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }


public function login(Request $request)
{
    // ✅ Step 1: Validate basic form inputs
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'g-recaptcha-response' => 'required', // Google reCAPTCHA v3 token
    ]);

    // ✅ Step 2: Verify Google reCAPTCHA v3
    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret'),
        'response' => $request->input('g-recaptcha-response'),
        'remoteip' => $request->ip(),
    ]);

    $data = $response->json();

    // ✅ Step 3: Check the verification result
    if (empty($data['success']) || ($data['score'] ?? 0) < 0.5) {
        return back()->withErrors([
            'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
        ])->withInput();
    }

    // ✅ Step 4: Verify credentials
    $user = \App\Models\User::where('email', $request->email)->first();
    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])
                     ->withInput();
    }

    // ✅ Step 5: Generate email PIN
    $pin = rand(100000, 999999);
    Session::put('login_pin', $pin);
    Session::put('login_user_id', $user->id);
    Session::put('pin_expires', now()->addMinutes(5));

    // Send PIN to user email
    Mail::raw("Your login PIN is: $pin", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Your Login PIN');
    });

    // ✅ Step 6: Return back and show PIN modal
return back()->with('showPinModal', true)->withInput(['email' => $request->email]);

}

public function verifyPin(Request $request)
{
    $request->validate([
        'pin' => 'required|digits:6',
    ]);

    $storedPin = session('login_pin');
    $expiresAt = session('pin_expires');
    $userId = session('login_user_id');

    if (!$storedPin || !$userId || now()->gt($expiresAt)) {
        return back()->withErrors(['pin' => 'PIN expired or invalid.'])->withInput();
    }

    if ($request->pin != $storedPin) {
        return back()->withErrors(['pin' => 'Incorrect PIN'])->withInput();
    }

    // ✅ PIN verified, log the user in
    $user = \App\Models\User::find($userId);
    Auth::login($user);

    // Clear PIN session
    session()->forget(['login_pin', 'login_user_id', 'pin_expires', 'showPinModal']);

    return redirect()->intended('/dashboard');
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
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
// app/Http/Controllers/RegisterController.php (or wherever step1 is)
public function step1(Request $request)
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
            'phone'           => 'required|digits:11',
            'address'         => 'required|string|max:255',
            'contact_name'    => 'required|string|max:255',
            'contact_number'  => 'required|digits:11',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:8|max:16|confirmed',
        ]);

        // ✅ 1. Save user
        $user = \App\Models\User::create([
            'firstname'       => $validated['firstname'],
            'middlename'      => $validated['middlename'] ?? null,
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

        // ✅ 2. Store user ID in session
        session(['user_id' => $user->id]);

        // ✅ 3. Return a small “launch” view with JavaScript to call your EXE
        return view('register.open-fingerprint', ['userId' => $user->id]);
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



public function Step3(Request $request)
{
    $user = User::find(session('user_id')) ?? auth()->user();

    if (!$user) {
        return redirect('/register/step1')->with('error', 'User session not found.');
    }

    // If user clicked skip
    if ($request->input('action') === 'skip') {
        return redirect('/welcome')->with('info', 'You skipped the face scan.');
    }

    // Validate face scan data exists
    if (!$request->face_descriptor || !$request->face_image) {
        return redirect()->back()->with('error', 'Face scan failed. Please try again.');
    }

    // ✅ Save both descriptor + image
    $descriptor = json_decode($request->face_descriptor, true);

    if (is_array($descriptor) && count($descriptor) === 128) {
        $user->face_descriptor = json_encode($descriptor);
        $user->face_image = $request->face_image; // ✅ Save Base64 image
        $user->save();

        return redirect('/welcome')->with('success', 'Face scan saved successfully!');
    }

    return redirect()->back()->with('error', 'Invalid face data.');
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

  // AJAX: check if phone exists
    public function checkPhone(Request $request)
    {
        $exists = User::where('phone', $request->phone)->exists();
        return response()->json(['exists' => $exists]);
    }

    // AJAX: check if email exists
public function checkEmail(Request $request)
{
    // Make sure to validate input
    $request->validate(['email' => 'required|email']);

    // Check if email exists
    $exists = \App\Models\User::where('email', $request->email)->exists();

    // Return JSON
    return response()->json(['exists' => $exists]);
}

    // Other methods remain unchanged...

    public function step2View() {
    return view('step2'); // Fingerprint registration
}
public function checkFingerprintStatus(Request $request)
{
    // Get the user ID from session set during Step1
    $userId = session('user_id');

    if (!$userId) {
        return response()->json(['status' => 0]);
    }

    $user = \App\Models\User::find($userId);

    return response()->json([
        'status' => $user?->fingerprint_registered ?? 0
    ]);
}

public function fingerprintPage($id)
{
    $user = User::findOrFail($id);

    // Check if fingerprint is already registered
    if ($user->fingerprint_registered) {
        // Redirect to step 3
        return redirect()->route('registration.step3', ['user' => $user->id]);
    }

    // Otherwise, show fingerprint registration page
    return view('register.fingerprint', compact('user'));
}


}


