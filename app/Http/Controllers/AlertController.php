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
        $userId = auth()->check() ? auth()->id() : null;

        $alert = Alert::create([
            'user_id'     => $userId,
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
