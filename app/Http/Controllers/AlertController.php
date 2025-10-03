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

        // Save alert to DB
        Alert::create([
            'patient_id'  => Auth::id(), // ⚠️ make sure this matches your patients table
            'type'        => $request->type,
            'destination' => $request->destination,
            'photo'       => $request->photo,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'address'     => $request->address,
            'status'      => 'Pending',
        ]);

        return back()->with('success', '🚨 Emergency alert sent to ' . $request->destination . ' responders!');
    }
}
