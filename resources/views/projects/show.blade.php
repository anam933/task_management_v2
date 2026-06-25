@extends('adminlte::page')

@section('title', $project->project_name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">{{ $project->project_name }}</h1>
            <p class="text-muted mb-0">{{ $project->project_code }}</p>
        </div>
        <div>
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning mr-2">
                <i class="fas fa-pen mr-1"></i> Edit
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
@stop

@section('content')
    @include('projects.partials.toasts')

    @php
        $statusClass = match ($project->project_status) {
            'Active' => 'badge-success',
            'On Hold' => 'badge-warning',
            'Completed' => 'badge-info',
            'Cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
        $priorityClass = match ($project->priority) {
            'High' => 'badge-danger',
            'Medium' => 'badge-warning',
            default => 'badge-success',
        };
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Project Overview</h3>
                    <div>
                        <span class="badge {{ $statusClass }} px-3 py-2">{{ $project->project_status }}</span>
                        <span class="badge {{ $priorityClass }} px-3 py-2 ml-1">{{ $project->priority }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">{{ $project->project_description ?: 'No project description provided.' }}</p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Project Manager</small>
                                <strong>{{ optional($project->manager)->name ?? 'Unassigned' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Start Date</small>
                                <strong>{{ optional($project->start_date)->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">End Date</small>
                                <strong>{{ optional($project->end_date)->format('d M Y') ?? 'Open' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Budget</small>
                                <strong>{{ $project->budget !== null ? number_format($project->budget, 2) : 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Created By</small>
                                <strong>{{ optional($project->creator)->name ?? 'System' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Project Code</small>
                                <strong>{{ $project->project_code }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Progress</strong>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-2">{{ $completedTasks }} of {{ $totalTasks }} tasks completed.</small>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Assigned Tasks</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Assignee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks as $task)
                                    <tr>
                                        <td>{{ $task->task_name }}</td>
                                        <td>{{ optional($task->category)->category_name ?? 'N/A' }}</td>
                                        <td>{{ $task->status }}</td>
                                        <td>{{ optional($task->assignedUser)->name ?? 'Unassigned' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No tasks assigned to this project.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Team Members</h3>
                </div>
                <div class="card-body">
                    @forelse($project->teamMembers as $member)
                        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                            <div>
                                <strong>{{ $member->name }}</strong>
                                <div class="text-muted small">{{ $member->email }}</div>
                            </div>
                            <span class="badge badge-light border">Member</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No team members assigned.</p>
                    @endforelse
                </div>
            </div>

            <div class="card card-outline card-dark shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Activity Log</h3>
                </div>
                <div class="card-body">
                    @forelse($project->activityLogs->take(8) as $log)
                        <div class="border-left pl-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $log->title }}</strong>
                                <small class="text-muted">{{ $log->created_at->format('d M H:i') }}</small>
                            </div>
                            <div class="text-muted small">{{ $log->description }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No activity logged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
