<?php 


use App\Http\Controllers\Api\BiometricRegistrationController;

Route::post('/register', [BiometricRegistrationController::class, 'store']);
Route::post('/register', [ApiRegisterController::class, 'store']);
Route::post('/fingerprint', [App\Http\Controllers\FingerprintController::class, 'store']);
