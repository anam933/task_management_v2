<?php

namespace App\Http\Controllers;

use App\Models\DailyStandupReport;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DailyStandupReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-standup-reports')->only(['index', 'show']);
        $this->middleware('can:manage-standup-reports')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedCategory = $this->currentCategoryId();

        $reportsQuery = DailyStandupReport::with(['user', 'project'])
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->latest('report_date')
            ->latest('id');

        $reportsQuery->when($request->filled('report_date'), function ($query) use ($request) {
            $query->whereDate('report_date', $request->date('report_date'));
        });

        $reportsQuery->when($request->filled('project_id'), function ($query) use ($request) {
            $query->where('project_id', $request->integer('project_id'));
        });

        $reportsQuery->when($request->filled('user_id') && $user->hasRole(['admin', 'manager']), function ($query) use ($request) {
            $query->where('user_id', $request->integer('user_id'));
        });

        $reports = $reportsQuery->paginate(10)->withQueryString();

        $statsBase = DailyStandupReport::query()
            ->visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory));

        $todayReports = (clone $statsBase)->whereDate('report_date', today())->count();
        $weekReports = (clone $statsBase)->whereBetween('report_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count();
        $blockedReports = (clone $statsBase)->whereNotNull('blockers')->where('blockers', '!=', '')->count();
        $totalReports = (clone $statsBase)->count();

        $projects = Project::visibleTo($user)
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();
        $employees = $user->hasRole(['admin', 'manager'])
            ? User::employees()->orderBy('name')->get()
            : collect([$user]);

        $filters = $request->only(['report_date', 'project_id', 'user_id']);

        return view('standup_reports.index', compact(
            'reports',
            'projects',
            'employees',
            'filters',
            'todayReports',
            'weekReports',
            'blockedReports',
            'totalReports'
        ));
    }

    public function create()
    {
        $selectedCategory = $this->currentCategoryId();
        $projects = Project::visibleTo(Auth::user())
            ->when($selectedCategory, fn ($query) => $query->currentCategory($selectedCategory))
            ->orderBy('project_name')
            ->get();

        return view('standup_reports.create', compact('projects'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-standup-reports');

        $data = $request->validate([
            'report_date' => [
                'required',
                'date',
                Rule::unique('daily_standup_reports', 'report_date')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'project_id' => 'nullable|exists:projects,id',
            'yesterday_work' => 'required|string',
            'today_plan' => 'required|string',
            'blockers' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        DailyStandupReport::create([
            'user_id' => Auth::id(),
            'project_id' => $data['project_id'] ?? null,
            'report_date' => $data['report_date'],
            'yesterday_work' => $data['yesterday_work'],
            'today_plan' => $data['today_plan'],
            'blockers' => $data['blockers'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('standup-reports.index')
            ->with('success', 'Standup report submitted successfully');
    }

    public function show(DailyStandupReport $standup_report)
    {
        Gate::authorize('view-standup-reports', $standup_report);

        $standup_report->load(['user', 'project']);

        return view('standup_reports.show', [
            'report' => $standup_report,
        ]);
    }

    public function edit(DailyStandupReport $standup_report)
    {
        Gate::authorize('manage-standup-reports', $standup_report);

        $projects = Project::visibleTo(Auth::user())->orderBy('project_name')->get();

        return view('standup_reports.edit', [
            'report' => $standup_report,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, DailyStandupReport $standup_report)
    {
        Gate::authorize('manage-standup-reports', $standup_report);

        $data = $request->validate([
            'report_date' => [
                'required',
                'date',
                Rule::unique('daily_standup_reports', 'report_date')
                    ->where(fn ($query) => $query->where('user_id', Auth::id()))
                    ->ignore($standup_report->id),
            ],
            'project_id' => 'nullable|exists:projects,id',
            'yesterday_work' => 'required|string',
            'today_plan' => 'required|string',
            'blockers' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['project_id'])) {
            abort_unless(
                Auth::user()->hasRole('admin') || Project::visibleTo(Auth::user())->whereKey($data['project_id'])->exists(),
                403
            );
        }

        $standup_report->update([
            'project_id' => $data['project_id'] ?? null,
            'report_date' => $data['report_date'],
            'yesterday_work' => $data['yesterday_work'],
            'today_plan' => $data['today_plan'],
            'blockers' => $data['blockers'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('standup-reports.index')
            ->with('success', 'Standup report updated successfully');
    }

    public function destroy(DailyStandupReport $standup_report)
    {
        Gate::authorize('manage-standup-reports', $standup_report);

        $standup_report->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Standup report deleted successfully',
            ]);
        }

        return redirect()
            ->route('standup-reports.index')
            ->with('success', 'Standup report deleted successfully');
    }
}
