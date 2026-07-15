@extends('adminlte::page')

@section('title', 'Task Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Task Management</h1>
            <p class="text-muted mb-0">Track task category, priority, assignment, and progress in one place.</p>
        </div>
        @can('manage-tasks')
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add New Task
        </a>
        @endcan
    </div>
@stop

@section('content')

@include('projects.partials.toasts')

<div class="card card-outline card-secondary shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row align-items-end">
            <div class="col-lg-5 mb-2">
                <label class="mb-1">Filter by Project</label>
                <select name="project_id" class="form-control">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }} ({{ $project->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 mb-2">
                <button class="btn btn-primary mr-2">Apply</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

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
                <h3>{{ $inProgressTasks }}</h3>
                <p>In Progress</p>
            </div>
            <div class="icon">
                <i class="fas fa-spinner"></i>
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

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">All Tasks</h3>
        <span class="badge badge-light">{{ $tasks->count() }} records</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Task</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Tags</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned By</th>
                        <th>Assigned To</th>
                        <th>reports_to</th> 
                        <th>Timeline</th>
                        @can('view-tasks')
                            <th style="width: 150px;">Actions</th>
                        @endcan
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
                                <span class="badge badge-light border px-3 py-2">
                                    {{ optional($task->project)->project_name ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $categoryName = optional($task->category)->category_name ?? optional($task->legacyCategory)->category_name ?? 'N/A';
                                @endphp
                                <span class="badge badge-primary px-3 py-2">{{ $categoryName }}</span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap" style="gap: 0.35rem;">
                                    @forelse($task->tags->take(3) as $tag)
                                        <span class="badge px-3 py-2" style="background-color: {{ $tag->color }}; color: #fff;">
                                            {{ $tag->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">No tags</span>
                                    @endforelse
                                    @if($task->tags->count() > 3)
                                        <span class="badge badge-light border px-3 py-2">+{{ $task->tags->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @php
                                    $priorityClass = match ($task->priority) {
                                        'High' => 'badge-danger',
                                        'Medium' => 'badge-warning',
                                        default => 'badge-success',
                                    };
                                @endphp
                                <span class="badge {{ $priorityClass }} px-3 py-2">{{ $task->priority }}</span>
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
                                    <span class="badge badge-danger px-2 py-2 mt-1 d-block" style="width: fit-content;"><i class="fas fa-exclamation-triangle"></i> Overdue</span>
                                @endif
                            </td>

                            <td>
                                    <div>{{ optional($task->assignedByUser)->name ?? 'System' }}</div>
                                </td>

                                <td>
                                    <div>{{ optional($task->assignedUser)->name ?? 'Unassigned' }}</div>
                                </td>

                                <td>
                                    <div>{{ optional($task->reportingManager)->name ?? '-' }}</div>
                                </td>

                                <td>
                                    <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</div>
                                    <div><strong>Due:</strong> {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}</div>
                                </td>

                            @can('view-tasks')
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @can('manage-tasks')
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
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

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('form[action*="tasks/"]').forEach((form) => {
            const deleteButton = form.querySelector('button[type="submit"]');
            if (!deleteButton) {
                return;
            }

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const result = await Swal.fire({
                    title: 'Delete task?',
                    text: 'This task will be removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
