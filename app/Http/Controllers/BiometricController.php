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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginPinMail;


class BiometricController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }


public function login(Request $request)
{
    // ✅ Step 1: Validate form input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'g-recaptcha-response' => 'required',
    ]);

    // ✅ Step 2: reCAPTCHA v3 Verification
    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret'),
        'response' => $request->input('g-recaptcha-response'),
        'remoteip' => $request->ip(),
    ]);

    $data = $response->json();

    if (empty($data['success']) || ($data['score'] ?? 0) < 0.5) {
        return back()->withErrors([
            'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
        ])->withInput();
    }

    // ✅ Step 3: Rate Limiting (3 attempts, 60s cooldown)
    $key = Str::lower($request->email) . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($key, 3)) {
        $seconds = RateLimiter::availableIn($key);
        return back()
            ->with('lockout', $seconds)
            ->withErrors(['email' => "Too many attempts. Try again in {$seconds} seconds."]);
    }

    // ✅ Step 4: Manual credential check
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        // Count failed attempt
        RateLimiter::hit($key, 60); // 60 seconds cooldown

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // ✅ Step 5: If success, clear previous failed attempts
    RateLimiter::clear($key);

    // ✅ Step 6: Generate 6-digit PIN and store in session
    $pin = rand(100000, 999999);
    session([
        'login_pin' => $pin,
        'login_user_id' => $user->id,
        'pin_expires' => now()->addMinutes(5),
    ]);

    // ✅ Step 7: Send PIN via email
    Mail::raw("Your login PIN is $pin", function ($message) use ($user) {
        $message->to($user->email)->subject('Your Biometric Medical Access Login PIN');
    });

    // ✅ Step 8: Do not log in yet — show PIN modal
    return back()
        ->with('showPinModal', true)
        ->withInput(['email' => $request->email]);
}
public function verifyPin(Request $request) {
    $request->validate(['pin'=>'required|digits:6']);

    $storedPin = session('login_pin');
    $userId = session('login_user_id');
    $expiresAt = session('pin_expires');

    if (!$storedPin || !$userId || now()->gt($expiresAt)) {
        return back()->withErrors(['pin'=>'PIN expired'])->withInput();
    }

    if ($request->pin != $storedPin) {
        return back()->withErrors(['pin'=>'Incorrect PIN'])->withInput();
    }

    // ✅ PIN correct, log user in
    $user = User::find($userId);
    Auth::login($user);

    // ✅ Record login history
    LoginHistory::create([
        'loggable_id' => $user->id,
        'loggable_type' => get_class($user), // App\Models\User
        'method' => 'PIN',
        'ip' => $request->ip(),
        'device' => $request->userAgent(),
        'location' => ['city'=>'Unknown','country'=>'Unknown'], // you can add geolocation here
        'session_id' => session()->getId(),
        'logged_in_at' => now(),
    ]);

    // Clear PIN session
    session()->forget(['login_pin','login_user_id','pin_expires','showPinModal']);

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
            'phone'           => 'required|digits:11|unique:users,phone',
            'address'         => 'required|string|max:255',
            'contact_name'    => 'required|string|max:255',
            'contact_number'  => 'required|digits:11',
            'email'           => 'required|email|unique:users,email',
            'password'        => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'max:16',
                'regex:/[a-z]/',      // at least 1 lowercase
                'regex:/[A-Z]/',      // at least 1 uppercase
                'regex:/[0-9]/',      // at least 1 number
                'regex:/[@$!%*#?&]/'  // at least 1 special char
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            'phone.unique'   => 'Phone number is already in use.',
            'email.unique'   => 'Email is already in use.',
        ]);

        // Save user
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

        // Store user ID in session for fingerprint step
        session(['user_id' => $user->id]);

        // Return fingerprint launch view
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

public function Step3(User $user)
    {
        return view('register.step3', compact('user'));
    }

// POST /register/step3/{user}
public function Step3Submit(Request $request, User $user)
{
    // Skip face scan
    if ($request->input('action') === 'skip') {
        return redirect('/welcome')->with('info', 'You skipped the face scan.');
    }

    // Validate inputs
    if (!$request->face_descriptor || !$request->face_image) {
        return redirect()->back()->with('error', 'Face scan failed. Please try again.');
    }

    $descriptor = json_decode($request->face_descriptor, true);
    if (!is_array($descriptor) || count($descriptor) !== 128) {
        return redirect()->back()->with('error', 'Invalid face data.');
    }

    // Save descriptor
    $user->face_descriptor = json_encode($descriptor);

    // Save face image to storage
    $imageData = $request->face_image;
    $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);

    $imageName = 'faces/' . $user->id . '_' . time() . '.jpg';
    Storage::disk('public')->put($imageName, base64_decode($imageData));
    $user->face_image = $imageName;

    $user->save();

    return redirect('/welcome')->with('success', 'Face scan saved successfully!');
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




public function validatePhone(Request $request)
{
    $exists = \App\Models\User::where('phone', $request->phone)->exists();

    return response()->json(['exists' => $exists]);
}

public function validateEmail(Request $request)
{
    $exists = \App\Models\User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
}

}