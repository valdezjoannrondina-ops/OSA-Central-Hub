<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OrganizationFileController extends Controller
{
    /**
     * Display a listing of files for an organization.
     */
    public function index($organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        $files = OrganizationFile::where('organization_id', $organizationId)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('staff.organization-files.index', compact('organization', 'files'));
    }

    /**
     * Show the form for uploading a new file.
     */
    public function create($organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        return view('staff.organization-files.create', compact('organization'));
    }

    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request, $organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls,csv,txt|max:20480', // 20MB max
            'file_type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        
        $file = $request->file('file');
        
        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $filename = time() . '_' . $filename; // Add timestamp to prevent conflicts
        
        // Store file in organization-specific folder
        $folderPath = 'organizations/' . $organizationId;
        $path = $file->storeAs($folderPath, $filename, 'public');
        
        // Get file info
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $fileType = $request->file_type ?? $this->detectFileType($mimeType, $file->getClientOriginalName());
        
        try {
            OrganizationFile::create([
                'organization_id' => $organizationId,
                'uploaded_by' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $fileType,
                'description' => $request->description,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
            ]);
            
            Log::info('Organization file uploaded', [
                'organization_id' => $organizationId,
                'file_name' => $file->getClientOriginalName(),
                'uploaded_by' => $user->id,
            ]);
            
            return redirect()->route('staff.organization-files.index', $organizationId)
                ->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            // Delete file if database insert fails
            Storage::disk('public')->delete($path);
            
            Log::error('Failed to upload organization file', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
            ]);
            
            return back()->withInput()
                ->with('error', 'Failed to upload file. Please try again.');
        }
    }

    /**
     * Download a file.
     */
    public function download($organizationId, $fileId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        $file = OrganizationFile::where('organization_id', $organizationId)
            ->findOrFail($fileId);
        
        $path = storage_path('app/public/' . $file->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
        
        // Security: verify path is within expected directory
        $realPath = realpath($path);
        $basePath = realpath(storage_path('app/public/organizations/' . $organizationId));
        
        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404, 'Invalid file path.');
        }
        
        Log::info('Organization file downloaded', [
            'organization_id' => $organizationId,
            'file_id' => $fileId,
            'downloaded_by' => $user->id,
        ]);
        
        return response()->download($realPath, $file->file_name);
    }

    /**
     * Remove the specified file.
     */
    public function destroy($organizationId, $fileId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        $file = OrganizationFile::where('organization_id', $organizationId)
            ->findOrFail($fileId);
        
        // Only allow deletion if user uploaded the file or is admin
        if ($file->uploaded_by !== $user->id && (int)$user->role !== 4) {
            abort(403, 'You can only delete files you uploaded.');
        }
        
        // Delete physical file
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        
        // Delete database record
        $file->delete();
        
        Log::info('Organization file deleted', [
            'organization_id' => $organizationId,
            'file_id' => $fileId,
            'deleted_by' => $user->id,
        ]);
        
        return redirect()->route('staff.organization-files.index', $organizationId)
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Check if user has access to the organization.
     */
    private function checkOrganizationAccess($user, $organizationId)
    {
        // Admins have access to all organizations
        if ((int)($user->role ?? 0) === 4) {
            return true;
        }
        
        // Check if user is staff and has access to this organization
        if ((int)($user->role ?? 0) === 2) {
            $staff = \App\Models\Staff::where('email', $user->email)->first();
            
            if ($staff) {
                // Check single organization
                if ($staff->organization_id == $organizationId) {
                    return true;
                }
                
                // Check many-to-many organizations
                if ($staff->organizations()->where('organizations.id', $organizationId)->exists()) {
                    return true;
                }
            }
            
            // Check user's direct organization
            if ($user->organization_id == $organizationId) {
                return true;
            }
            
            // Check user's other organizations
            if (method_exists($user, 'otherOrganizations')) {
                if ($user->otherOrganizations()->where('organizations.id', $organizationId)->exists()) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Detect file type based on MIME type and filename.
     */
    private function detectFileType($mimeType, $filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check for Personal Data Sheet indicators
        if (stripos($filename, 'personal') !== false || 
            stripos($filename, 'data sheet') !== false ||
            stripos($filename, 'pds') !== false) {
            return 'personal_data_sheet';
        }
        
        // Check by MIME type
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        
        if ($mimeType === 'application/pdf') {
            return 'document';
        }
        
        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ])) {
            return 'document';
        }
        
        if (in_array($mimeType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return 'spreadsheet';
        }
        
        return 'document';
    }
}

