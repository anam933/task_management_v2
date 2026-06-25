@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Task Management Dashboard</h1>
            <p class="text-muted mb-0">Executive overview of users, tasks, and project delivery.</p>
        </div>
    </div>
@stop

@section('css')
    <style>
        .dashboard-shell {
            background: linear-gradient(180deg, rgba(2, 132, 199, 0.05), rgba(255,255,255,0));
        }
        .metric-box {
            border-radius: 18px;
            border: 0;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }
        .metric-box .inner h3 {
            font-size: 2rem;
            font-weight: 700;
        }
        .section-title {
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: 0.75rem;
        }
    </style>
@stop

@section('content')
<div class="dashboard-shell">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info metric-box">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success metric-box">
                <div class="inner">
                    <h3>{{ $totalCategories }}</h3>
                    <p>Task Categories</p>
                </div>
                <div class="icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning metric-box">
                <div class="inner">
                    <h3>{{ $totalTasks }}</h3>
                    <p>Total Tasks</p>
                </div>
                <div class="icon"><i class="fas fa-tasks"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger metric-box">
                <div class="inner">
                    <h3>{{ $pendingTasks }}</h3>
                    <p>Pending Tasks</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="section-title">Project Overview</div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary metric-box">
                <div class="inner">
                    <h3>{{ $totalProjects }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success metric-box">
                <div class="inner">
                    <h3>{{ $activeProjects }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon"><i class="fas fa-play-circle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info metric-box">
                <div class="inner">
                    <h3>{{ $completedProjects }}</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning metric-box">
                <div class="inner">
                    <h3>{{ $onHoldProjects }}</h3>
                    <p>On Hold Projects</p>
                </div>
                <div class="icon"><i class="fas fa-pause-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm mt-3">
        <div class="card-header">
            <h3 class="card-title mb-0">Recent Users</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->role }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No Users Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
