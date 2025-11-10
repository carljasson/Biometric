<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    BiometricController, AdminController, ScanController,
    PatientController, ExportController, AlertController,
    MedicalRecordController, ResponderController, FingerprintController,
    CheckController, RegisterController, ProfileController
};
use App\Models\Alert;

// =========================
// 🌐 GENERAL
// =========================
Route::get('/', fn() => view('welcome'))->name('welcome');

// =========================
// 👤 USER AUTH & DASHBOARD
// =========================
Route::get('/login', [BiometricController::class, 'showLoginForm'])->name('login');
Route::post('/login', [BiometricController::class, 'login'])->name('login.post');
Route::post('/login/pin', [BiometricController::class,'verifyPin'])->name('login.pin');
Route::post('/logout', [BiometricController::class, 'logout'])->name('logout');

// Registration steps
Route::match(['get','post'], '/register/step1', [BiometricController::class, 'step1'])->name('register.step1');
Route::match(['get','post'], '/register/step2', [BiometricController::class, 'step2'])->name('register.step2');
Route::get('/register/step3/{user?}', [BiometricController::class, 'step3'])->name('register.step3');
Route::post('/register/step3/{user?}', [BiometricController::class, 'registerStep3'])->name('register.step3.post');

// AJAX Validation
Route::post('/check-email', [BiometricController::class, 'checkEmail'])->name('check.email');
Route::post('/check-phone', [BiometricController::class, 'checkPhone'])->name('check.phone');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BiometricController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit-profile', [BiometricController::class, 'edit'])->name('edit.profile');
    Route::put('/update-profile', [BiometricController::class, 'update'])->name('update.profile');
});

// =========================
// 🛡️ ADMIN AUTH & DASHBOARD
// =========================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/register', [AdminController::class, 'showRegister'])->name('admin.register');
    Route::post('/register', [AdminController::class, 'register'])->name('admin.register.post');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Management
        Route::get('/admin-users', [AdminController::class, 'showAdminusers'])->name('admin.admin-users');
        Route::post('/admin-users', [AdminController::class, 'storeAdmin'])->name('admin.store');
        Route::put('/admin-users/{id}', [AdminController::class, 'updateAdmin'])->name('admin.update');
        Route::delete('/admin-users/{id}', [AdminController::class, 'destroyAdmin'])->name('admin.destroy');

        // App Users
        Route::get('/users', [AdminController::class, 'showAppUsers'])->name('admin.users');

        // Patients
        Route::resource('patients', PatientController::class)->names([
            'index' => 'admin.patients',
            'store' => 'patients.store',
            'update' => 'patients.update',
            'destroy' => 'patients.destroy',
        ]);

        // Settings, Exports, and Records
        Route::get('/settings', [AdminController::class, 'settingsPage'])->name('admin.settings');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::get('/export', [AdminController::class, 'exportPage'])->name('export.page');
        Route::get('/medical-records', [AdminController::class, 'medicalRecordsPage'])->name('admin.records');
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])->name('admin.medical_records.show');

        // Alerts
        Route::get('/alerts', [AdminController::class, 'showAlerts'])->name('admin.alerts');
        Route::post('/alerts/{id}/resolve', [AdminController::class, 'resolveAlert'])->name('admin.alerts.resolve');
        Route::get('/fetch-alerts', [AdminController::class, 'fetchAlerts'])->name('admin.fetch-alerts');
        Route::post('/alerts/mark-read', [AdminController::class, 'markAlertRead'])->name('admin.alerts.mark-read');
        Route::post('/alerts/{alert}/notify', [AdminController::class, 'notifyResponder'])->name('admin.alerts.notify');
        Route::get('/login-history', [AdminController::class, 'loginHistory'])->name('admin.login-history');
    });
});

// Export
Route::get('/admin/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
Route::get('/admin/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');

// =========================
// 👨‍⚕️ PATIENT ROUTES
// =========================
Route::resource('patients', PatientController::class)->only(['index', 'store', 'update', 'destroy']);
Route::view('/emergency', 'patient.emergency')->name('emergency');
Route::view('/about-this-app', 'patient.aboutthisapp')->name('about');

Route::middleware('auth')->prefix('patient')->group(function () {
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('patient.medical_records.index');
    Route::get('/medical-records/create', [MedicalRecordController::class, 'create'])->name('patient.medical_records.create');
    Route::post('/medical-records', [MedicalRecordController::class, 'store'])->name('patient.medical_records.store');
    Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])->name('patient.medical_records.show');
    Route::get('/medical-check/{id}', [MedicalRecordController::class, 'checkRecord'])->name('patient.check.medical');
});
Route::redirect('/patient/create', '/patient/medical-records/create')->name('patient.create');

// =========================
// 🚨 ALERTS
// =========================
Route::post('/patient/send-alert', [AlertController::class, 'sendAlert'])->name('patient.sendAlert');
Route::get('/debug-alerts', fn() => \App\Models\Alert::latest()->take(5)->get());

// =========================
// 🚑 RESPONDER ROUTES
// =========================
Route::get('/responder/login', [ResponderController::class, 'showLoginForm'])->name('responder.login');
Route::post('/responder/login', [ResponderController::class, 'login'])->name('responder.login.submit');
Route::get('/responder/logout', [ResponderController::class, 'logout'])->name('responder.logout');

Route::middleware('auth:responder')->prefix('responder')->group(function () {
    Route::get('/dashboard', [ResponderController::class, 'dashboard'])->name('responder.dashboard');
    Route::get('/profile', [ResponderController::class, 'profile'])->name('responder.profile');
    Route::get('/scan', [ResponderController::class, 'showScanPage'])->name('responder.scan');
    Route::get('/scan/fingerprint', [ResponderController::class, 'fingerprintScan'])->name('responder.scan.fingerprint');
    Route::get('/scan/face', [ResponderController::class, 'faceScan'])->name('responder.scan.face');
    Route::post('/scan/fingerprint', [ResponderController::class, 'identifyFingerprint'])->name('responder.scan.fingerprint.post');
    Route::post('/scan/face/identify', [ResponderController::class, 'identifyFace'])->name('responder.scan.face.identify');
    Route::get('/alerts/check', [ResponderController::class, 'checkAlerts'])->name('responder.alerts.check');
});

Route::resource('responders', ResponderController::class);
Route::middleware('admin.auth')->get('/admin/add-responder', [ResponderController::class, 'index'])->name('admin.add-responder');

// =========================
// 🧬 FINGERPRINT ROUTES
// =========================
Route::post('/fingerprint/capture', [FingerprintController::class, 'capture'])->name('fingerprint.capture');
Route::post('/fingerprint-register', [FingerprintController::class, 'registerFingerprint'])->name('fingerprint.register');
Route::get('/fingerprint/latest', [FingerprintController::class, 'latestScan'])->name('fingerprint.latest');

// =========================
// 👤 PROFILE MANAGEMENT
// =========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth scaffolding routes (if using Breeze/Fortify)
require __DIR__.'/auth.php';
