@extends('adminlte::page')





@section('css')
    <style>
        .dashboard-shell {
            min-height: 80vh;
            padding: 1.5rem 0 3rem;
            background: transparent;
        }

        .dashboard-welcome {
            border-radius: 1.75rem;
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            background: #ffffff;
        }

        .dashboard-welcome .card-body {
            padding: 1.8rem 1.8rem 1.3rem;
        }

        .metric-grid {
            gap: 1.25rem;
        }

        .metric-card {
            border-radius: 1.6rem;
            position: relative;
            overflow: hidden;
            color: var(--text);
            min-height: 180px;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: var(--surface);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 52px rgba(15, 23, 42, 0.12);
        }

        .metric-card .metric-label {
            font-size: 0.81rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.85;
            margin-bottom: 0.75rem;
            color: #64748b;
        }

        .metric-card h3 {
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
            color: #0f172a;
        }

        .metric-card .metric-icon {
            position: absolute;
            right: 1.2rem;
            top: 1.2rem;
            font-size: 4.2rem;
            opacity: 0.12;
            color: var(--accent);
        }

        .metric-card.bg-info {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.18), rgba(59, 130, 246, 0.24));
        }

        .metric-card.bg-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.18), rgba(52, 211, 153, 0.24));
        }

        .metric-card.bg-warning {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(251, 146, 60, 0.24));
        }

        .metric-card.bg-danger {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.18), rgba(239, 68, 68, 0.24));
        }

        .metric-card.bg-primary {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(99, 102, 241, 0.24));
        }

        .dashboard-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            background: #ffffff;
        }

        .dashboard-card .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            background: transparent;
            padding-bottom: 0.9rem;
        }

        .dashboard-card .card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .dashboard-card .card-body {
            padding: 1.7rem 1.7rem 1.8rem;
        }

        .dashboard-card .table-responsive {
            border-radius: 1rem;
            overflow: hidden;
        }

        .dashboard-card .table thead th {
            border-bottom: 0;
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
        }

        .dashboard-card .table tbody tr:hover {
            background: #eff6ff;
        }

        .dashboard-card .table td,
        .dashboard-card .table th {
            vertical-align: middle;
            color: #475569;
        }

        .dashboard-section-title {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #94a3b8;
            font-weight: 700;
            margin: 2rem 0 1rem;
        }

        .filter-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            background: #ffffff;
        }

        .filter-card .form-label {
            font-weight: 700;
            color: #334155;
        }

        .filter-card .form-control {
            border-radius: 1rem;
            background: #f8fbff;
            color: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        @media (max-width: 767px) {
            .metric-card {
                min-height: 150px;
            }
        }
    </style>
@stop

@section('title', 'Dashboard')





@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Task Management Dashboard</h1>
            <p class="text-muted mb-0">Executive overview of users, tasks, and project delivery.</p>
        </div>
    </div>
@stop

@section('content')
<div class="dashboard-shell">
    @if($showSystemStats)
       <div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-welcome">
            <div class="card-body">
                <div class="d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge badge-pill badge-primary mb-3 px-3 py-2"
                            style="background:#6366f1;color:#fff;">
                            System Insights
                        </span>

                        <h2 class="mb-2">Welcome to your workspace</h2>

                        <p class="text-muted mb-0">
                            Track user growth, task throughput and project health at a glance.
                        </p>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            Updated {{ now()->format('M d, Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 metric-grid">
            <div class="col">
                <div class="metric-card bg-info">
                    <div class="p-4">
                        <div class="metric-label">Total Users</div>
                        <h3>{{ $totalUsers }}</h3>
                        <div class="metric-icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-success">
                    <div class="p-4">
                        <div class="metric-label">Task Categories</div>
                        <h3>{{ $totalCategories }}</h3>
                        <div class="metric-icon"><i class="fas fa-tags"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-warning">
                    <div class="p-4">
                        <div class="metric-label">Total Tasks</div>
                        <h3>{{ $totalTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-danger">
                    <div class="p-4">
                        <div class="metric-label">Pending Tasks</div>
                        <h3>{{ $pendingTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-section-title">Project Overview</div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 metric-grid">
            <div class="col">
                <div class="metric-card bg-primary">
                    <div class="p-4">
                        <div class="metric-label">Total Projects</div>
                        <h3>{{ $totalProjects }}</h3>
                        <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-success">
                    <div class="p-4">
                        <div class="metric-label">Active Projects</div>
                        <h3>{{ $activeProjects }}</h3>
                        <div class="metric-icon"><i class="fas fa-play-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-info">
                    <div class="p-4">
                        <div class="metric-label">Completed Projects</div>
                        <h3>{{ $completedProjects }}</h3>
                        <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-warning">
                    <div class="p-4">
                        <div class="metric-label">On Hold Projects</div>
                        <h3>{{ $onHoldProjects }}</h3>
                        <div class="metric-icon"><i class="fas fa-pause-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card mt-4">
            <div class="card-header">
                <h3 class="card-title mb-0">Recent Users</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
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
                                    <td>{{ ucfirst($user->role) }}</td>
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
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 metric-grid">
            <div class="col">
                <div class="metric-card bg-warning">
                    <div class="p-4">
                        <div class="metric-label">My Tasks</div>
                        <h3>{{ $myTotalTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-danger">
                    <div class="p-4">
                        <div class="metric-label">Pending</div>
                        <h3>{{ $myPendingTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-info">
                    <div class="p-4">
                        <div class="metric-label">In Progress</div>
                        <h3>{{ $myInProgressTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-spinner"></i></div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card bg-success">
                    <div class="p-4">
                        <div class="metric-label">Completed</div>
                        <h3>{{ $myCompletedTasks }}</h3>
                        <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card mt-4">
            <div class="card-header">
                <h3 class="card-title mb-0">Workspace Access</h3>
            </div>
            <div class="card-body">
                <p class="mb-0">Your role gives you access to tasks, the board, projects you belong to, and your profile.</p>
            </div>
        </div>
    @endif
</div>

@if($upcomingTasks->count())

<div class="modal fade" id="taskReminderModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-danger">

                <h5 class="modal-title text-white">
                    <i class="fas fa-bell"></i>
                    Task Reminder
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Deadline</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($upcomingTasks as $task)

                        <tr>

                            <td>{{ $task->task_name }}</td>

                            <td>{{ optional($task->assignedUser)->name }}</td>

                            <td>

                                {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}

                            </td>

                            <td>

                                @if(\Carbon\Carbon::parse($task->deadline_date)->isPast())

                                    <span class="badge badge-danger">
                                        Overdue
                                    </span>

                                @elseif(\Carbon\Carbon::parse($task->deadline_date)->isToday())

                                    <span class="badge badge-warning">
                                        Due Today
                                    </span>

                                @else

                                    <span class="badge badge-info">
                                        Upcoming
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        data-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

@endif
@stop

@section('js')


@if($upcomingTasks->count())

<script>
    $(document).ready(function () {
        $('#taskReminderModal').modal('show');
    });
</script>

@endif

@stop
