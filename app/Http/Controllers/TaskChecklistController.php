<?php

namespace App\Http\Controllers;

use App\Models\TaskChecklist;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
{
    public function toggle(TaskChecklist $checklist)
    {
        $checklist->update([
            'is_completed' => !$checklist->is_completed,
        ]);

        return back()->with('success', 'Checklist updated successfully.');
    }
}