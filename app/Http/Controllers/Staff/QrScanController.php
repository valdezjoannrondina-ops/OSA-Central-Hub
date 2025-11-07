<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    public function scan(Request $request)
{
    // Parse QR data: "ID:123|Name:Juan..."
    $qrData = $request->qr_data;
    // ... validate and mark attendance ...

    return response()->json([
        'success' => true,
        'message' => 'Attendance recorded successfully!'
    ]);
}
}

