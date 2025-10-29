<?php 
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintApiController;
use App\Http\Controllers\Api\BiometricRegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::post('/identify-fingerprint', function (Request $request) {
    $fingerprint = $request->input('fingerprint_template');

    // decode base64
    $binaryFingerprint = base64_decode($fingerprint);

    // ⚠️ Use ZK SDK or your stored fingerprint comparison function
    // For example, you can compare directly if you stored templates from zkfp2.DBMerge()
    // Here we assume you have stored the merged fingerprint as base64 in `fingerprint_template` column

    $users = User::whereNotNull('fingerprint_template')->get();

    foreach ($users as $user) {
        // Compare using your fingerprint library or an external script
        // If match found:
        // return user info
    }

    return response()->json(['error' => 'No match found'], 404);
});

Route::post('/register/web', [BiometricController::class, 'store']);
Route::post('/register/api', [ApiRegisterController::class, 'store']);

// api.php
Route::post('/save-fingerprint', [FingerprintController::class, 'store']);
Route::post('/match-fingerprint', [FingerprintController::class, 'matchFingerprint']);

Route::post('/identify-fingerprint', [FingerprintController::class, 'identify']);


// routes/api.php
Route::post('/fingerprint-match', [FingerprintController::class, 'match']);
