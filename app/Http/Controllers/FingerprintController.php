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

        // ✅ Keep as base64 (do NOT decode)
        $user->fingerprint_registered = 1;
        $user->fingerprint_template = $validated['fingerprint_template'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '✅ Fingerprint saved successfully',
            'user_id' => $user->id,
        ]);
    }

    /**
     * 🔍 Identify user by fingerprint
     * POST /api/identify-fingerprint
     */
    public function identify(Request $request)
    {
        $request->validate([
            'fingerprint_template' => 'required|string'
        ]);

        $inputTemplate = $request->fingerprint_template;

        // ✅ Compare base64 strings directly
        $user = User::where('fingerprint_template', $inputTemplate)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No match found'], 404);
        }

        return response()->json([
            'success' => true,
            'firstname' => $user->firstname,
            'middlename' => $user->middlename,
            'lastname' => $user->lastname,
            'age' => $user->age,
            'birthday' => $user->birthday,
            'contact_name' => $user->contact_name,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
        ]);
    }
    public function matchFingerprint(Request $request)
{
    $inputTemplate = $request->input('fingerprint_template');

    // 🧠 Convert Base64 → raw bytes
    $decoded = base64_decode($inputTemplate);

    // Example: compare with stored fingerprints
    $users = User::whereNotNull('fingerprint_template')->get();

    foreach ($users as $user) {
        if ($this->fingerprintsMatch($decoded, base64_decode($user->fingerprint_template))) {
            return response()->json(['exists' => true, 'user_id' => $user->id]);
        }
    }

    return response()->json(['exists' => false]);
}

private function fingerprintsMatch($template1, $template2)
{
    // 🔧 Simplified: In production, use zkfp matching algorithm or your stored match API
    // For now, assume identical data = match
    return $template1 === $template2;
}

public function allFingerprints()
{
    $users = User::whereNotNull('fingerprint_template')->get([
        'id',
        'firstname',
        'middlename',
        'lastname',
        'age',
        'birthday',
        'contact_name',
        'contact_number',
        'address',
        'status',
        'phone',
        'gender',
        'fingerprint_template'
    ]);

    // Replace nulls with empty strings
    $usersSafe = $users->map(function($u) {
        return [
            'id' => $u->id,
            'firstname' => $u->firstname ?? '',
            'middlename' => $u->middlename ?? '', // ✅ empty if null
            'lastname' => $u->lastname ?? '',
            'age' => $u->age ?? '',
            'birthday' => $u->birthday ?? '',
            'contact_name' => $u->contact_name ?? '',
            'contact_number' => $u->contact_number ?? '',
            'address' => $u->address ?? '',
            'status' => $u->status ?? '',
            'phone' => $u->phone ?? '',
            'gender' => $u->gender ?? '',
            'fingerprint_template' => $u->fingerprint_template ?? '',
        ];
    });

    return response()->json($usersSafe);
}


}
