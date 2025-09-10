<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FingerprintController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string',
        ]);

        $user = Auth::user(); // logged-in user
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $user->fingerprints = $request->fingerprint;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint saved successfully',
            'user_id' => $user->id,
        ]);
    }
}
