<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class BiometricRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'fingerprint' => 'required|string',
            'face_image' => 'required|string', // base64
        ]);

        // Decode base64 and save the image
        $faceImageData = base64_decode($request->face_image);
        $faceImageName = 'face_' . time() . '.png';
        Storage::put("public/faces/{$faceImageName}", $faceImageData);

        // Save to database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'fingerprint' => $request->fingerprint,
            'face_image_path' => "faces/{$faceImageName}", // You may add this column to users table
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user_id' => $user->id,
        ]);
    }
}
