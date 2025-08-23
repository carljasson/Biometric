<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PatientsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new PatientsExport, 'medical_records.xlsx');
    }

    public function exportPDF()
    {
        $patients = Patient::all();
        $pdf = Pdf::loadView('admin.exports.pdf', compact('patients'));
        return $pdf->download('medical_records.pdf');
    }
}
