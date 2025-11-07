<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffOrganizationFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * Display a listing of files for the current staff member.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get staff record by email
        $staff = \App\Models\Staff::where('email', $user->email)->first();
        
        // Get organizations for this staff member
        $organizations = collect();
        
        if ($staff) {
            if ($staff->organization_id) {
                $org = \App\Models\Organization::find($staff->organization_id);
                if ($org) {
                    $organizations->push($org);
                }
            }
            $additionalOrgs = $staff->organizations()->get();
            foreach ($additionalOrgs as $org) {
                if (!$organizations->contains('id', $org->id)) {
                    $organizations->push($org);
                }
            }
        }
        
        if ($user->organization_id) {
            $org = \App\Models\Organization::find($user->organization_id);
            if ($org && !$organizations->contains('id', $org->id)) {
                $organizations->push($org);
            }
        }
        
        // Get files for current staff member
        $query = StaffOrganizationFile::where('staff_id', $user->id)
            ->with(['organization', 'uploader'])
            ->orderBy('organization_id')
            ->orderBy('created_at', 'desc');
        
        // Filter by organization if provided
        if ($request->has('organization_id') && $request->organization_id) {
            $query->where('organization_id', $request->organization_id);
        }
        
        // Filter by file type if provided
        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }
        
        $allFiles = $query->get();
        
        // Group files by organization (organization_id is now required, so no need for 'general')
        $filesByOrganization = $allFiles->groupBy('organization_id');
        
        return view('staff.files.index', compact('filesByOrganization', 'allFiles', 'organizations'));
    }

    /**
     * Show the form for uploading a new file.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get staff record by email
        $staff = \App\Models\Staff::where('email', $user->email)->first();
        
        // Get organizations for this staff member
        $organizations = collect();
        
        if ($staff) {
            if ($staff->organization_id) {
                $org = \App\Models\Organization::find($staff->organization_id);
                if ($org) {
                    $organizations->push($org);
                }
            }
            $additionalOrgs = $staff->organizations()->get();
            foreach ($additionalOrgs as $org) {
                if (!$organizations->contains('id', $org->id)) {
                    $organizations->push($org);
                }
            }
        }
        
        if ($user->organization_id) {
            $org = \App\Models\Organization::find($user->organization_id);
            if ($org && !$organizations->contains('id', $org->id)) {
                $organizations->push($org);
            }
        }
        
        // Get organization_id from request if provided (for pre-selecting organization)
        $selectedOrganizationId = $request->query('organization_id');
        
        return view('staff.files.create', compact('organizations', 'selectedOrganizationId'));
    }

    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls,csv|max:20480', // 20MB
            'organization_id' => 'required|exists:organizations,id',
            'file_type' => 'nullable|string|max:100',
            'file_category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        
        $file = $request->file('file');
        
        // Sanitize filename
        $originalName = $file->getClientOriginalName();
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $filename = time() . '_' . $filename; // Add timestamp to prevent conflicts
        
        // Create storage path: staff/{staff_id}/organizations/{organization_id}/files/
        // Files are organized by organization to prevent mixing
        $organizationId = $request->organization_id;
        $storagePath = 'staff/' . $user->id . '/organizations/' . $organizationId . '/files';
        
        // Store file
        $path = $file->storeAs($storagePath, $filename, 'public');
        
        // Create database record
        $fileRecord = StaffOrganizationFile::create([
            'staff_id' => $user->id,
            'organization_id' => $organizationId, // Required - files must belong to an organization
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $request->file_type ?? 'other',
            'file_category' => $request->file_category ?? 'Other',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'uploaded_by' => $user->id,
        ]);
        
        Log::info('Staff organization file uploaded', [
            'file_id' => $fileRecord->id,
            'staff_id' => $user->id,
            'organization_id' => $request->organization_id,
            'filename' => $originalName,
        ]);
        
        return redirect()->route('staff.files.index')->with('success', 'File uploaded successfully.');
    }

    /**
     * Download a file.
     */
    public function download($id)
    {
        $user = auth()->user();
        $fileRecord = StaffOrganizationFile::findOrFail($id);
        
        // Verify ownership or admin access
        if ($fileRecord->staff_id != $user->id && (int)($user->role ?? 0) !== 4) {
            abort(403, 'You do not have permission to download this file.');
        }
        
        // Sanitize file path
        $filePath = storage_path('app/public/' . $fileRecord->file_path);
        
        // Verify file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }
        
        // Additional security: verify path is within expected directory
        $realPath = realpath($filePath);
        $basePath = realpath(storage_path('app/public/staff/' . $user->id));
        
        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404, 'File not found.');
        }
        
        return response()->download($realPath, $fileRecord->file_name);
    }

    /**
     * Delete a file.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $fileRecord = StaffOrganizationFile::findOrFail($id);
        
        // Verify ownership or admin access
        if ($fileRecord->staff_id != $user->id && (int)($user->role ?? 0) !== 4) {
            abort(403, 'You do not have permission to delete this file.');
        }
        
        // Delete physical file
        if (Storage::disk('public')->exists($fileRecord->file_path)) {
            Storage::disk('public')->delete($fileRecord->file_path);
        }
        
        // Delete database record
        $fileRecord->delete();
        
        Log::info('Staff organization file deleted', [
            'file_id' => $id,
            'staff_id' => $user->id,
        ]);
        
        return redirect()->route('staff.files.index')->with('success', 'File deleted successfully.');
    }
}

