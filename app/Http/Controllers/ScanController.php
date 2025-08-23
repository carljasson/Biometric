<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    // Show scan page
    public function show()
    {
        return view('patient.scan');
    }

    // Handle submitted scan data
    public function submit(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string',
            'face' => 'required|string',
        ]);

        // Store or process data (for now just log)
        $fingerprint = $request->input('fingerprint');
        $faceImage = $request->input('face');

        // Save face image to storage (optional)
        $image = str_replace('data:image/png;base64,', '', $faceImage);
        $image = str_replace(' ', '+', $image);
        $imageName = 'face_' . time() . '.png';
        Storage::disk('public')->put("scans/{$imageName}", base64_decode($image));

        // Simulate saving fingerprint (optional)
        // You could save it in a DB, for now just return a view
        return back()->with('success', 'Scans successfully submitted.');
    }
}
