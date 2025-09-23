<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
class RegisterController extends Controller
{
public function checkPhone(Request $request) {
    $request->validate(['phone' => 'required|digits:11']);
    $exists = User::where('phone', $request->phone)->exists();
    return response()->json(['exists' => $exists]);
}

public function checkEmail(Request $request) {
    $request->validate(['email' => 'required|email']);
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
}

public function storeStep1(Request $request) {
    $validated = $request->validate([
        'firstname' => 'required|string|max:50',
        'lastname' => 'required|string|max:50',
        'birthday' => 'required|date',
        'age' => 'required|integer',
        'gender' => 'required|string',
        'status' => 'required|string',
        'phone' => 'required|digits:11|unique:users,phone',
        'address' => 'required|string',
        'contact_name' => 'required|string',
        'contact_number' => 'required|digits:11',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|confirmed|min:8|max:16',
    ]);

    $user = User::create([
        'firstname' => $validated['firstname'],
        'middlename' => $request->middlename ?? null,
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
    ]);

    return response()->json(['success' => true]);
}
public function saveFingerprint(Request $request)
{
    $userId = auth()->id(); // or session()->get('user_id') depending on your flow
    $fingerprint = $request->input('fingerprint_data');

    if ($userId && $fingerprint) {
        $user = User::find($userId);
        if ($user) {
            $user->fingerprint_registered = $fingerprint;
            $user->save();

            return redirect('/register/step3')->with('success', 'Fingerprint saved successfully!');
        }
    }

    return redirect('/register/step3')->with('warning', 'Fingerprint skipped.');
}

}