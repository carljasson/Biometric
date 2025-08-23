<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ApiRegisterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'lastname' => 'required|string',
            'birthday' => 'required|date',
            'age' => 'required|integer',
            'gender' => 'required|string|in:Male,Female',
            'status' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'contact_name' => 'required|string',
            'contact_number' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'face_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fingerprint_registered' => 'required|boolean',
        ]);

        $path = null;

        if ($request->hasFile('face_image')) {
            $path = $request->file('face_image')->store('faces', 'public');
        }

        $user = User::create([
            'firstname' => $validated['firstname'],
            'middlename' => $validated['middlename'] ?? null,
            'lastname' => $validated['lastname'],
            'birthday' => $validated['birthday'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'status' => $validated['status'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'contact_name' => $validated['contact_name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'face_image' => $path,
            'fingerprint_registered' => $validated['fingerprint_registered'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered.',
            'user_id' => $user->id,
        ], 201);
    }
}
