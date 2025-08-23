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
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    // Save to DB
    EmergencyAlert::create([
        'user_id' => auth()->id(),
        'type' => $request->type,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status' => 'Active',
    ]);

    return redirect()->back()->with('success', 'Emergency alert sent!');
}

}
