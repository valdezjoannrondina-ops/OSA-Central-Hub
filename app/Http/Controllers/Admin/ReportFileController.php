<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\Controller;

class ReportFileController extends Controller
{
    public function view($filename)
    {
        $filename = urldecode($filename);
        $path = public_path('staff/sidebar/report/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext === 'json') {
            $content = file_get_contents($path);
            $data = json_decode($content, true);
            return view('admin.staff.view-json', ['filename' => $filename, 'data' => $data]);
        } elseif ($ext === 'xlsx') {
            // Preview as HTML table using PhpSpreadsheet
            try {
                $spreadsheet = IOFactory::load($path);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = [];
                foreach ($sheet->toArray() as $row) {
                    $rows[] = $row;
                }
                return view('admin.staff.view-xlsx', ['filename' => $filename, 'rows' => $rows]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to preview Excel file.');
            }
        } else {
            abort(415);
        }
    }

    public function delete(Request $request, $filename)
    {
        $filename = urldecode($filename);
        $filePath = public_path('staff/sidebar/report/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('status', 'File deleted');
    }
}
