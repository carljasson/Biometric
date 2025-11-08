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
        $user = auth()->user(); // Can be null if not logged in
        $photoPath = null;

        // 🖼️ Handle Base64 image data
        if (strpos($request->photo, 'data:image') === 0) {
            $imageData = explode(',', $request->photo)[1];
            $image = base64_decode($imageData);
            $fileName = 'alert_' . time() . '.png';
            $filePath = storage_path('app/public/alerts/' . $fileName);

            // 🛠 Auto-create folder if missing
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0775, true);
            }

            file_put_contents($filePath, $image);
            $photoPath = 'storage/alerts/' . $fileName;
        }

        // ✅ Create the alert
        $alert = \App\Models\Alert::create([
            'user_id'     => $user ? $user->id : null, // ✅ allow null if guest
            'type'        => $request->type,
            'destination' => $request->destination,
            'photo'       => $photoPath,
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
