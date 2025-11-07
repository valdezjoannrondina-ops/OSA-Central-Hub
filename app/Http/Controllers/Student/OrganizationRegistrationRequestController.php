<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrganizationRegistrationRequest;
use Illuminate\Support\Facades\Auth;

class OrganizationRegistrationRequestController extends Controller
{
    // Show all requests for assistant-staff approval
    public function index()
    {
        $requests = OrganizationRegistrationRequest::with('student', 'organization')->where('status', 'pending')->get();
        return view('assistant.organization-requests', compact('requests'));
    }

    // Store a new organization registration request
    public function store(Request $request)
    {
        $user = Auth::user();
        // Only allow students (role 1)
        if ($user->role !== 1) {
            abort(403);
        }
        // Check if student already has 3 approved non-academic organizations
        // Count approved organization registration requests (where status = 'approved')
        // Only count non-academic organizations (those without department_id)
        $approvedRequests = OrganizationRegistrationRequest::where('student_id', $user->id)
            ->where('status', 'approved')
            ->with('organization')
            ->get();
        
        $approvedCount = $approvedRequests->filter(function ($req) {
            return $req->organization && !$req->organization->department_id;
        })->count();
        
        if ($approvedCount >= 3) {
            return back()->with('error', 'You have reached the limit of 3 non-academic organizations.');
        }
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'details' => 'nullable|string|max:1000',
        ]);
        
        // Ensure the organization is non-academic (no department_id)
        // Students are automatically members of their department's organization
        $organization = \App\Models\Organization::findOrFail($request->organization_id);
        if ($organization->department_id) {
            return back()->with('error', 'You cannot apply for a department-related organization. You are automatically a member of your department\'s organization.')->withInput();
        }
        
        // Prevent duplicate pending requests
        $exists = OrganizationRegistrationRequest::where('student_id', $user->id)
            ->where('organization_id', $request->organization_id)
            ->where('status', 'pending')
            ->exists();
        if ($exists) {
            return back()->with('error', 'You already have a pending request for this organization.');
        }
        OrganizationRegistrationRequest::create([
            'student_id' => $user->id,
            'organization_id' => $request->organization_id,
            'status' => 'pending',
            'details' => $request->details ?? null,
        ]);
        return back()->with('success', 'Organization registration request submitted.');
    }

    // Assistant-staff approves a request
    public function approve($id)
    {
        $request = OrganizationRegistrationRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();
        // Attach organization to student via pivot table (many-to-many relationship)
        // Check if already attached to avoid duplicates
        if (!$request->student->otherOrganizations()->where('organizations.id', $request->organization_id)->exists()) {
            $request->student->otherOrganizations()->attach($request->organization_id);
        }
        return back()->with('success', 'Request approved.');
    }

    // Assistant-staff declines a request
    public function decline($id)
    {
        $request = OrganizationRegistrationRequest::findOrFail($id);
        $request->status = 'declined';
        $request->save();
        return back()->with('success', 'Request declined.');
    }
}
