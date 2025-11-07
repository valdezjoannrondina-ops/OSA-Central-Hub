<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index($id)
    {
        // List files for event $id
        // $files = ...
        return view('assistant.files.index');
    }

    public function upload(Request $request, $id)
    {
        // Handle file upload for event $id
        // ...
        return back()->with('success', 'File uploaded.');
    }

    public function download($id, $file)
    {
        // Sanitize filename to prevent path traversal
        $file = basename($file);
        
        $path = storage_path('app/events/' . $id . '/' . $file);
        
        // Verify file exists and is within the allowed directory
        if (!file_exists($path)) {
            abort(404);
        }
        
        // Additional security: verify path is within expected directory
        $realPath = realpath($path);
        $basePath = realpath(storage_path('app/events/' . $id));
        
        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404);
        }
        
        return response()->download($realPath, $file);
    }
}
