<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    BiometricController,
    AdminController,
    ScanController,
    PatientController,
    ExportController,
    AlertController,
    MedicalRecordController,
    ResponderController,
    FingerprintController,
    CheckController,
    RegisterController,
    ProfileController
};
use App\Models\Alert;

// =========================
// 🌐 GENERAL
// =========================
Route::get('/', fn () => view('welcome'))->name('welcome');

// =========================
// 👤 USER AUTH & DASHBOARD
// =========================
Route::get('/login', [BiometricController::class, 'showLoginForm'])->name('login');
Route::post('/login', [BiometricController::class, 'login'])->name('login.post');
Route::post('/login/pin', [BiometricController::class,'verifyPin'])->name('login.pin');
Route::post('/logout', [BiometricController::class, 'logout'])->name('logout');

// Registration Steps
Route::match(['get', 'post'], '/register/step1', [BiometricController::class, 'step1'])->name('register.step1');
Route::match(['get', 'post'], '/register/step2', [BiometricController::class, 'step2'])->name('register.step2');
Route::get('/register/step3', [BiometricController::class, 'step3'])->name('register.step3');
Route::post('/register/step3', [BiometricController::class, 'registerStep3'])->name('register.step3.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [BiometricController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit-profile', [BiometricController::class, 'edit'])->name('edit.profile');
    Route::put('/update-profile', [BiometricController::class, 'update'])->name('update.profile');
});

// =========================
// 🛡️ ADMIN AUTH & DASHBOARD
// =========================
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest (not logged in)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminController::class, 'login'])->name('login.post');
        Route::get('register', [AdminController::class, 'showRegister'])->name('register');
        Route::post('register', [AdminController::class, 'register'])->name('register.post');
    });

    // Authenticated Admin
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('upload-photo', [AdminController::class, 'uploadPhoto'])->name('uploadPhoto');
        Route::post('announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');

        // Admin Users
        Route::get('admin-users', [AdminController::class, 'showAdminusers'])->name('admin-users');
        Route::post('admin-users', [AdminController::class, 'storeAdmin'])->name('store');
        Route::put('admin-users/{id}', [AdminController::class, 'updateAdmin'])->name('update');
        Route::delete('admin-users/{id}', [AdminController::class, 'destroyAdmin'])->name('destroy');

        // App Users
        Route::get('users', [AdminController::class, 'showAppUsers'])->name('users');

        // Alerts
        Route::get('alerts', [AdminController::class, 'showAlerts'])->name('alerts');
        Route::post('alerts/{id}/resolve', [AdminController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::get('fetch-alerts', [AdminController::class, 'fetchAlerts'])->name('fetch-alerts');
        Route::post('mark-alerts-read', [AdminController::class, 'markAlertsRead'])->name('mark-alerts-read');
        Route::post('alerts/{alert}/notify', [AdminController::class, 'notifyResponder'])->name('alerts.notify');

        // Patients
        Route::resource('patients', PatientController::class)->names('patients');

        // Settings
        Route::get('settings', [AdminController::class, 'settingsPage'])->name('settings');
        Route::post('settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');

        // Export
        Route::get('export', [AdminController::class, 'exportPage'])->name('export.page');
        Route::get('export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
        Route::get('export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');

        // Medical Records
        Route::get('medical-records', [AdminController::class, 'medicalRecordsPage'])->name('records');
        Route::get('medical-records/{id}', [MedicalRecordController::class, 'show'])->name('medical_records.show');

        // Responder Management
        Route::get('add-responder', [ResponderController::class, 'index'])->name('add-responder');

        // Login History
        Route::get('login-history', [AdminController::class, 'loginHistory'])->name('login-history');
    });
});

// =========================
// 🔍 SCAN FEATURES
// =========================
Route::get('/scan', [BiometricController::class, 'scanForm']);
Route::post('/scan', [BiometricController::class, 'scanIdentify']);

// =========================
// 👨‍⚕️ PATIENT ROUTES
// =========================
Route::middleware('auth')->prefix('patient')->group(function () {
    Route::get('medical-records/create', [MedicalRecordController::class, 'create'])->name('patient.medical_records.create');
    Route::post('medical-records', [MedicalRecordController::class, 'store'])->name('patient.medical_records.store');
    Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('patient.medical_records.index');
    Route::get('medical-records/{id}', [MedicalRecordController::class, 'show'])->name('patient.medical_records.show');
    Route::get('medical-check/{id}', [MedicalRecordController::class, 'checkRecord'])->name('patient.check.medical');
});

Route::get('/patient/create', fn () => redirect()->route('patient.medical_records.create'))->name('patient.create');

// Emergency
Route::get('/emergency', fn () => view('patient.emergency'))->name('emergency');
Route::get('/about-this-app', fn () => view('patient.aboutthisapp'))->name('about');
Route::post('/patient/send-alert', [AlertController::class, 'sendAlert'])->name('patient.sendAlert');

// =========================
// 🚑 RESPONDER ROUTES
// =========================
Route::get('/responder/login', [ResponderController::class, 'showLoginForm'])->name('responder.login');
Route::post('/responder/login', [ResponderController::class, 'login'])->name('responder.login.submit');
Route::get('/responder/logout', [ResponderController::class, 'logout'])->name('responder.logout');

Route::middleware('auth:responder')->group(function () {
    Route::get('/responder/dashboard', [ResponderController::class, 'dashboard'])->name('responder.dashboard');
    Route::get('/responder/profile', [ResponderController::class, 'profile'])->name('responder.profile');
    Route::get('/responder/scan', [ResponderController::class, 'showScanPage'])->name('responder.scan');
    Route::get('/responder/scan/face', [ResponderController::class, 'faceScan'])->name('responder.scan.face');
    Route::get('/responder/scan/fingerprint', [ResponderController::class, 'fingerprintScan'])->name('responder.scan.fingerprint');
    Route::post('/responder/scan/fingerprint', [ResponderController::class, 'identifyFingerprint'])->name('responder.scan.fingerprint.post');
    Route::post('/responder/scan/face/identify', [ResponderController::class, 'identifyFace'])->name('responder.scan.face.identify');
    Route::get('/responder/alerts/check', [ResponderController::class, 'checkAlerts'])->name('responder.alerts.check');
});

Route::get('/responder/demo', [ResponderController::class, 'insertDemoResponder']);
Route::resource('responders', ResponderController::class);

// =========================
// 🧠 FINGERPRINT
// =========================
Route::post('/fingerprint/capture', [FingerprintController::class, 'capture'])->name('fingerprint.capture');
Route::post('/fingerprint-register', [FingerprintController::class, 'registerFingerprint']);
Route::get('/fingerprint/latest', [FingerprintController::class, 'latestScan']);

// =========================
// ⚙️ SYSTEM ROUTES
// =========================
Route::post('/check-phone', [BiometricController::class, 'checkPhone'])->name('check.phone');
Route::post('/check-email', [BiometricController::class, 'checkEmail'])->name('check.email');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
