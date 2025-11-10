<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintApiController;
use App\Http\Controllers\Api\BiometricRegistrationController;
use App\Models\User;

// ===== Fingerprint API Routes =====

// Save fingerprint data
Route::post('/save-fingerprint', [FingerprintController::class, 'store']);

// Match fingerprint for verification (used during login/scan)
Route::post('/match-fingerprint', [FingerprintController::class, 'matchFingerprint']);

// Alternate fingerprint match route (optional)
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);

// ✅ Add this — used by your C# app for fingerprint identification
Route::post('/identify-fingerprint', [FingerprintController::class, 'identify']);
