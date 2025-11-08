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
            'type'        => 'required|string',
            'destination' => 'required|string|in:Santa Fe,Madridejos,Bantayan',
            'photo'       => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'address'     => 'required',
        ]);

        try {
            $user = auth()->user();
            $photoPath = null;

            // 🖼️ Handle Base64 image data
            if ($request->photo && strpos($request->photo, 'data:image') === 0) {
                $data = explode(',', $request->photo);
                $photoData = base64_decode($data[1]);
                $filename = 'alert_' . time() . '.png';

                /**
                 * ✅ Hostinger-safe public save path
                 * We save to /public/alerts instead of storage (no symlink needed)
                 */
                $saveDir = public_path('alerts');
                if (!file_exists($saveDir)) {
                    mkdir($saveDir, 0775, true);
                }

                $fullPath = $saveDir . '/' . $filename;
                file_put_contents($fullPath, $photoData);

                // URL accessible directly from the web
                $photoPath = 'alerts/' . $filename;
            }

            // 🩺 Save alert record
            $alert = Alert::create([
                'user_id'     => $user->id,
                'patient_id'  => $user->id, // same person who sends it
                'type'        => $request->type,
                'destination' => $request->destination,
                'photo'       => $photoPath,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'address'     => $request->address,
                'status'      => 'Pending',
            ]);

            return response()->json([
                'success'  => true,
                'message'  => "🚨 Emergency alert sent to {$request->destination} responders!",
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
