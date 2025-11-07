<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    public function scan(Request $request)
    {
        // Handle QR code scan from students
        // $qrData = $request->input('qr_data');
        // ...
        return response()->json(['success' => true, 'message' => 'QR scanned.']);
    }
}
