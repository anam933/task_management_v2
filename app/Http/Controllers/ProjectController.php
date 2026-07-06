<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\ProjectCategory;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-projects')->only(['index', 'show']);
        $this->middleware('can:manage-projects')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'status', 'priority']);

        $selectedCategory = $this->currentCategoryId();

        $projectsQuery = Project::with(['manager', 'teamMembers'])
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'Completed');
            }])
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->latest();

        $projectsQuery->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->string('search')->trim()->toString();

            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('project_name', 'like', "%{$search}%")
                    ->orWhere('project_code', 'like', "%{$search}%")
                    ->orWhere('project_description', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($managerQuery) use ($search) {
                        $managerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        });

        $projectsQuery->when($request->filled('status'), function ($query) use ($request) {
            $query->where('project_status', $request->string('status')->toString());
        });

        $projectsQuery->when($request->filled('priority'), function ($query) use ($request) {
            $query->where('priority', $request->string('priority')->toString());
        });

        $projects = $projectsQuery->paginate(9)->withQueryString();

        $statsBase = Project::query()
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory));

        $stats = [
            'totalProjects' => (clone $statsBase)->count(),
            'activeProjects' => (clone $statsBase)->where('project_status', 'Active')->count(),
            'completedProjects' => (clone $statsBase)->where('project_status', 'Completed')->count(),
            'onHoldProjects' => (clone $statsBase)->where('project_status', 'On Hold')->count(),
        ];

        return view('projects.index', array_merge(compact('projects', 'filters'), $stats));
    }

    public function create()
    {
        $user = Auth::user();
        $selectedCategory = $this->currentCategoryId();
        if ($user->hasRole('admin') && !$selectedCategory) {
    $users = User::employees()->orderBy('name')->get();
} else {
    $users = User::employees()
        ->where('category_id', $selectedCategory)
        ->orderBy('name')
        ->get();
}
        $categories = $user->hasRole('admin')
            ? ProjectCategory::orderBy('category_name')->get()
            : ProjectCategory::whereKey($user->category_id)->orderBy('category_name')->get();

        return view('projects.create', compact('users', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);
        $project = null;

        DB::transaction(function () use ($data, $request, &$project) {
            $project = Project::create([
                'project_name' => $data['project_name'],
                'project_code' => $data['project_code'],
                'project_description' => $data['project_description'] ?? null,
                 'category_id' => session('current_category_id'),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'project_manager_id' => $data['project_manager_id'],
                'project_status' => $data['project_status'],
                'priority' => $data['priority'],
                'budget' => $data['budget'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ]);

            $project->teamMembers()->sync($data['team_members'] ?? []);

            $this->logActivity(
                project: $project,
                event: 'created',
                title: 'Project Created',
                description: 'Project "' . $project->project_name . '" was created.',
                metadata: ['status' => $project->project_status, 'priority' => $project->priority]
            );
        });

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view-projects', $project);

        $project->load([
            'manager',
            'creator',
            'teamMembers',
            'tasks' => function ($query) {
                $query->latest();
            },
            'tasks.assignedUser',
            'tasks.assignedByUser',
            'tasks.category',
            'activityLogs' => function ($query) {
                $query->latest();
            },
            'activityLogs.user',
        ]);

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'Completed')->count();
        $progress = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        return view('projects.show', compact('project', 'totalTasks', 'completedTasks', 'progress'));
    }

    public function edit(Project $project)
    {
        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($project->id)->exists(),
            403
        );

       if ($user->hasRole('admin') && !$selectedCategory) {
    $users = User::employees()->orderBy('name')->get();
} else {
    $users = User::employees()
        ->where('category_id', $selectedCategory)
        ->orderBy('name')
        ->get();
}
        $categories = Auth::user()->hasRole('admin')
            ? ProjectCategory::orderBy('category_name')->get()
            : ProjectCategory::whereKey(Auth::user()->category_id)->orderBy('category_name')->get();

        $project->load('teamMembers');

        return view('projects.edit', compact('project', 'users', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($project->id)->exists(),
            403
        );
        $data = $this->validateProject($request, $project->id);
        $originalStatus = $project->project_status;

        DB::transaction(function () use ($project, $data, $originalStatus) {
            $project->update([
                'project_name' => $data['project_name'],
                'project_code' => $data['project_code'],
                'project_description' => $data['project_description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'project_manager_id' => $data['project_manager_id'],
                'project_status' => $data['project_status'],
                'priority' => $data['priority'],
                'budget' => $data['budget'] ?? null,
            ]);

            $project->teamMembers()->sync($data['team_members'] ?? []);

            $this->logActivity(
                project: $project,
                event: 'updated',
                title: 'Project Updated',
                description: 'Project "' . $project->project_name . '" was updated.',
                metadata: ['status' => $project->project_status, 'priority' => $project->priority]
            );

            if ($originalStatus !== $project->project_status) {
                $this->logActivity(
                    project: $project,
                    event: 'status_changed',
                    title: 'Project Status Changed',
                    description: 'Project status changed from ' . $originalStatus . ' to ' . $project->project_status . '.',
                    metadata: ['from' => $originalStatus, 'to' => $project->project_status]
                );
            }
        });

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        abort_unless(
            Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($project->id)->exists(),
            403
        );
        DB::transaction(function () use ($project) {
            $this->logActivity(
                project: $project,
                event: 'deleted',
                title: 'Project Deleted',
                description: 'Project "' . $project->project_name . '" was deleted.',
                metadata: ['status' => $project->project_status, 'priority' => $project->priority]
            );

            $project->delete();
        });

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Project deleted successfully.',
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function validateProject(Request $request, ?int $projectId = null)
    {
        return $request->validate([
            'project_name' => 'required|string|max:255',
            'project_code' => [
                'required',
                'string',
                Rule::unique('projects', 'project_code')->ignore($projectId),
            ],
            'project_description' => 'nullable|string',

            

            'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',

        'project_manager_id' => 'required|exists:users,id',

        'project_status' => 'required|string',
        'priority' => 'required|string',

        'budget' => 'nullable|numeric',

        'team_members' => 'nullable|array',
    ]);
}

    private function logActivity(Project $project, string $event, string $title, string $description, array $metadata = []): void
    {
        ProjectActivityLog::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'event' => $event,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
