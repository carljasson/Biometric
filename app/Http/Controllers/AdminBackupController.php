<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBackupController extends Controller
{
    // Show upload form and list of backups
    public function index()
    {
        // list files in storage/app/backups
        $files = Storage::files('backups');

        // get metadata
        $backups = collect($files)->map(function($path) {
            return [
                'path' => $path,
                'name' => basename($path),
                'size' => Storage::size($path),
                'modified' => date('Y-m-d H:i:s', Storage::lastModified($path)),
            ];
        })->sortByDesc('modified')->values();

        return view('admin.backups.index', compact('backups'));
    }

    // Handle upload
    public function store(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400', // 100MB, change as needed
        ]);

        $file = $request->file('backup_file');

        // optional: sanitize original filename and add timestamp
        $safeName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $filename = $safeName . '_' . now()->format('Ymd_His') . '.' . $ext;

        // store in local storage 'backups' folder
        $path = $file->storeAs('backups', $filename);

        return redirect()->route('admin.backups.index')->with('success', 'Backup uploaded: ' . $filename);
    }

    // Download file
    public function download($filename)
    {
        // disallow path traversal
        $filename = basename($filename);
        $path = 'backups/' . $filename;

        if (!Storage::exists($path)) {
            return redirect()->route('admin.backups.index')->withErrors('File not found.');
        }

        return Storage::download($path, $filename);
    }

    // Delete file
    public function destroy($filename)
    {
        $filename = basename($filename);
        $path = 'backups/' . $filename;

        if (!Storage::exists($path)) {
            return redirect()->route('admin.backups.index')->withErrors('File not found.');
        }

        Storage::delete($path);

        return redirect()->route('admin.backups.index')->with('success', 'Deleted: ' . $filename);
    }
}
