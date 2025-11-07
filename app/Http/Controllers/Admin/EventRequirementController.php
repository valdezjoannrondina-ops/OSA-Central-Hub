<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRequirement;
use App\Models\StatusChange;

class EventRequirementController extends Controller
{
    public function approve($id)
    {
        $requirement = EventRequirement::findOrFail($id);
        $this->authorize('approve', $requirement);
        $from = $requirement->approved ? 'approved' : 'pending';
        $requirement->update(['approved' => true]);
        StatusChange::create([
            'auditable_type' => EventRequirement::class,
            'auditable_id' => $requirement->id,
            'from_status' => $from,
            'to_status' => 'approved',
            'changed_by' => auth()->id(),
            'meta' => null,
        ]);
        return back()->with('success', 'Requirement approved.');
    }
}


