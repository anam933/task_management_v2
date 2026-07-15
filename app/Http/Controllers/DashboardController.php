<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-dashboard');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedCategory = $this->currentCategoryId();

        $categories = $user->hasRole('admin')
            ? ProjectCategory::orderBy('category_name')->get()
            : ProjectCategory::whereKey($user->category_id)->get();

        $showSystemStats = $user->hasRole(['admin', 'manager']);

        // Project Query
        $projectQuery = Project::query();

        if ($selectedCategory) {
            $projectQuery->where('category_id', $selectedCategory);
        }

        // Task Query
        $taskQuery = Task::query();

        if ($selectedCategory) {
            $taskQuery->whereHas('project', function ($q) use ($selectedCategory) {
                $q->where('category_id', $selectedCategory);
            });
        }

        if ($showSystemStats) {
           $userQuery = User::query();

if ($user->hasRole('manager')) {

    $userQuery->where(function ($q) use ($user) {
        $q->where('id', $user->id)
          ->orWhere('reports_to', $user->id);
    });

}

if ($selectedCategory) {
    $userQuery->where('category_id', $selectedCategory);
}
$users = $userQuery->latest()->get();
$totalUsers = (clone $userQuery)->count();
            $categoryQuery = TaskCategory::query();

            if ($selectedCategory) {
                $categoryQuery->whereHas('tasks', function ($q) use ($selectedCategory) {
                    $q->whereHas('project', function ($q) use ($selectedCategory) {
                        $q->where('category_id', $selectedCategory);
                    });
                });
            }

            $totalCategories = $categoryQuery->count();
            $totalTasks = (clone $taskQuery)->count();

            $pendingTasks = (clone $taskQuery)
                ->where('status', 'Pending')
                ->count();

            $totalProjects = (clone $projectQuery)->count();

            $activeProjects = (clone $projectQuery)
                ->where('project_status', 'Active')
                ->count();

            $completedProjects = (clone $projectQuery)
                ->where('project_status', 'Completed')
                ->count();

            $onHoldProjects = (clone $projectQuery)
                ->where('project_status', 'On Hold')
                ->count();

            $myTotalTasks = Task::where('assigned_to', $user->id)->count();

            $myPendingTasks = Task::where('assigned_to', $user->id)
                ->where('status', 'Pending')
                ->count();

            $myInProgressTasks = Task::where('assigned_to', $user->id)
                ->where('status', 'In Progress')
                ->count();

            $myCompletedTasks = Task::where('assigned_to', $user->id)
                ->where('status', 'Completed')
                ->count();

        } else {
            $users = collect();

           $userQuery = User::query();

if ($user->hasRole('admin')) {

    if ($selectedCategory) {
        $userQuery->where('category_id', $selectedCategory);
    }

} elseif ($user->hasRole('manager')) {

    $userQuery->where(function ($q) use ($user) {
        $q->where('id', $user->id)
          ->orWhere('reports_to', $user->id);
    });

} else {

    $userQuery->where('id', $user->id);

}

$users = $userQuery->latest()->get();
$totalUsers = (clone $userQuery)->count();

            $categoryQuery = TaskCategory::query();

            if ($selectedCategory) {
                $categoryQuery->whereHas('tasks', function ($q) use ($selectedCategory, $user) {
                    $q->visibleTo($user)->whereHas('project', function ($q) use ($selectedCategory) {
                        $q->where('category_id', $selectedCategory);
                    });
                });
            } else {
                $categoryQuery->whereHas('tasks', function ($q) use ($user) {
                    $q->visibleTo($user);
                });
            }

            $totalCategories = $categoryQuery->count();

            $taskQuery = Task::visibleTo($user);

            if ($selectedCategory) {
                $taskQuery->whereHas('project', function ($q) use ($selectedCategory) {
                    $q->where('category_id', $selectedCategory);
                });
            }

            $projectQuery = Project::visibleTo($user);

            if ($selectedCategory) {
                $projectQuery->where('category_id', $selectedCategory);
            }

            $totalTasks = (clone $taskQuery)->count();

            $pendingTasks = (clone $taskQuery)
                ->where('status', 'Pending')
                ->count();

            $totalProjects = (clone $projectQuery)->count();

            $activeProjects = 0;
            $completedProjects = 0;
            $onHoldProjects = 0;

            $myTotalTasks = $totalTasks;

            $myPendingTasks = $pendingTasks;

            $myInProgressTasks = (clone $taskQuery)
                ->where('status', 'In Progress')
                ->count();

            $myCompletedTasks = (clone $taskQuery)
                ->where('status', 'Completed')
                ->count();
        }


        $today = Carbon::today();

            $overdueTasks = Task::visibleTo($user)
                ->whereDate('deadline_date', '<', $today)
                ->whereNotIn('status', ['Completed'])
                ->count();

            $todayDeadlineTasks = Task::visibleTo($user)
                ->whereDate('deadline_date', $today)
                ->whereNotIn('status', ['Completed'])
                ->count();

                $upcomingTasks = Task::query()

    ->when($selectedCategory, function ($query) use ($selectedCategory) {

        $query->whereHas('project', function ($q) use ($selectedCategory) {

            $q->where('category_id', $selectedCategory);

        });

    })

    ->when($user->hasRole('employee'), function ($query) use ($user) {

        $query->where('assigned_to', $user->id);

    })

    ->when($user->hasRole('manager'), function ($query) use ($user) {

        $query->where(function ($q) use ($user) {

            $q->where('assigned_to', $user->id)
              ->orWhere('reports_to', $user->id);

        });

    })

    ->whereIn('status', ['Pending','In Progress'])

    ->whereDate('deadline_date','<=', now()->addDays(2))

    ->orderBy('deadline_date')

    ->take(5)

    ->get();




        
        return view('dashboard.index', compact(
            'users',
            'showSystemStats',
            'totalUsers',
            'totalCategories',
            'totalTasks',
            'pendingTasks',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'onHoldProjects',
            'myTotalTasks',
            'myPendingTasks',
            'myInProgressTasks',
            'myCompletedTasks',
            'categories',
            'selectedCategory',
            'overdueTasks',
            'todayDeadlineTasks',
            'upcomingTasks' 
        ));
    }
}