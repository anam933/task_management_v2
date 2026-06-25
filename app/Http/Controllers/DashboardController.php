<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
{
    $users = User::latest()->get();

    $totalUsers = User::count();
    $totalCategories = TaskCategory::count();
    $totalTasks = Task::count();
    $pendingTasks = Task::where('status', 'Pending')->count();

    $totalProjects = Project::count();
    $activeProjects = Project::where('project_status', 'Active')->count();
    $completedProjects = Project::where('project_status', 'Completed')->count();
    $onHoldProjects = Project::where('project_status', 'On Hold')->count();

    return view('dashboard.index', compact(
        'users',
        'totalUsers',
        'totalCategories',
        'totalTasks',
        'pendingTasks',
        'totalProjects',
        'activeProjects',
        'completedProjects',
        'onHoldProjects'
    ));
}
}
