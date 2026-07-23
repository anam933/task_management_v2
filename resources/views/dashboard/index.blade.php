@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .dashboard-shell {
            font-family: 'Inter', sans-serif;
            padding: 1.5rem 0 3rem;
        }

        .dashboard-shell h1, .dashboard-shell h2, .dashboard-shell h3, .dashboard-shell h4, .dashboard-shell h5 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: #0f172a;
        }

        .dashboard-welcome {
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
            background: linear-gradient(120deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .dashboard-welcome::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .dashboard-welcome .card-body {
            padding: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .metric-grid {
            gap: 1.5rem 0;
            margin-bottom: 1rem;
        }

        .metric-card {
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
            min-height: 140px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 1;
            padding: 1.5rem;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .metric-card .metric-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .metric-card h3 {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 0;
            color: #1e293b;
            line-height: 1;
        }

        .metric-card .metric-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3.5rem;
            opacity: 0.1;
            transition: all 0.3s ease;
            z-index: -1;
        }

        .metric-card:hover .metric-icon {
            transform: translateY(-50%) scale(1.1);
            opacity: 0.2;
        }

        .metric-card::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 4px 0 0 4px;
        }

        .accent-info::after { background: #0ea5e9; }
        .accent-info .metric-icon { color: #0ea5e9; }

        .accent-success::after { background: #10b981; }
        .accent-success .metric-icon { color: #10b981; }

        .accent-warning::after { background: #f59e0b; }
        .accent-warning .metric-icon { color: #f59e0b; }

        .accent-danger::after { background: #ef4444; }
        .accent-danger .metric-icon { color: #ef4444; }

        .accent-primary::after { background: #6366f1; }
        .accent-primary .metric-icon { color: #6366f1; }

        .dashboard-card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .dashboard-card .card-header {
            border-bottom: 1px solid #f1f5f9;
            background: #ffffff;
            padding: 1.25rem 1.5rem;
        }

        .dashboard-card .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .dashboard-card .card-body {
            padding: 0; /* Let table handle padding */
        }
        
        .dashboard-card .card-body.padded-body {
            padding: 1.5rem;
        }

        .dashboard-card .table {
            margin-bottom: 0;
        }

        .dashboard-card .table thead th {
            border-bottom: 2px solid #f1f5f9;
            border-top: none;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
        }

        .dashboard-card .table tbody tr {
            transition: background-color 0.2s;
        }

        .dashboard-card .table tbody tr:hover {
            background: #f8fafc;
        }

        .dashboard-card .table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            border-top: none;
            font-size: 0.875rem;
        }

        .dashboard-card .table tbody tr:last-child td {
            border-bottom: none;
        }

        .dashboard-section-title {
            font-size: 1.125rem;
            color: #0f172a;
            font-weight: 700;
            margin: 2.5rem 0 1.5rem;
            display: flex;
            align-items: center;
        }

        .dashboard-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
            margin-left: 1.5rem;
        }

        .badge-pill {
            padding: 0.4em 0.8em;
            font-weight: 600;
            letter-spacing: 0.025em;
            border-radius: 9999px;
        }

        .badge.bg-primary { background: #6366f1 !important; color: #fff; }
        .badge.bg-danger { background: #ef4444 !important; color: #fff; }
        .badge.bg-warning { background: #f59e0b !important; color: #fff; }
        .badge.bg-info { background: #0ea5e9 !important; color: #fff; }
        .badge.bg-success { background: #10b981 !important; color: #fff; }

        /* Modal styling */
        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }
        .modal-header.bg-danger {
            background-color: #fef2f2 !important;
            border-bottom-color: #fee2e2;
            color: #ef4444 !important;
        }
        .modal-header.bg-danger .modal-title, .modal-header.bg-danger .close {
            color: #ef4444 !important;
            text-shadow: none;
            opacity: 1;
        }
        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .metric-card {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
        .metric-grid .col:nth-child(1) .metric-card { animation-delay: 0.05s; }
        .metric-grid .col:nth-child(2) .metric-card { animation-delay: 0.1s; }
        .metric-grid .col:nth-child(3) .metric-card { animation-delay: 0.15s; }
        .metric-grid .col:nth-child(4) .metric-card { animation-delay: 0.2s; }
        
        .content-header h1 {
            font-size: 1.75rem;
        }
        
        /* Layout overrides */
        .content-wrapper {
            background-color: #f8fafc !important;
        }

    </style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap pt-2 pb-2">
        <div>
            <h1 class="mb-1 text-dark font-weight-bold">Whiteforce Task Management Dashboard</h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Executive overview of users, tasks, and project delivery.</p>
        </div>
    </div>
@stop

@section('content')
<div class="dashboard-shell">
    @if($showSystemStats)
       <div class="row">
            <div class="col-12">
                <div class="card dashboard-welcome">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge badge-pill bg-primary mb-3">
                                    <i class="fas fa-sparkles mr-1"></i> System Insights
                                </span>

                                <h2 class="mb-2">Welcome to your workspace</h2>

                                <p class="text-muted mb-0" style="font-size: 1.05rem;">
                                    Track user growth, task throughput and project health at a glance.
                                </p>
                            </div>

                            <div class="mt-4 pt-2">
                                <small class="text-muted font-weight-bold">
                                    <i class="far fa-calendar-alt mr-1"></i> Updated {{ now()->format('M d, Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 metric-grid">
            <div class="col">
                <div class="metric-card accent-info">
                    <div class="metric-label">Total Users</div>
                    <h3>{{ $totalUsers }}</h3>
                    <div class="metric-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-success">
                    <div class="metric-label">Task Categories</div>
                    <h3>{{ $totalCategories }}</h3>
                    <div class="metric-icon"><i class="fas fa-tags"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-warning">
                    <div class="metric-label">Total Tasks</div>
                    <h3>{{ $totalTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-danger">
                    <div class="metric-label">Pending Tasks</div>
                    <h3>{{ $pendingTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        <div class="dashboard-section-title">Project Overview</div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 metric-grid">
            <div class="col">
                <div class="metric-card accent-primary">
                    <div class="metric-label">Total Projects</div>
                    <h3>{{ $totalProjects }}</h3>
                    <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-success">
                    <div class="metric-label">Active Projects</div>
                    <h3>{{ $activeProjects }}</h3>
                    <div class="metric-icon"><i class="fas fa-play-circle"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-info">
                    <div class="metric-label">Completed Projects</div>
                    <h3>{{ $completedProjects }}</h3>
                    <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-warning">
                    <div class="metric-label">On Hold Projects</div>
                    <h3>{{ $onHoldProjects }}</h3>
                    <div class="metric-icon"><i class="fas fa-pause-circle"></i></div>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('admin'))
            <div class="dashboard-section-title">Meeting Minutes Overview</div>
            <div class="row row-cols-1 row-cols-md-2 metric-grid">
                <div class="col">
                    <div class="metric-card accent-primary">
                        <div class="metric-label">Total Meetings</div>
                        <h3>{{ $totalMeetings }}</h3>
                        <div class="metric-icon"><i class="fas fa-handshake"></i></div>
                    </div>
                </div>
                <div class="col">
                    <div class="metric-card accent-success">
                        <div class="metric-label">Upcoming Meetings</div>
                        <h3>{{ $upcomingMeetings }}</h3>
                        <div class="metric-icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card dashboard-card mt-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-user-friends mr-2 text-primary"></i> Recent Users</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless">
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
                                    <td><span class="text-muted font-weight-bold">#{{ $user->id }}</span></td>
                                    <td class="font-weight-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-pill bg-{{ $user->role == 'admin' ? 'primary' : 'info' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        No Users Found
                                    </td>
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
                <div class="metric-card accent-warning">
                    <div class="metric-label">My Tasks</div>
                    <h3>{{ $myTotalTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-danger">
                    <div class="metric-label">Pending</div>
                    <h3>{{ $myPendingTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-info">
                    <div class="metric-label">In Progress</div>
                    <h3>{{ $myInProgressTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-spinner"></i></div>
                </div>
            </div>
            <div class="col">
                <div class="metric-card accent-success">
                    <div class="metric-label">Completed</div>
                    <h3>{{ $myCompletedTasks }}</h3>
                    <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card mt-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt mr-2 text-primary"></i> Workspace Access</h3>
            </div>
            <div class="card-body padded-body">
                <div class="d-flex align-items-center">
                    <div class="mr-4">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-unlock-alt text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1 font-weight-bold text-dark">Your Access Level</h5>
                        <p class="mb-0 text-muted">Your role gives you access to tasks, the board, projects you belong to, and your profile.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($upcomingTasks->count())
<div class="modal fade" id="taskReminderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-bell mr-2"></i> Task Reminder
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 px-4">Task</th>
                                <th class="border-top-0">Assigned To</th>
                                <th class="border-top-0">Deadline</th>
                                <th class="border-top-0 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($upcomingTasks as $task)
                            <tr>
                                <td class="font-weight-bold px-4">{{ $task->task_name }}</td>
                                <td>
                                    @if(optional($task->assignedUser)->name)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:28px;height:28px;font-size:12px;">
                                                {{ substr(optional($task->assignedUser)->name, 0, 1) }}
                                            </div>
                                            {{ optional($task->assignedUser)->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}
                                </td>
                                <td class="px-4">
                                    @if(\Carbon\Carbon::parse($task->deadline_date)->isPast())
                                        <span class="badge badge-pill badge-danger py-1 px-3">Overdue</span>
                                    @elseif(\Carbon\Carbon::parse($task->deadline_date)->isToday())
                                        <span class="badge badge-pill badge-warning py-1 px-3">Due Today</span>
                                    @else
                                        <span class="badge badge-pill badge-info py-1 px-3">Upcoming</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light font-weight-bold px-4" data-dismiss="modal">Close</button>
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
