<?php

namespace App\Http\Controllers;

use App\Models\MeetingMinute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-meeting-minutes')->only(['index', 'show']);
        $this->middleware('can:manage-meeting-minutes')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $meetingQuery = MeetingMinute::with(['user', 'project'])
            ->visibleTo($user)
            ->latest('meeting_date')
            ->latest('id');

        $meetingQuery->when($request->filled('meeting_date'), function ($query) use ($request) {
            $query->whereDate('meeting_date', $request->date('meeting_date'));
        });

        $meetingQuery->when($request->filled('project_id'), function ($query) use ($request) {
            $query->where('project_id', $request->integer('project_id'));
        });

        $meetingQuery->when($request->filled('user_id') && $user->hasRole(['admin', 'manager']), function ($query) use ($request) {
            $query->where('user_id', $request->integer('user_id'));
        });

        $meetings = $meetingQuery->paginate(10)->withQueryString();

        $statsBase = MeetingMinute::query()->visibleTo($user);
        $todayMeetings = (clone $statsBase)->whereDate('meeting_date', today())->count();
        $weekMeetings = (clone $statsBase)->whereBetween('meeting_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count();
        $decisionMeetings = (clone $statsBase)->whereNotNull('decisions')->where('decisions', '!=', '')->count();
        $totalMeetings = (clone $statsBase)->count();

        $projects = Project::visibleTo($user)->orderBy('project_name')->get();
        $employees = $user->hasRole(['admin', 'manager'])
            ? User::employees()->orderBy('name')->get()
            : collect([$user]);

        $filters = $request->only(['meeting_date', 'project_id', 'user_id']);

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
        $projects = Project::visibleTo(Auth::user())->orderBy('project_name')->get();

        return view('meeting_minutes.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-meeting-minutes');

        $data = $request->validate([
            'meeting_date' => [
                'required',
                'date',
                Rule::unique('meeting_minutes', 'meeting_date')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'decisions' => 'nullable|string',
            'action_items' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        MeetingMinute::create([
            'user_id' => Auth::id(),
            'project_id' => $data['project_id'] ?? null,
            'meeting_date' => $data['meeting_date'],
            'title' => $data['title'],
            'attendees' => $data['attendees'],
            'discussion_points' => $data['discussion_points'],
            'decisions' => $data['decisions'] ?? null,
            'action_items' => $data['action_items'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting notes saved successfully');
    }

    public function show(MeetingMinute $meeting_minute)
    {
        Gate::authorize('view-meeting-minutes', $meeting_minute);

        $meeting_minute->load(['user', 'project']);

        return view('meeting_minutes.show', ['meeting' => $meeting_minute]);
    }

    public function edit(MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $projects = Project::visibleTo(Auth::user())->orderBy('project_name')->get();

        return view('meeting_minutes.edit', compact('meeting_minute', 'projects'));
    }

    public function update(Request $request, MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $data = $request->validate([
            'meeting_date' => [
                'required',
                'date',
                Rule::unique('meeting_minutes', 'meeting_date')
                    ->where(fn ($query) => $query->where('user_id', Auth::id()))
                    ->ignore($meeting_minute->id),
            ],
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'decisions' => 'nullable|string',
            'action_items' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        $meeting_minute->update([
            'project_id' => $data['project_id'] ?? null,
            'meeting_date' => $data['meeting_date'],
            'title' => $data['title'],
            'attendees' => $data['attendees'],
            'discussion_points' => $data['discussion_points'],
            'decisions' => $data['decisions'] ?? null,
            'action_items' => $data['action_items'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting notes updated successfully');
    }

    public function destroy(MeetingMinute $meeting_minute)
    {
        Gate::authorize('manage-meeting-minutes', $meeting_minute);

        $meeting_minute->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Meeting notes deleted successfully',
            ]);
        }

        return redirect()
            ->route('meeting-minutes.index')
            ->with('success', 'Meeting notes deleted successfully');
    }
}
