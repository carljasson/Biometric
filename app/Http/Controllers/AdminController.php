<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\Alert;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Str; 

class AdminController extends Controller
{
    protected $maxAttempts = 3;        // 3 wrong tries allowed
    protected $decaySeconds = 3600;    // 1 hour lockout (3600 seconds)

    // ✅ Show the login form
    public function showLoginForm()
    {
        return view('admin_login');
    }

    // ✅ Process the login form
public function showPinForm()
{
    // If no PIN session, redirect back to login
    if (!Session::has('responder_id')) {
        return redirect()->route('responder.login')->with('error', 'Please login first.');
    }

    return view('responder.login-pin');
}


public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8',
    ]);

    $key = $this->throttleKey($request);

    // If locked out
    if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        $message = 'Too many failed login attempts. Please wait ' . gmdate('H:i:s', $seconds) . ' before trying again.';
        return back()
            ->withInput($request->only('email'))
            ->with('lockout', $message)
            ->with('lockout_seconds', $seconds);
    }

    // Attempt credentials (but DO NOT keep the user logged in yet)
    if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {

        // clear rate limiter
        RateLimiter::clear($key);

        // get admin model instance
        $admin = Auth::guard('admin')->user();

        // Generate PIN and save pending login info in session (expires in 5 minutes)
        $pin = random_int(100000, 999999);
        session([
            'admin_pending_id'     => $admin->id,
            'admin_login_pin'      => $pin,
            'admin_pin_expires_at' => now()->addMinutes(5)->toDateTimeString(),
        ]);

        // Immediately logout so app does not treat this attempt as an authenticated session
        Auth::guard('admin')->logout();

        // Send PIN — simple/quick implementation; switch to Mailable if you prefer
        try {
            Mail::raw("Your admin login PIN is: {$pin}\nThis PIN will expire in 5 minutes.", function ($msg) use ($admin) {
                $msg->to($admin->email)->subject('Your Admin Login PIN');
            });
        } catch (\Exception $e) {
            // Optionally log email sending failure; still redirect so admin can request resend
            \Log::error('Failed sending admin login PIN: '.$e->getMessage());
        }

        // redirect to pin entry page (route name described below)
        return redirect()->route('admin.login.pin')->with('success', 'A verification PIN has been sent to your email.');
    }

    // Failed login -> increment attempts
    RateLimiter::hit($key, $this->decaySeconds);

    $attemptsLeft = RateLimiter::remaining($key, $this->maxAttempts);
    $error = $attemptsLeft > 0
        ? "Invalid credentials. You have {$attemptsLeft} attempt(s) remaining."
        : "Account locked. Please wait 1 hour before trying again.";

    return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => $error]);
}

    /**
     * Generate a unique key for throttling based on user email + IP
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    // ✅ Admin dashboard
    public function dashboard()
    {
        $admin = Admin::find(session('admin_id'));

        $users = User::all();
        $totalUsers = $users->count();
        $weeklyUsers = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthlyUsers = User::whereMonth('created_at', now()->month)->count();
        $yearlyUsers = User::whereYear('created_at', now()->year)->count();
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        $totalPatients = Patient::count();
    $weeklyPatients = Patient::whereBetween('admit_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
    $monthlyPatients = Patient::whereMonth('admit_date', Carbon::now()->month)->count();
    $yearlyPatients = Patient::whereYear('admit_date', Carbon::now()->year)->count();
$announcements = Announcement::where(function ($query) {
    $query->whereNull('expired_at')
          ->orWhere('expired_at', '>', now());
})->orderBy('created_at', 'desc')->get();

        $patients = Patient::all();

        return view('admin.dashboard', compact(
            'admin',
           
            'totalUsers', 'weeklyUsers', 'monthlyUsers', 'yearlyUsers',
        'totalPatients', 'weeklyPatients', 'monthlyPatients', 'yearlyPatients',
        'announcements', 'users', 'patients'
        ));
    }

    // ✅ Admin logout
    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->forget('admin_id');
        return redirect()->route('admin.login');
    }

    // ✅ Upload admin profile photo
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admin = Admin::find(session('admin_id'));
        $filename = time() . '.' . $request->photo->extension();
        $request->photo->move(public_path('uploads/profile'), $filename);
        $admin->profile_photo = 'uploads/profile/' . $filename;
        $admin->save();

        return redirect()->route('admin.dashboard');
    }
public function showAppUsers()
{
    $admin = Admin::find(session('admin_id'));
    $users = User::all();
    return view('admin.users', compact('users', 'admin'));
}

    // ✅ Show broadcast form (optional standalone page)
    public function showBroadcastForm()
    {
        return view('admin.broadcast');
    }

    // ✅ Broadcast via optional separate form
    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        Announcement::create([
            'message' => $request->message,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement broadcasted!');
    }

    // ✅ Save broadcast from dashboard modal
   public function storeAnnouncement(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
        'expired_at' => 'required|date|after:now',
    ]);

    Announcement::create([
        'title' => $request->title,
        'message' => $request->message,
         'expired_at' => $request->expired_at,
    ]);

    return redirect()->back()->with('success', 'Announcement broadcasted successfully!');
}

    // ✅ Show admin registration form
    public function showRegister()
    {
        return view('admin_register');
    }

    // ✅ Process registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|confirmed|min:6',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.login')->with('success', 'Admin registered successfully.');
    }

    // ✅ Announcements page
    public function manageAnnouncements()
    {
      $announcements = Announcement::where(function ($query) {
    $query->whereNull('expired_at')
          ->orWhere('expired_at', '>', now());
})->orderBy('created_at', 'desc')->get();

        return view('admin.announcements', compact('announcements'));
    }

    // ✅ Settings page (fixed and sends admin as $settings)
    public function settingsPage()
    {
        $admin = Admin::find(session('admin_id'));
        return view('admin.settings', ['settings' => $admin]);
    }

    // ✅ Export data page (fixed with $admins)
public function exportPage()
{
    $admins = Admin::all();
    $patients = Patient::paginate(10); // paginate 10 per page

    // Fetch alerts with the associated user
    $alerts = Alert::with('user')->latest()->take(10)->get();

    return view('admin.data_export', compact('admins', 'patients', 'alerts'));
}

    // ✅ Emergency alerts page




// ✅ Show dedicated Patient Management page
public function showPatients()
{
    $admin = Admin::find(session('admin_id'));
    $patients = Patient::all(); // or paginate(10) for large datasets
    return view('admin.patients', compact('admin', 'patients'));
}

    // ✅ Medical records page
    public function medicalRecordsPage()
{
$patients = MedicalRecord::with('user')->paginate(10);
    return view('admin.medical-records', compact('patients'));
}

    // ✅ Admin Users management page
   public function showAdminUsers()
{
    $admin = Admin::find(session('admin_id')); // ✅ Get current admin
    $admins = Admin::all();
    return view('admin.admin-users', compact('admins', 'admin')); // ✅ Pass $admin to view
}

    // ✅ Delete admin
    public function destroyAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.admin-users')->with('success', 'Admin deleted successfully.');
    }

    // ✅ Store new admin
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,superadmin',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.admin-users')->with('success', 'Admin added successfully!');
    }

    // ✅ Update admin
    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'role' => 'required|in:admin,superadmin',
        ]);

        $admin = Admin::findOrFail($id);

        $dirty = false;
        if (
            $admin->name !== $request->name ||
            $admin->email !== $request->email ||
            $admin->role !== $request->role
        ) {
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);
            $dirty = true;
        }

        return $dirty
            ? redirect()->route('admin.admin-users')->with('success', 'Admin updated successfully.')
            : redirect()->route('admin.admin-users');
    }

    // ✅ Update settings
   public function updateSettings(Request $request)
{
    $request->validate([
        'theme' => 'required|in:light,dark',
        'auto_logout' => 'required|integer|min:1',
        'notifications' => 'required|in:email,sms,none',
        'current_password' => 'nullable|string',
        'new_password' => 'nullable|string|confirmed|min:6',
    ]);

    $admin = Admin::find(session('admin_id'));

    if (!$admin) {
        return redirect()->back()->withErrors(['admin' => 'Admin not found.']);
    }

    // 🔐 If password fields are filled, check and update
    if ($request->filled('current_password') && $request->filled('new_password')) {
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->password = Hash::make($request->new_password);
    }

    // ✅ Update other settings
    $admin->theme = $request->theme;
    $admin->auto_logout = $request->auto_logout;
    $admin->notifications = $request->notifications;
    $admin->save();

    return redirect()->route('admin.settings')->with('success', '✅ Settings updated successfully!');
}

public function showAlerts()
{
    $alerts = Alert::with('user')->orderBy('created_at', 'desc')->paginate(10);
    return view('admin.alerts', compact('alerts'));
}
public function resolveAlert($id)
{
    $alert = Alert::findOrFail($id);
    $alert->status = 'Resolved';
    $alert->save();

    return redirect()->back()->with('success', 'Alert marked as resolved.');
}
public function index()
{
    $alerts = Alert::with('patient')->latest()->get(); // eager load patient info
    return view('admin.alerts.index', compact('alerts'));
}

public function fetchAlerts()
{
    $alerts = Alert::orderBy('created_at', 'desc')->get();

    $unreadCount = $alerts->where('read', 0)->count();

    return response()->json([
        'alerts' => $alerts,
        'unreadCount' => $unreadCount
    ]);
}

public function markAlertsRead(Request $request)
{
    $alertId = $request->id;
    $alert = Alert::find($alertId);

    if ($alert) {
        $alert->read = 1;
        $alert->save();
    }

    return response()->json(['success' => true]);
}

public function markAlertRead(Request $request)
{
    $alert = Alert::find($request->id);
    if ($alert) {
        $alert->read = true;
        $alert->save();
    }

    return response()->json(['success' => true]);
}

public function addResponder()
{
    return view('admin.addresponder');  // Blade file: resources/views/addresponder.blade.php
}



public function loginHistory()
{
    $entries = LoginHistory::with('user')
        ->orderBy('logged_in_at', 'desc')
        ->get();

    return view('admin.login-history', compact('entries'));
}


public function showPinForm()
{
    if (!session()->has('admin_pending_id') || !session()->has('admin_login_pin')) {
        return redirect()->route('admin.login')->withErrors('Please login first.');
    }

    // pass expiry seconds to show countdown if you want
    $expiresAt = session('admin_pin_expires_at');
    $secondsLeft = $expiresAt ? max(0, Carbon::parse($expiresAt)->diffInSeconds(now())) : null;

    return view('admin.login-pin', compact('secondsLeft'));
}

public function verifyPin(Request $request)
{
    $request->validate(['pin' => 'required|digits:6']);

    if (!session()->has('admin_pending_id') || !session()->has('admin_login_pin')) {
        return redirect()->route('admin.login')->withErrors('Session expired. Please login again.');
    }

    // check expiry
    $expiresAt = session('admin_pin_expires_at');
    if (!$expiresAt || now()->greaterThan(Carbon::parse($expiresAt))) {
        session()->forget(['admin_pending_id', 'admin_login_pin', 'admin_pin_expires_at']);
        return redirect()->route('admin.login')->withErrors('PIN expired. Please login again.');
    }

    // check PIN
    if ($request->pin != session('admin_login_pin')) {
        return back()->withErrors(['pin' => 'Incorrect PIN.'])->withInput();
    }

    // PIN correct — log in the admin and clear session
    $adminId = session('admin_pending_id');
    Auth::guard('admin')->loginUsingId($adminId);

    // Set session('admin_id') if your app relies on that
    session(['admin_id' => $adminId]);

    // Clear temporary session keys
    session()->forget(['admin_pending_id', 'admin_login_pin', 'admin_pin_expires_at']);

    return redirect()->intended('/admin/dashboard')->with('success', 'Welcome back, Admin!');
}

public function resendPin()
{
    if (!session()->has('admin_pending_id')) {
        return redirect()->route('admin.login')->withErrors('Please login first.');
    }

    $admin = Admin::find(session('admin_pending_id'));
    if (!$admin) {
        session()->forget(['admin_pending_id', 'admin_login_pin', 'admin_pin_expires_at']);
        return redirect()->route('admin.login')->withErrors('Admin not found. Please login again.');
    }

    $pin = random_int(100000, 999999);

    session([
        'admin_login_pin'      => $pin,
        'admin_pin_expires_at' => now()->addMinutes(5)->toDateTimeString(),
    ]);

    try {
        Mail::raw("Your new admin login PIN is: {$pin}\nThis PIN will expire in 5 minutes.", function ($msg) use ($admin) {
            $msg->to($admin->email)->subject('Your NEW Admin Login PIN');
        });
    } catch (\Exception $e) {
        \Log::error('Failed sending admin resend PIN: '.$e->getMessage());
    }

    return back()->with('success', 'A new PIN has been sent to your email.');
}




}
