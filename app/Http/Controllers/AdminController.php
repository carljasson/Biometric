<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\Alert;

use Illuminate\Support\Facades\Hash;
use App\Models\MedicalRecord;
use Carbon\Carbon;


class AdminController extends Controller
{
    // ✅ Show the login form
    public function showLoginForm()
    {
        return view('admin_login');
    }

    // ✅ Process the login form
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        session(['admin_id' => $admin->id]);
        return redirect()->route('admin.dashboard');
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

}
