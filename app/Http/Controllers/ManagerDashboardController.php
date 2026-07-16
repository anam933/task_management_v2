<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $selectedCategory = $this->currentCategoryId();
        
        $allTasksQuery = Task::with(['category', 'legacyCategory', 'assignedUser', 'assignedByUser', 'project', 'tags', 'reportingManager'])
            ->where('reports_to', $user->id)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->latest();

        $allTasks = $allTasksQuery->get();
        // Only show Submitted tasks for review in the dashboard list
        $tasks = $allTasks->where('status', 'Submitted'); 

        $totalTasks = $allTasks->count();
        $pendingTasks = $allTasks->where('status', 'Pending')->count();
        $inProgressTasks = $allTasks->where('status', 'In Progress')->count();
        $submittedTasks = $allTasks->where('status', 'Submitted')->count();
        $completedTasks = $allTasks->where('status', 'Completed')->count();

        $createdMomCount = \App\Models\MeetingMinute::where('created_by', $user->id)->count();

        $pendingActionItemsCount = \App\Models\MeetingAction::whereIn('status', ['Pending', 'In Progress'])
            ->where(function ($query) use ($user) {
                $query->whereHas('meetingMinute', function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhereHas('project', function ($projQ) use ($user) {
                          $projQ->where('project_manager_id', $user->id)
                                ->orWhere('created_by', $user->id);
                      });
                })->orWhereHas('assignee', function ($q) use ($user) {
                    $q->where('reports_to', $user->id);
                });
            })->count();

        return view('manager_dashboard.index', compact(
            'tasks',
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'submittedTasks',
            'completedTasks',
            'createdMomCount',
            'pendingActionItemsCount'
        ));
    }

    public function approve(Request $request, Task $task)
    {
        abort_unless($task->reports_to === Auth::id() || Auth::user()->hasRole('admin'), 403);
        
        $task->status = 'Completed';
        $task->review_comment = null; // Clear previous comments if approved
        $task->save();

        return redirect()->back()->with('success', 'Task Approved and marked as Completed.');
    }

    public function reject(Request $request, Task $task)
    {
        abort_unless($task->reports_to === Auth::id() || Auth::user()->hasRole('admin'), 403);

        $request->validate([
            'review_comment' => 'required|string|max:1000',
        ]);

        $task->status = 'In Progress';
        $task->review_comment = $request->review_comment;
        $task->save();

        return redirect()->back()->with('error', 'Task Rejected. Sent back to In Progress.');
    }

    public function reassign(Request $request, Task $task)
    {
        abort_unless($task->reports_to === Auth::id() || Auth::user()->hasRole('admin'), 403);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task->assigned_to = $request->assigned_to;
        $task->status = 'Pending'; // Or leave it as is, but usually it resets
        $task->save();

        return redirect()->back()->with('success', 'Task Reassigned Successfully.');
    }
}
