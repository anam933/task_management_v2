<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PipelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-task-board')->only(['kanbanBoard']);
    }

    public function kanbanBoard()
    {
        $selectedCategory = $this->currentCategoryId();

        $tasks = Task::with(['assignedUser', 'assignedByUser', 'project', 'tags', 'checklists'])
            ->visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->get();
        return view('kanban_board', compact('tasks'));
    }

    public function updateStatus(Request $request)
    {
        $task = Task::findOrFail($request->task_id);
        Gate::authorize('update-task-status', $task);

        $request->validate([
            'status' => 'required|in:Pending,In Progress,Submitted,Completed',
        ]);

        $status = $request->status;

        if ($status === 'Completed' && $task->reports_to && !Auth::user()->hasRole('admin') && Auth::id() !== (int) $task->reports_to) {
            $status = 'Submitted';
        }

        $task->status = $status;
        $task->save();

        return response()->json(['success' => true]);
    }

    public function taskDetails($id)
    {
        $task = Task::with(['project.teamMembers', 'assignedUser', 'assignedByUser', 'tags', 'attachments'])->findOrFail($id);
        Gate::authorize('view-tasks', $task);

        return view('task_details', compact('task'));
    }
}
