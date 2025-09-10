<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BiometricController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ResponderController;


// =========================
// 🌐 GENERAL
// =========================
Route::get('/', fn () => view('welcome'))->name('welcome');

// =========================
// 👤 USER AUTH & DASHBOARD
// =========================
Route::get('/login', [BiometricController::class, 'showLoginForm'])->name('login');

// Handle login submit (POST)
Route::post('/login', [BiometricController::class, 'login'])->name('login.post');


Route::post('/logout', [BiometricController::class, 'logout'])->name('logout');

Route::match(['get', 'post'], '/register/step1', [BiometricController::class, 'step1'])->name('register.step1');
Route::match(['get', 'post'], '/register/step2', [BiometricController::class, 'step2'])->name('register.step2');
Route::get('/register/step3', [BiometricController::class, 'step3'])->name('register.step3');

// ✅ ADD THIS LINE BELOW ⬇
Route::post('/register/step3', [BiometricController::class, 'registerStep3'])->name('register.step3.post');

Route::middleware(['auth'])->group(function () {
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

    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/upload-photo', [AdminController::class, 'uploadPhoto'])->name('admin.uploadPhoto');
        Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    });
});

// =========================
// 🔍 SCAN FEATURES
// =========================
Route::get('/scan', [BiometricController::class, 'scanForm']);
Route::post('/scan', [BiometricController::class, 'scanIdentify']);
// =========================
// 👨‍⚕️ PATIENT RESOURCE ROUTES
// =========================
Route::resource('patients', PatientController::class);

// Emergency Route
Route::get('/emergency', function () {
    return view('patient.emergency');
})->name('emergency');

// About This App Route
Route::get('/about-this-app', function () {
    return view('patient.aboutthisapp');
})->name('about');

// Admin Pages
Route::prefix('admin')->middleware(['admin.auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Admin User Management
    Route::get('/admin-users', [AdminController::class, 'showAdminusers'])->name('admin.admin-users');
    Route::post('/admin-users', [AdminController::class, 'storeAdmin'])->name('admin.store');
    Route::put('/admin-users/{id}', [AdminController::class, 'updateAdmin'])->name('admin.update');
    Route::delete('/admin-users/{id}', [AdminController::class, 'destroyAdmin'])->name('admin.destroy');
// Add this:
Route::get('/alerts', [AdminController::class, 'showAlerts'])->name('admin.alerts');

    // Regular App Users
    Route::get('/users', [AdminController::class, 'showAppUsers'])->name('admin.users');

    // Patient Routes
    Route::get('/patients', [PatientController::class, 'index'])->name('admin.patients');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

    // Other Admin Pages
    Route::get('/settings', [AdminController::class, 'settingsPage'])->name('admin.settings');
    Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/export', [AdminController::class, 'exportPage'])->name('export.page');
    Route::get('/medical-records', [AdminController::class, 'medicalRecordsPage'])->name('admin.records');
Route::get('/admin/medical-records/{id}', [MedicalRecordController::class, 'show'])->middleware('admin.auth')->name('admin.medical_records.show');

    // RECOMMENDED: Use this for patient listing
    Route::get('/patients', [PatientController::class, 'index'])->name('admin.patients');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

});

// Export Routes
Route::get('/admin/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
Route::get('/admin/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');

// =========================
// MEDICAL RECORD ROUTES
// =========================
Route::middleware(['auth'])->group(function () {
    Route::prefix('patient')->group(function () {
        // Medical Record Routes for Patient
        Route::get('/medical-records/create', [MedicalRecordController::class, 'create'])->name('patient.medical_records.create');
        Route::post('/medical-records', [MedicalRecordController::class, 'store'])->name('patient.medical_records.store');
        Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('patient.medical_records.index');
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])->name('patient.medical_records.show');
        // Redirect based on whether patient has a medical record
Route::get('/patient/medical-check/{id}', [MedicalRecordController::class, 'checkRecord'])->name('patient.check.medical');

    });
});

