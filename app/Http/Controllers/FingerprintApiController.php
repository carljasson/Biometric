<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FingerprintApiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'fingerprint_template' => 'required|string',
        ]);

        // Decode base64 fingerprint template
        $template = base64_decode($validated['fingerprint_template']);

        $user = User::find($validated['user_id']);
        $user->fingerprint_registered = 1;
        $user->fingerprint_template = $template;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Fingerprint saved!']);
    }
}
