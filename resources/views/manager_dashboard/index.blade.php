@extends('adminlte::page')

@section('title', 'Manager Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Manager Dashboard</h1>
            <p class="text-muted mb-0">Tasks you are managing.</p>
        </div>
    </div>
@stop

@section('content')

@include('projects.partials.toasts')

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalTasks }}</h3>
                <p>Total Tasks</p>
            </div>
            <div class="icon">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingTasks }}</h3>
                <p>Pending</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $submittedTasks }}</h3>
                <p>Submitted for Review</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-import"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $completedTasks }}</h3>
                <p>Completed</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $createdMomCount }}</h3>
                <p>MOMs Created</p>
            </div>
            <div class="icon">
                <i class="fas fa-handshake"></i>
            </div>
            <a href="{{ route('meeting-minutes.index') }}" class="small-box-footer">
                View MOMs <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner p-3">
                <h3 class="text-dark">{{ $pendingActionItemsCount }}</h3>
                <p class="text-dark font-weight-bold">Pending Action Items</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list text-dark opacity-25"></i>
            </div>
            <a href="{{ route('meeting-minutes.index') }}" class="small-box-footer text-dark">
                View Actions <i class="fas fa-arrow-circle-right text-dark"></i>
            </a>
        </div>
    </div>
</div>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Tasks to Review & Manage</h3>
        <span class="badge badge-light">{{ $tasks->count() }} records</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $task->task_name }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($task->task_details, 60) }}</small>
                            </td>
                            <td>
                                <div>{{ optional($task->assignedUser)->name ?? 'Unassigned' }}</div>
                            </td>
                            <td>
                                <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</div>
                                <div><strong>Due:</strong> {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}</div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($task->status) {
                                        'Completed' => 'badge-success',
                                        'In Progress' => 'badge-info',
                                        'Submitted' => 'badge-primary',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} px-3 py-2">{{ $task->status }}</span>
                                
                                @if(\Carbon\Carbon::parse($task->deadline_date)->isPast() && !in_array($task->status, ['Completed', 'Submitted']))
                                    <span class="badge badge-danger px-2 py-2 mt-1 d-block"><i class="fas fa-exclamation-triangle"></i> Overdue</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No Tasks Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
