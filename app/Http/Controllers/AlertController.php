<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function sendAlert(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'destination' => 'required|string|in:Santa Fe,Madridejos,Bantayan',
            'photo' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
        ]);

        try {
            $user = Auth::user(); // 👈 get logged-in user

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // 👇 Save alert with both user_id and patient_id as same user
            $alert = Alert::create([
                'user_id'     => $user->id,
                'patient_id'  => $user->id,   // ✅ same as user_id
                'type'        => $request->type,
                'destination' => $request->destination,
                'photo'       => $request->photo,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'address'     => $request->address,
                'status'      => 'Pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => "🚨 Emergency alert sent to {$request->destination} responders!",
                'alert_id' => $alert->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send alert: ' . $e->getMessage(),
            ], 500);
        }
    }
}
