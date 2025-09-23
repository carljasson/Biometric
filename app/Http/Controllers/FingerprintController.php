<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FingerprintController extends Controller
{
       // Register fingerprint for a specific user
    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        $user->fingerprint_registered = $request->fingerprint;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Fingerprint registered successfully.'
        ]);
    }
    public function latestScan()
{
    // Suppose your C# app saves last scanned fingerprint in cache
    $scannedFingerprint = cache('latest_fingerprint'); // example

    if (!$scannedFingerprint) {
        return response()->json(['status' => 'waiting']);
    }

    $user = User::where('fingerprint_registered', $scannedFingerprint)->first();

    if ($user) {
        return response()->json([
            'status' => 'success',
            'user' => [
                'firstname' => $user->firstname,
                'middlename' => $user->middlename,
                'lastname' => $user->lastname,
                'address' => $user->address,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'age' => $user->age,
                'contact_name' => $user->contact_name,
                'contact_number' => $user->contact_number,
                'status' => $user->status,
                'birthday' => $user->birthday,
            ]
        ]);
    }

    return response()->json(['status' => 'not_found']);
}

public function match(Request $request)
{
    $fingerprint = $request->fingerprint;

    // Find user where fingerprint matches the database
    $user = User::where('fingerprint_registered', $fingerprint)->first();

    if ($user) {
        return response()->json([
            'status' => 'success',
            'user' => [
                'firstname' => $user->firstname,
                'middlename' => $user->middlename,
                'lastname' => $user->lastname,
                'address' => $user->address,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'age' => $user->age,
                'contact_name' => $user->contact_name,
                'contact_number' => $user->contact_number,
                'status' => $user->status,
                'birthday' => $user->birthday,
            ]
        ]);
    } else {
        return response()->json(['status' => 'not_found']);
    }
}
public function matchFingerprint(Request $request)
{
    $fingerprint = $request->input('fingerprint');

    // Check against 'fingerprint_registered' column
    $user = User::where('fingerprint_registered', $fingerprint)->first();

    if ($user) {
        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    } else {
        return response()->json([
            'status' => 'fail'
        ]);
    }
}

}

