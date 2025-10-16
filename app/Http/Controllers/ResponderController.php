<?php

namespace App\Http\Controllers;

use App\Models\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Announcement;



class ResponderController extends Controller
{
    // Show login form
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    $responder = Responder::where('email', $credentials['email'])->first();

    if (!$responder) {
        return back()->with('error', 'Responder not found.');
    }

    if (!Hash::check($credentials['password'], $responder->password)) {
        return back()->with('error', 'Incorrect password.');
    }

    Auth::guard('responder')->login($responder); // ✅ Manually log in
    return redirect()->route('responder.dashboard');
}

public function showLoginForm()
{
    return view('responder.login');
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
        return view('addresponder', compact('responders'));
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
}