// Alias route name: patient.create
Route::get('/patient/create', function () {
    return redirect()->route('patient.medical_records.create');
})->name('patient.create');
//admin
Route::get('/test-view', function () {
    return view('admin.medical-records');
});

// Emergency Responder Login Routes
Route::get('/responder/login', [ResponderController::class, 'showLoginForm'])->name('responder.login');
Route::post('/responder/login', [ResponderController::class, 'login'])->name('responder.login.submit');
Route::get('/responder/logout', [ResponderController::class, 'logout'])->name('responder.logout');

// Responder Dashboard (protected)
Route::middleware(['auth:responder'])->group(function () {
    Route::get('/responder/dashboard', [ResponderController::class, 'dashboard'])->name('responder.dashboard');
    Route::get('/responder/profile', [ResponderController::class, 'profile'])->name('responder.profile'); // ✅ Add this

});

// Insert a demo responder for testing
Route::get('/responder/demo', [ResponderController::class, 'insertDemoResponder']);
Route::get('/responder/scan', [ResponderController::class, 'showScanPage'])->name('responder.scan');

Route::middleware(['auth:responder'])->group(function () {
    Route::get('/responder/scan/fingerprint', [ResponderController::class, 'fingerprintScan'])->name('responder.scan.fingerprint');
   Route::post('/responder/scan/face/identify', [ResponderController::class, 'identifyFace'])->name('responder.scan.face.identify');

});
Route::post('/responder/scan/fingerprint', [ResponderController::class, 'identifyFingerprint'])->name('responder.scan.fingerprint.post');
Route::post('/responder/scan/face/identify', [ResponderController::class, 'identifyFace'])->name('responder.scan.face.identify');
Route::get('/scan', [App\Http\Controllers\BiometricController::class, 'scanPage'])->name('scan.page');

Route::get('/responder/scan', [ResponderController::class, 'showScanPage'])->name('responder.scan');
Route::post('/scan/submit', [BiometricController::class, 'submitScan'])->name('scan.submit');
Route::get('/responder/scan/face', [ResponderController::class, 'faceScan'])->name('responder.scan.face');

Route::post('/check-phone', [App\Http\Controllers\Auth\BiometricController::class, 'checkPhone']);
Route::post('/check-email', [App\Http\Controllers\Auth\BiometricController::class, 'checkEmail']);

use App\Http\Controllers\CheckController;

Route::post('/check-phone', [CheckController::class, 'checkPhone']);
Route::post('/check-email', [CheckController::class, 'checkEmail']);


Route::get('/admin/alerts', [AdminController::class, 'showAlerts'])->name('admin.alerts');

Route::post('/admin/alerts/{id}/resolve', [AdminController::class, 'resolveAlert'])->name('alerts.resolve');

Route::post('/patient/send-alert', [PatientController::class, 'sendAlert'])->name('patient.sendAlert');

Route::get('/admin/alerts', [AdminController::class, 'showAlerts'])->name('admin.alerts');
Route::post('/admin/alerts/{id}/resolve', [AdminController::class, 'resolveAlert'])->name('admin.alerts.resolve');



Route::get('/admin/fetch-alerts', [App\Http\Controllers\AdminController::class, 'fetchAlerts'])->name('admin.fetch-alerts');
Route::get('/admin/fetch-alerts', [AdminController::class, 'fetchAlerts'])->name('admin.fetch-alerts');

Route::post('/admin/mark-alerts-read', [AdminController::class, 'markAlertsRead'])->name('admin.mark-alerts-read');
Route::post('/admin/alerts/mark-read', [AdminController::class, 'markAlertRead'])->name('admin.mark-alerts-read');

// routes/web.php
Route::get('/responder/alerts/check', [ResponderController::class, 'checkAlerts'])->name('responder.alerts.check');

// Notify Responder about an emergency alert
Route::post('/admin/alerts/{alert}/notify', [App\Http\Controllers\AdminController::class, 'notifyResponder'])
    ->name('admin.alerts.notify');


// Login Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
