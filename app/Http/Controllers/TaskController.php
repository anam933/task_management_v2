<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Tag;
use App\Models\TaskCategory;
use App\Models\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-tasks')->only(['index']);
        $this->middleware('can:manage-tasks')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();

        $projects = Project::visibleTo($user)->orderBy('project_name')->get();

        $selectedCategory = $this->currentCategoryId();

        $tasksQuery = Task::with(['category', 'legacyCategory', 'assignedUser', 'assignedByUser', 'project', 'tags'])
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->latest();

        if (request()->filled('project_id')) {
            $tasksQuery->where('project_id', request('project_id'));
        }

        $tasks = $tasksQuery->get();

        $statsQuery = Task::query()
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory));

        if (request()->filled('project_id')) {
            $statsQuery->where('project_id', request('project_id'));
        }

        $totalTasks = (clone $statsQuery)->count();
        $pendingTasks = (clone $statsQuery)->where('status', 'Pending')->count();
        $inProgressTasks = (clone $statsQuery)->where('status', 'In Progress')->count();
        $completedTasks = (clone $statsQuery)->where('status', 'Completed')->count();

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
        $selectedCategory = $this->currentCategoryId();

        $users = User::orderBy('name')->get();
        $categories = TaskCategory::orderBy('category_name')->get();
        $tags = Tag::orderBy('name')->get();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();
        $selectedTagIds = [];

    return view('tasks.create', compact(
        'users',
        'categories',
        'projects',
        'tags',
        'selectedTagIds'
    ));
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
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($request->project_id)->exists(),
            403
        );

        $task = Task::create([
            'task_name' => $request->task_name,
            'task_details' => $request->task_details,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => Auth::id(),
            'status' => $request->status,
            'task_category_id' => $request->task_category_id,
            'project_id' => $request->project_id,
        ]);

        $task->tags()->sync($request->input('tag_ids', []));

        return redirect('/tasks')
            ->with('success', 'Task Added Successfully');
}

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        abort_unless(
            Auth::user()->hasRole('admin') || Task::visibleTo(Auth::user())->whereKey($task->id)->exists(),
            403
        );

        $selectedCategory = $this->currentCategoryId();
        $users = User::orderBy('name')->get();
        $categories = TaskCategory::orderBy('category_name')->get();
        $tags = Tag::orderBy('name')->get();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();
        $selectedTagIds = $task->tags()->pluck('tags.id')->all();

        return view('tasks.edit', compact('task', 'users', 'categories', 'projects', 'tags', 'selectedTagIds'));
    }

    public function show(Task $task)
    {
        $task->load(['assignedUser', 'assignedByUser', 'project', 'tags']);

        abort_unless(
            Auth::user()->hasRole('admin') || Task::visibleTo(Auth::user())->whereKey($task->id)->exists(),
            403
        );
        return redirect()->route('task.details', $task->id);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        abort_unless(
            Auth::user()->hasRole('admin') || Task::visibleTo(Auth::user())->whereKey($task->id)->exists(),
            403
        );

        $request->validate([
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:Low,Medium,High',
            'task_category_id' => 'required|exists:task_categories,id',
            'project_id' => 'required|exists:projects,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|distinct|exists:tags,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($request->project_id)->exists(),
            403
        );

        $task->update([
            'task_name' => $request->task_name,
            'task_details' => $request->task_details,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => Auth::id(),
            'status' => $request->status,
            'task_category_id' => $request->task_category_id,
            'project_id' => $request->project_id,
        ]);

        $task->tags()->sync($request->input('tag_ids', []));

        return redirect('/tasks')->with('success', 'Task Updated');
    }

    public function destroy(Task $task)
    {
        abort_unless(
            Auth::user()->hasRole('admin') || Task::visibleTo(Auth::user())->whereKey($task->id)->exists(),
            403
        );
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Task Deleted Successfully',
            ]);
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task Deleted Successfully');
    }
        
}
