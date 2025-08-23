<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    // Show the medical records form for the patient
    public function create()
    {
        $patient = Auth::user();
        return view('patient.medical-records-form', compact('patient'));
    }

    // Store the submitted medical record for the patient
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blood_type' => 'required|string',
            'allergies' => 'nullable|array',
            'medications' => 'nullable|string',
            'surgeries' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'dose' => 'nullable|string',
            'reason_medication' => 'nullable|string',
            'surgery' => 'nullable|string',
        ]);

        $userId = auth()->user()->is_admin && $request->has('user_id') 
                  ? $request->input('user_id') 
                  : auth()->id();

        MedicalRecord::create([
            'user_id' => $userId,
            'blood_type' => $validated['blood_type'],
            'allergies' => isset($validated['allergies']) ? implode(', ', $validated['allergies']) : null,
            'medications' => $validated['medications'] ?? null,
            'surgeries' => $validated['surgeries'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'dose' => $validated['dose'] ?? 'N/A',
            'reason_medication' => $validated['reason_medication'] ?? null,
            'surgery' => $validated['surgery'] ?? 'N/A',
        ]);

        return redirect()->route('patient.medical_records.index')->with('success', 'Medical record created successfully!');
    }

    public function index()
    {
        if (Auth::user()->is_admin) {
            $patients = MedicalRecord::with('user')->paginate(10);
            return view('admin.medical-records', compact('patients'));
        } else {
            $patients = MedicalRecord::with('user')
                         ->where('user_id', Auth::id())
                         ->paginate(10);
            return view('patient.medical-records', compact('patients'));
        }
    }

    public function show($id)
    {
        $record = MedicalRecord::with('user')->findOrFail($id);

        if ($record->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        if (Auth::user()->is_admin) {
            return view('admin.medical-records-show', compact('record'));
        } else {
            return view('patient.medical-records-show', compact('record'));
        }
    }

    public function showMedicalRecord($id)
    {
        $patient = User::findOrFail($id);
        $record = $patient->medicalRecord;

        if ($record) {
            return view('patients.view-medical-record', compact('record', 'patient'));
        }

        return redirect()->route('medical.form', ['id' => $patient->id])
            ->with('info', 'No medical record found. Please create one.');
    }

    public function checkRecord($id)
    {
        $user = User::findOrFail($id);
        $record = $user->medicalRecord;

        if ($record) {
            return redirect()->route('patient.medical_records.show', $record->id);
        } else {
            return redirect()->route('patient.medical_records.create');
        }
    }
}
