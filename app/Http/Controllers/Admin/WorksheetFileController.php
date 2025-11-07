<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WorksheetFileController extends Controller
{
    public function saveUpdated(Request $request)
    {
        $user = auth()->user();
        $designation = $user?->designation
            ?? optional($user?->staffProfile)->designation
            ?? \App\Models\Staff::where('email', $user?->email)->value('designation')
            ?? '';
        if (strcasecmp($designation, 'Admission Services Officer') !== 0 && (int)($user?->role ?? 0) !== 4) {
            abort(403);
        }
        $rows = $request->input('rows', []);
        $timestamp = now()->format('Ymd_His');
        $filename = "updated_worksheet_{$timestamp}.json";
        $path = public_path('staff/sidebar/report/' . $filename);
        file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT));
        Log::info('Worksheet updated and saved: ' . $filename);
        return response()->json(['success' => true, 'filename' => $filename]);
    }
}
