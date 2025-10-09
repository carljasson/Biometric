<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FingerprintController extends Controller
{
    /**
     * 📥 Save fingerprint from C# app (enrollment)
     * POST /api/save-fingerprint
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'fingerprint_template' => 'required|string', // base64 encoded
        ]);

        $user = User::find($validated['user_id']);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Decode base64 fingerprint data from C#
        $user->fingerprint_registered = 1;
        $user->fingerprint_template = base64_decode($validated['fingerprint_template']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '✅ Fingerprint saved successfully',
            'user_id' => $user->id,
        ]);
    }

    /**
     * 🔍 Match fingerprint for identification
     * POST /api/match-fingerprint
     */
    public function matchFingerprint(Request $request)
    {
        $validated = $request->validate([
            'fingerprint_template' => 'required|string',
        ]);

        $fingerprintBinary = base64_decode($validated['fingerprint_template']);
        $user = User::where('fingerprint_template', $fingerprintBinary)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No match found']);
    }
}
