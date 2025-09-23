<?php 


use App\Http\Controllers\Api\BiometricRegistrationController;

Route::post('/register', [BiometricRegistrationController::class, 'store']);
Route::post('/register', [ApiRegisterController::class, 'store']);
// api.php
Route::post('/fingerprint/save', function(Request $request) {
    cache(['latest_fingerprint' => $request->fingerprint], 10); // store 10 seconds
    return response()->json(['status' => 'ok']);
});

// routes/api.php
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);
