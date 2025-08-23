<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Alert;
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
    $request->validate([
        'type' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'address' => 'nullable|string',
    ]);

    $patientId = Auth::id(); // assuming the logged-in user is a patient

    Alert::create([
        'patient_id' => $patientId,
        'type' => $request->type,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'address' => $request->address,
    ]);

    return redirect()->back()->with('success', 'Emergency alert sent successfully!');
}
}
