<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffOrganizationFile;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StaffOrganizationFileController extends Controller
{
    /**
     * Display files for a staff member's organization.
     */
    public function index($organizationId)
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($organizationId);

        // Verify the user has access to this organization
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);

        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }

        // Get files for this staff member and organization
        $files = StaffOrganizationFile::where('staff_id', $user->id)
            ->where('organization_id', $organizationId)
            ->with(['organization', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.organization-files.index', compact('organization', 'files'));
    }

    /**
     * Show the form for uploading a new file.
     */
    public function create($organizationId)
    {
        $user = auth()->user();
        $organization = Organization::findOrFail($organizationId);

        // Verify the user has access to this organization
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
        $user = auth()->user();
        $organization = Organization::findOrFail($organizationId);

        // Verify the user has access to this organization
        $hasAccess = $this->checkOrganizationAccess($user, $organizationId);

        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }

        $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'file_type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');

        // Sanitize filename
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

        // Create folder structure: staff/{staff_id}/organizations/{organization_id}/
        $folderPath = 'staff/' . $user->id . '/organizations/' . $organizationId;
        $filePath = $file->storeAs($folderPath, $filename, 'public');

        // Create database record
        $staffFile = StaffOrganizationFile::create([
            'staff_id' => $user->id,
            'organization_id' => $organizationId,
            'file_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $request->input('file_type', $this->detectFileType($file)),
            'description' => $request->input('description'),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $user->id,
        ]);

        Log::info('Staff organization file uploaded', [
            'staff_id' => $user->id,
            'organization_id' => $organizationId,
            'file_id' => $staffFile->id,
            'file_name' => $originalName,
        ]);

        return redirect()->route('staff.organization-files.index', $organizationId)
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Download a file.
     */
    public function download($organizationId, $fileId)
    {
        $user = auth()->user();
        $file = StaffOrganizationFile::findOrFail($fileId);

        // Verify the file belongs to this staff member and organization
        if ($file->staff_id !== $user->id || $file->organization_id != $organizationId) {
            abort(403, 'You do not have access to this file.');
        }

        // Verify file exists
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        // Security: verify path is within expected directory
        $realPath = realpath(Storage::disk('public')->path($file->file_path));
        $basePath = realpath(Storage::disk('public')->path('staff/' . $user->id . '/organizations/' . $organizationId));

        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404, 'Invalid file path.');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Delete a file.
     */
    public function destroy($organizationId, $fileId)
    {
        $user = auth()->user();
        $file = StaffOrganizationFile::findOrFail($fileId);

        // Verify the file belongs to this staff member and organization
        if ($file->staff_id !== $user->id || $file->organization_id != $organizationId) {
            abort(403, 'You do not have access to this file.');
        }

        // Delete physical file
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Delete database record
        $file->delete();

        Log::info('Staff organization file deleted', [
            'staff_id' => $user->id,
            'organization_id' => $organizationId,
            'file_id' => $fileId,
            'file_name' => $file->file_name,
        ]);

        return redirect()->route('staff.organization-files.index', $organizationId)
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Check if user has access to an organization.
     */
    private function checkOrganizationAccess($user, $organizationId)
    {
        // Admins have access to all organizations
        if ((int)($user->role ?? 0) === 4) {
            return true;
        }

        // Staff (role 2) - check if they are assigned to this organization
        if ((int)($user->role ?? 0) === 2) {
            // Check if user has organization_id
            if ($user->organization_id == $organizationId) {
                return true;
            }

            // Check many-to-many relationship
            if (method_exists($user, 'otherOrganizations')) {
                if ($user->otherOrganizations()->where('organizations.id', $organizationId)->exists()) {
                    return true;
                }
            }

            // Check Staff table for organization_id
            $staff = \App\Models\Staff::where('email', $user->email)->first();
            if ($staff) {
                if ($staff->organization_id == $organizationId) {
                    return true;
                }

                // Check many-to-many relationship in Staff model
                if (method_exists($staff, 'organizations')) {
                    if ($staff->organizations()->where('organizations.id', $organizationId)->exists()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Detect file type from file.
     */
    private function detectFileType($file)
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        // Personal Data Sheets are typically PDFs or images
        if (in_array($extension, ['pdf', 'doc', 'docx']) && 
            stripos($file->getClientOriginalName(), 'personal') !== false ||
            stripos($file->getClientOriginalName(), 'data sheet') !== false) {
            return 'personal_data_sheet';
        }

        // Detect by MIME type
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'application/pdf')) {
            return 'pdf';
        } elseif (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        } elseif (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return 'spreadsheet';
        }

        return 'other';
    }
}

