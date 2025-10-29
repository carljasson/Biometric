<?php 
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintApiController;
use App\Http\Controllers\Api\BiometricRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;



Route::post('/register/web', [BiometricController::class, 'store']);
Route::post('/register/api', [ApiRegisterController::class, 'store']);

// api.php
Route::post('/save-fingerprint', [FingerprintController::class, 'store']);
Route::post('/match-fingerprint', [FingerprintController::class, 'matchFingerprint']);

Route::post('/identify-fingerprint', [FingerprintController::class, 'identify']);


// routes/api.php
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);
