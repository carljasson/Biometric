<?php 
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintApiController;
use App\Http\Controllers\Api\BiometricRegistrationController;

Route::post('/register/web', [BiometricController::class, 'store']);
Route::post('/register/api', [ApiRegisterController::class, 'store']);

// api.php
Route::post('/save-fingerprint', [FingerprintController::class, 'store']);
Route::post('/match-fingerprint', [FingerprintController::class, 'matchFingerprint']);



// routes/api.php
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);
