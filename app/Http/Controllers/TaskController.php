<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskCategory;
use App\Models\Project;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('project_name')->get();

        $tasksQuery = Task::with(['category', 'legacyCategory', 'assignedUser', 'project'])
            ->latest();

        if (request()->filled('project_id')) {
            $tasksQuery->where('project_id', request('project_id'));
        }

        $tasks = $tasksQuery->get();

        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();
        $completedTasks = Task::where('status', 'Completed')->count();

        return view('tasks.index', compact(
            'tasks',
            'projects',
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks'
        ));
    }

    public function create()
    {
        $users = User::all();
        $categories = TaskCategory::orderBy('category_name')->get();
        $projects = Project::orderBy('project_name')->get();

        return view('tasks.create', compact('users', 'categories', 'projects'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:Low,Medium,High',
            'task_category_id' => 'required|exists:task_categories,id',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        Task::create([
            'task_name' => $request->task_name,
            'task_details' => $request->task_details,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => 1,
            'status' => $request->status,
            'task_category_id' => $request->task_category_id,
            'project_id' => $request->project_id,
        ]);

        return redirect('/tasks')
            ->with('success', 'Task Added Successfully');
}

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        $categories = TaskCategory::orderBy('category_name')->get();
        $projects = Project::orderBy('project_name')->get();

        return view('tasks.edit', compact('task', 'users', 'categories', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:Low,Medium,High',
            'task_category_id' => 'required|exists:task_categories,id',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $task->update([
            'task_name' => $request->task_name,
            'task_details' => $request->task_details,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => 1,
            'status' => $request->status,
            'task_category_id' => $request->task_category_id,
            'project_id' => $request->project_id,
        ]);

        return redirect('/tasks')->with('success', 'Task Updated');
    }
        
}
