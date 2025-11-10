<?php 
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintApiController;
use App\Http\Controllers\Api\BiometricRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;





// api.php
Route::post('/save-fingerprint', [FingerprintController::class, 'store']);
Route::post('/match-fingerprint', [FingerprintController::class, 'matchFingerprint']);



// routes/api.php
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);

Route::post('/identify-fingerprint', [FingerprintController::class, 'identifyFingerprint']);
