<?php

namespace App\Http\Controllers;

use App\Models\MeetingMinute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\MeetingChecklist;
use App\Models\TaskChecklist;

class MeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedCategory = $this->currentCategoryId();

        $meetingQuery = MeetingMinute::with(['user', 'project'])
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->latest('meeting_date')
            ->latest('id');

        // Apply filters
        $meetingQuery->when($request->filled('search'), function ($query) use ($request) {
            $query->where('meeting_title', 'like', '%' . $request->input('search') . '%');
        });

        $meetingQuery->when($request->filled('meeting_date'), function ($query) use ($request) {
            $query->whereDate('meeting_date', $request->date('meeting_date'));
        });

        $meetingQuery->when($request->filled('project_id'), function ($query) use ($request) {
            $query->where('project_id', $request->integer('project_id'));
        });

        $meetingQuery->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->input('status'));
        });

        $meetings = $meetingQuery->paginate(10)->withQueryString();

        // Statistics
        $statsBase = MeetingMinute::query()
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory));

        $totalMeetings = (clone $statsBase)->count();
        $todayMeetings = (clone $statsBase)->whereDate('meeting_date', today())->count();
        $decisionMeetings = (clone $statsBase)->whereNotNull('decisions')->where('decisions', '!=', '')->count();
        $weekMeetings = (clone $statsBase)->whereBetween('meeting_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count();

        $projects = Project::visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();

        $employees = $user->hasRole(['admin', 'manager'])
            ? User::employees()->orderBy('name')->get()
            : collect([$user]);

        $filters = $request->only(['search', 'meeting_date', 'project_id', 'status']);

        return view('meeting_minutes.index', compact(
            'meetings',
            'projects',
            'employees',
            'filters',
            'todayMeetings',
            'weekMeetings',
            'decisionMeetings',
            'totalMeetings'
        ));
    }

    public function create()
    {
        Gate::authorize('manage-meeting-minutes');

        $selectedCategory = $this->currentCategoryId();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();

        $users = collect();

        return view('meeting_minutes.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-meeting-minutes');

        $data = $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required|string',
            'meeting_type' => 'required|string|in:Online,Offline',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'discussion_points' => 'required|string',
            'decisions' => 'nullable|string',
            'action_items' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'required|string|in:Draft,Published,Completed',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'actions' => 'nullable|array',
            'actions.*.action_title' => 'required_with:actions.*.assigned_to|string|max:255',
            'actions.*.assigned_to' => 'required_with:actions.*.action_title|exists:users,id',
            'actions.*.deadline' => 'required_with:actions.*.action_title|date',
            'checklist_user_id' => 'nullable|exists:users,id',
            'completed_checklists' => 'nullable|array',
            'completed_checklists.*' => 'exists:task_checklists,id',

            'remarks' => 'nullable|array',
        ]);

        if (!empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        $meeting_minute = MeetingMinute::create([
            'meeting_title' => $data['meeting_title'],
            'meeting_date' => $data['meeting_date'],
            'meeting_time' => $data['meeting_time'],
            'meeting_type' => $data['meeting_type'],
            'location' => $data['location'] ?? null,
            'agenda' => $data['agenda'] ?? null,
            'discussion_points' => $data['discussion_points'],
            'decisions' => $data['decisions'] ?? null,
            'action_items' => $data['action_items'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'created_by' => Auth::id(),
            'status' => $data['status'],
        ]);

        if (!empty($data['participants'])) {
            $meeting_minute->participants()->sync($data['participants']);
        }

        if (!empty($data['actions'])) {
            foreach ($data['actions'] as $actionData) {
                if (!empty($actionData['action_title'])) {
                    $meeting_minute->actions()->create([
                        'action_title' => $actionData['action_title'],
                        'assigned_to' => $actionData['assigned_to'],
                        'deadline' => $actionData['deadline'],
                        'status' => 'Pending',
                    ]);
                }
            }
        }

        if ($meeting_minute && !empty($data['project_id']) && !empty($data['checklist_user_id'])) {
            $projectId = $data['project_id'];
            $checklistUserId = $data['checklist_user_id'];
            
            $allTaskChecklistIds = TaskChecklist::whereHas('task', function ($query) use ($projectId, $checklistUserId) {
                $query->where('project_id', $projectId)
                      ->where('assigned_to', $checklistUserId);
            })->pluck('id')->toArray();
            
            $completedChecklistIds = $data['completed_checklists'] ?? [];
            
            foreach ($allTaskChecklistIds as $checklistId) {
                $isCompleted = in_array($checklistId, $completedChecklistIds);
                
                TaskChecklist::where('id', $checklistId)->update(['is_completed' => $isCompleted]);
                
                $meeting_minute->checklistProgress()->create([
                    'task_checklist_id' => $checklistId,
                    'is_completed' => $isCompleted,
                ]);
            }
        }

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting minutes saved successfully.');
    }

    public function show(MeetingMinute $meeting_minute)
    {
        Gate::authorize('view-meeting-minutes', $meeting_minute);

        $meeting_minute->load(['user', 'project', 'participants', 'actions.assignee']);

        return view('meeting_minutes.show', ['meeting' => $meeting_minute]);
    }

    public function edit(MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $selectedCategory = $this->currentCategoryId();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();

        $users = User::orderBy('name')->get();
        $meeting_minute->load(['participants', 'actions']);

        return view('meeting_minutes.edit', compact('meeting_minute', 'projects', 'users'));
    }

    public function update(Request $request, MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $data = $request->validate([
            'meeting_title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required|string',
            'meeting_type' => 'required|string|in:Online,Offline',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'discussion_points' => 'required|string',
            'decisions' => 'nullable|string',
            'action_items' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'required|string|in:Draft,Published,Completed',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'actions' => 'nullable|array',
            'actions.*.action_title' => 'required_with:actions.*.assigned_to|string|max:255',
            'actions.*.assigned_to' => 'required_with:actions.*.action_title|exists:users,id',
            'actions.*.deadline' => 'required_with:actions.*.action_title|date',
            'actions.*.status' => 'required_with:actions.*.action_title|in:Pending,In Progress,Completed',
            'checklist_user_id' => 'nullable|exists:users,id',
            'completed_checklists' => 'nullable|array',
            'completed_checklists.*' => 'exists:task_checklists,id',
        ]);

        if (!empty($data['actions'])) {
            $existingActions = $meeting_minute->actions->keyBy(function ($action) {
                return $action->action_title . '_' . $action->assigned_to;
            });

            foreach ($data['actions'] as $index => $actionData) {
                if (!empty($actionData['action_title']) && $actionData['status'] === 'Completed') {
                    $assignedTo = (int)$actionData['assigned_to'];
                    $key = $actionData['action_title'] . '_' . $assignedTo;
                    $prevAction = $existingActions->get($key);

                    $ok = false;
                    if ($prevAction && $prevAction->status === 'Completed') {
                        $ok = true;
                    } elseif (Auth::id() === $assignedTo && $prevAction && $prevAction->status === 'In Progress') {
                        $ok = true;
                    }

                    if (!$ok) {
                        return back()->withErrors([
                            "actions.{$index}.status" => "Only the assigned employee can mark this action item as Completed after starting it."
                        ])->withInput();
                    }
                }
            }
        }

        if (!empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        $meeting_minute->update([
            'meeting_title' => $data['meeting_title'],
            'meeting_date' => $data['meeting_date'],
            'meeting_time' => $data['meeting_time'],
            'meeting_type' => $data['meeting_type'],
            'location' => $data['location'] ?? null,
            'agenda' => $data['agenda'] ?? null,
            'discussion_points' => $data['discussion_points'],
            'decisions' => $data['decisions'] ?? null,
            'action_items' => $data['action_items'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'status' => $data['status'],
        ]);

        $meeting_minute->participants()->sync($data['participants'] ?? []);

        // Recreate action items to handle modifications, additions, and deletions
        $meeting_minute->actions()->delete();
        if (!empty($data['actions'])) {
            foreach ($data['actions'] as $actionData) {
                if (!empty($actionData['action_title'])) {
                    $meeting_minute->actions()->create([
                        'action_title' => $actionData['action_title'],
                        'assigned_to' => $actionData['assigned_to'],
                        'deadline' => $actionData['deadline'],
                        'status' => $actionData['status'],
                    ]);
                }
            }
        }

        if (!empty($data['project_id']) && !empty($data['checklist_user_id'])) {
            $projectId = $data['project_id'];
            $checklistUserId = $data['checklist_user_id'];
            
            $allTaskChecklistIds = TaskChecklist::whereHas('task', function ($query) use ($projectId, $checklistUserId) {
                $query->where('project_id', $projectId)
                      ->where('assigned_to', $checklistUserId);
            })->pluck('id')->toArray();
            
            $meeting_minute->checklistProgress()->whereIn('task_checklist_id', $allTaskChecklistIds)->delete();
            
            $completedChecklistIds = $data['completed_checklists'] ?? [];
            
            foreach ($allTaskChecklistIds as $checklistId) {
                $isCompleted = in_array($checklistId, $completedChecklistIds);
                
                TaskChecklist::where('id', $checklistId)->update(['is_completed' => $isCompleted]);
                
                $meeting_minute->checklistProgress()->create([
                    'task_checklist_id' => $checklistId,
                    'is_completed' => $isCompleted,
                ]);
            }
        }

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting minutes updated successfully.');
    }

    public function destroy(MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $meeting_minute->delete();

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting minutes deleted successfully.');
    }

    public function getProjectUsers(Project $project)
    {
        $user = Auth::user();

        if (
            !$user->hasRole('admin') &&
            !Project::visibleTo($user)->whereKey($project->id)->exists()
        ) {
            abort(403);
        }

        $project->load([
            'manager',
            'assignedUser',
            'reportingManager',
            'teamMembers',
        ]);

        $users = $project->meetingUsers();

        return response()->json(
            $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            })->values()
        );
    }


    public function projectTaskChecklists(Request $request, Project $project)
{
    $userId = $request->query('user_id');

    $tasks = \App\Models\Task::where('project_id', $project->id)
        ->has('checklists')
        ->with('checklists')
        ->when($userId, function ($query) use ($userId) {
            $query->where('assigned_to', $userId);
        })
        ->get();

    return response()->json($tasks);
}
}
