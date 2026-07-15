<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Tag;
use App\Models\TaskCategory;
use App\Models\Project;
use App\Models\TaskChecklist;

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
     

       $selectedCategory = $this->currentCategoryId();

        $projects = Project::visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();

        $selectedCategory = $this->currentCategoryId();

        $tasksQuery = Task::with(['category', 'legacyCategory', 'assignedUser', 'assignedByUser', 'project', 'tags','reportingManager'])
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
         $ReviewTasks = (clone $statsQuery)->where('status', 'Review')->count();
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
    $user = Auth::user();

    $selectedCategory = $this->currentCategoryId();

    // Project select hone ke baad AJAX se users aayenge
    $users = collect();


    $categories = TaskCategory::orderBy('category_name')->get();


    if ($user->hasRole('admin')) {

        $reportingManagers = User::where('role', 'manager')
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->where('category_id', $selectedCategory);
            })
            ->orderBy('name')
            ->get();

    } else {

        $reportingManagers = collect([$user]);

    }


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
        'selectedTagIds',
        'reportingManagers'
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
    'reports_to' => 'required|exists:users,id',

    'status' => 'required|in:Pending',

    'checklist_items' => 'nullable|array',
    'checklist_items.*' => 'nullable|string|max:150',
]);

       abort_unless(
    Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($request->project_id)->exists(),
    403
);

if ($request->filled('assigned_to')) {

    $project = Project::find($request->project_id);

    $isAssignedUser = in_array($request->assigned_to, [
        $project->assigned_to,
        $project->project_manager_id
    ]);

    abort_unless($isAssignedUser, 403);
}

        $task = Task::create([
            'task_name' => $request->task_name,
            'task_details' => $request->task_details,
            'start_date' => $request->start_date,
            'deadline_date' => $request->deadline_date,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => Auth::id(),
            'status' => 'Pending',
            'task_category_id' => $request->task_category_id,
            'project_id' => $request->project_id,
            'reports_to' => $request->reports_to,
        ]);


        if ($request->filled('checklist_items')) {

    foreach ($request->checklist_items as $item) {

        if (trim($item) === '') {
            continue;
        }

        TaskChecklist::create([
            'task_id' => $task->id,
            'checklist_item' => $item,
            'created_by' => Auth::id(),
        ]);
    }
}

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
        $users = $task->project
    ? $task->project->teamMembers()->orderBy('name')->get()
    : collect();
        $categories = TaskCategory::orderBy('category_name')->get();
        $tags = Tag::orderBy('name')->get();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();
        $selectedTagIds = $task->tags()->pluck('tags.id')->all();

        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $reportingManagers = User::where('role', 'manager')
                ->when($selectedCategory, function ($query) use ($selectedCategory) {
                    $query->where('category_id', $selectedCategory);
                })
                ->orderBy('name')
                ->get();
        } else {
            $reportingManagers = collect([$user]);
        }

        return view('tasks.edit', compact('task', 'users', 'categories', 'projects', 'tags', 'selectedTagIds', 'reportingManagers'));
    }

    public function show(Task $task)
    {
        $task->load(['assignedUser', 'assignedByUser', 'project', 'tags','checklists']);

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
           'status' => 'required|in:Pending,In Progress,Submitted,Completed',
           'reports_to' => 'required|exists:users,id',
           
           'attachments.*'=>'file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip|max:10240'
           
            
        ]);

        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($request->project_id)->exists(),
            403
        );

        $isMember = Project::find($request->project_id)
    ->teamMembers()
    ->where('users.id', $request->assigned_to)
    ->exists();



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
            'reports_to' => $request->reports_to,
            'attachments'=>$request->attachments,
            
    
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

   public function projectMembers(Project $project)
{
    $users = User::where(function ($query) use ($project) {

        $query->where('id', $project->assigned_to)
              ->orWhere('id', $project->project_manager_id);

    })
    ->select('id', 'name')
    ->orderBy('name')
    ->get();


    return response()->json($users);
}

    public function submitTask(Request $request, Task $task)
    {

        if ($task->status !== 'In Progress') {
                return back()->with('error', 'Only tasks that are In Progress can be submitted.');
            }
        abort_unless((int)$task->assigned_to === (int)Auth::id() || Auth::user()->hasRole('admin'), 403);

        $request->validate([
            'submission_remarks' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip,txt|max:10240',
        ]);

        $task->update([
    'status' => 'Submitted',
    'submission_remarks' => $request->submission_remarks,
    'review_comment' => null,
]);


        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('task_attachments', 'public');
                \App\Models\TaskAttachment::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Task submitted successfully and sent to the Reporting Manager for review.');
    }

        public function startTask(Task $task)
{
    abort_unless(
        (int)$task->assigned_to === (int)Auth::id() || Auth::user()->hasRole('admin'),
        403
    );

    if ($task->status !== 'Pending') {
        return back()->with('error', 'Only pending tasks can be started.');
    }

    $task->update([
        'status' => 'In Progress',
    ]);

    return back()->with('success', 'Task started successfully.');
}


public function approveTask(Task $task)
{
    abort_unless(
        (int)$task->reports_to === (int)Auth::id()
        || Auth::user()->hasRole('admin'),
        403
    );

    if ($task->status !== 'Submitted') {
        return back()->with('error', 'Only submitted tasks can be approved.');
    }

    $task->update([
        'status' => 'Completed',
        'review_comment' => null,
    ]);

    return back()->with('success', 'Task approved successfully.');
}



public function rejectTask(Request $request, Task $task)
{
    abort_unless(
        (int)$task->reports_to === (int)Auth::id()
        || Auth::user()->hasRole('admin'),
        403
    );

    if ($task->status !== 'Submitted') {
        return back()->with('error', 'Only submitted tasks can be rejected.');
    }

    $request->validate([
        'review_comment' => 'required|string|max:1000',
    ]);

    $task->update([
        'status' => 'In Progress',
        'review_comment' => $request->review_comment,
    ]);

    return back()->with(
        'success',
        'Task rejected and sent back to the employee.'
    );
}


        
}
