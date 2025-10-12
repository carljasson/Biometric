<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Responder;               
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Alert;
use Illuminate\Support\Facades\Storage;
use App\Models\EmergencyAlert;
class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return view('admin.patients', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|numeric',
            'gender' => 'required',
            'condition' => 'required',
            'admit_date' => 'required|date',
            'room_number' => 'required'
        ]);

        Patient::create($request->all());
        return redirect()->route('patients.index')->with('add_success', 'Patient added successfully!');
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy($id)
    {
        Patient::destroy($id);
        return redirect()->route('patients.index')->with('delete_success', 'Patient deleted successfully!');
    }



public function sendAlert(Request $request)
    {
        // Validate input
        $request->validate([
            'type' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
        ]);

        // Save alert to database
        $alert = new Alert();
        $alert->user_id = Auth::id(); // assuming patient is logged in
        $alert->type = $request->type;
        $alert->photo = $request->photo;
        $alert->latitude = $request->latitude;
        $alert->longitude = $request->longitude;
        $alert->address = $request->address;
        $alert->status = 'pending';
        $alert->save();

        // Optionally send notification or broadcast
        // event(new AlertSent($alert));

        return redirect()->back()->with('success', '🚨 Alert sent successfully!');
    }
}




}