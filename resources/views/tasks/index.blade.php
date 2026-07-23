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
            <div class="col-lg-4 mb-2">
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
            <div class="col-lg-4 mb-2">
                <label class="mb-1">Filter by Assigned Employee</label>
                <select name="assigned_to" class="form-control" disabled>
                    <option value="">All Employees</option>
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <button class="btn btn-primary mr-2">Apply</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-lg border-0 rounded-lg overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 1rem;">
            <div class="card-header bg-light border-0 py-3">
                <h3 class="card-title text-primary font-weight-bold mb-0">
                    <i class="fas fa-chart-pie mr-2"></i> Task Status Distribution
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Legend Details -->
                    <div class="col-md-5 mb-4 mb-md-0">
                        <div class="p-3 rounded-lg bg-light shadow-sm">
                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-muted mr-1"></i> Statistics Summary</h5>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#ffc107;"></span> Pending Tasks
                                </span>
                                <span class="badge badge-warning font-weight-bold px-3 py-2 text-dark" style="font-size:0.9rem;">{{ $pendingTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#17a2b8;"></span> In Progress Tasks
                                </span>
                                <span class="badge badge-info font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $inProgressTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#007bff;"></span> Review Tasks
                                </span>
                                <span class="badge badge-primary font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $submittedTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pb-1">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#28a745;"></span> Completed Tasks
                                </span>
                                <span class="badge badge-success font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $completedTasks }}</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 font-weight-bold text-dark mb-0">Total Tracked Tasks</span>
                                <span class="badge badge-dark font-weight-bold px-3 py-2" style="font-size:1rem; border-radius: 0.5rem;">{{ $totalTasks }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Pie Chart -->
                    <div class="col-md-7 d-flex justify-content-center align-items-center">
                        <div style="position: relative; width: 100%; max-width: 320px; aspect-ratio: 1 / 1;">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Function to load and populate project members
            function loadProjectMembers(projectId, selectedUserId = null) {
                const userSelect = $('select[name="assigned_to"]');
                if (!projectId) {
                    userSelect.html('<option value="">All Employees</option>').prop('disabled', true);
                    return;
                }

                userSelect.prop('disabled', true);

                $.ajax({
                    url: `/projects/${projectId}/members`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(users) {
                        userSelect.empty().append('<option value="">All Employees</option>');
                        $.each(users, function(index, user) {
                            let displayName = user.text || user.name || '';
                            let isSelected = (selectedUserId && selectedUserId == user.id) ? 'selected' : '';
                            userSelect.append(`<option value="${user.id}" ${isSelected}>${displayName}</option>`);
                        });
                        userSelect.prop('disabled', false);
                    },
                    error: function() {
                        alert('Unable to load project members.');
                    }
                });
            }

            // On initial load, check if project is pre-selected
            const initialProject = $('select[name="project_id"]').val();
            if (initialProject) {
                loadProjectMembers(initialProject, '{{ request('assigned_to') }}');
            } else {
                $('select[name="assigned_to"]').prop('disabled', true);
            }

            // When Project changes
            $('select[name="project_id"]').on('change', function() {
                // Clear user dropdown and statistics/task list while loading/resetting
                $('select[name="assigned_to"]').html('<option value="">All Employees</option>').prop('disabled', true);
                
                // Submit form to reload
                $(this).closest('form').submit();
            });

            // When User changes
            $('select[name="assigned_to"]').on('change', function() {
                $(this).closest('form').submit();
            });

            const ctx = document.getElementById('taskStatusChart').getContext('2d');
            
            const pendingCount = {{ $pendingTasks }};
            const inProgressCount = {{ $inProgressTasks }};
            const submittedCount = {{ $submittedTasks }};
            const completedCount = {{ $completedTasks }};
            
            const totalCount = pendingCount + inProgressCount + submittedCount + completedCount;
            
            const dataValues = totalCount > 0 
                ? [pendingCount, inProgressCount, submittedCount, completedCount] 
                : [1];
            const dataLabels = totalCount > 0 
                ? ['Pending', 'In Progress', 'Review', 'Completed'] 
                : ['No Tasks'];
            const bgColors = totalCount > 0 
                ? ['#ffc107', '#17a2b8', '#007bff', '#28a745'] 
                : ['#e2e8f0'];
            const hoverBgColors = totalCount > 0 
                ? ['#e0a800', '#138496', '#0069d9', '#218838'] 
                : ['#cbd5e1'];

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: dataLabels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: bgColors,
                        hoverBackgroundColor: hoverBgColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: totalCount > 0,
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw;
                                    const pct = ((value / totalCount) * 100).toFixed(1);
                                    return ` ${context.label}: ${value} (${pct}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });

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
