@extends('adminlte::page')

@section('title', 'Projects')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Project Management</h1>
            <p class="text-muted mb-0">Monitor delivery, budgets, owners, and progress across all projects.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Project
        </a>
    </div>
@stop

@section('css')
    <style>
        .project-page {
            background: linear-gradient(180deg, rgba(13,110,253,0.04), rgba(255,255,255,0));
        }
        .project-metric {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        .project-metric .inner h3 {
            font-size: 2rem;
            font-weight: 700;
        }
        .table thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6c757d;
            border-top: 0;
        }
    </style>
@stop

@section('content')
<div class="project-page">
    @include('projects.partials.toasts')

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-primary project-metric">
                <div class="inner">
                    <h3>{{ $totalProjects }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success project-metric">
                <div class="inner">
                    <h3>{{ $activeProjects }}</h3>
                    <p>Active Projects</p>
                </div>
                <div class="icon"><i class="fas fa-play-circle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info project-metric">
                <div class="inner">
                    <h3>{{ $completedProjects }}</h3>
                    <p>Completed Projects</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning project-metric">
                <div class="inner">
                    <h3>{{ $onHoldProjects }}</h3>
                    <p>On Hold Projects</p>
                </div>
                <div class="icon"><i class="fas fa-pause-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <form method="GET" class="row align-items-end">
                <div class="col-lg-4 mb-2">
                    <label class="mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Project name, code, description">
                </div>

                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="Planning" {{ ($filters['status'] ?? '') === 'Planning' ? 'selected' : '' }}>Planning</option>
                        <option value="Active" {{ ($filters['status'] ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="On Hold" {{ ($filters['status'] ?? '') === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="Completed" {{ ($filters['status'] ?? '') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ ($filters['status'] ?? '') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Priority</label>
                    <select name="priority" class="form-control">
                        <option value="">All Priority</option>
                        <option value="Low" {{ ($filters['priority'] ?? '') === 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ ($filters['priority'] ?? '') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ ($filters['priority'] ?? '') === 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <div class="col-lg-2 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary mr-2 w-100">Filter</button>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Project</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Team</th>
                            <th>Budget</th>
                            <th>Timeline</th>
                            <th>Progress</th>
                            <th style="width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
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
                                $progress = $project->tasks_count > 0 ? (int) round(($project->completed_tasks_count / $project->tasks_count) * 100) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-weight-bold">
                                        <a href="{{ route('projects.show', $project->id) }}" class="text-dark">{{ $project->project_name }}</a>
                                    </div>
                                    <small class="text-muted">{{ $project->project_code }}</small>
                                </td>
                                <td>{{ optional($project->manager)->name ?? 'Unassigned' }}</td>
                                <td><span class="badge {{ $statusClass }} px-3 py-2">{{ $project->project_status }}</span></td>
                                <td><span class="badge {{ $priorityClass }} px-3 py-2">{{ $project->priority }}</span></td>
                                <td>
                                    <span class="badge badge-light border px-3 py-2">{{ $project->teamMembers->count() }} members</span>
                                </td>
                                <td>{{ $project->budget !== null ? number_format($project->budget, 2) : 'N/A' }}</td>
                                <td>
                                    <div>{{ optional($project->start_date)->format('d M Y') }}</div>
                                    <small class="text-muted">to {{ optional($project->end_date)->format('d M Y') ?? 'Open' }}</small>
                                </td>
                                <td style="min-width: 160px;">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>{{ $progress }}%</span>
                                        <span>{{ $project->completed_tasks_count }}/{{ $project->tasks_count }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger js-delete-project"
                                                data-id="{{ $project->id }}"
                                                data-url="{{ route('projects.destroy', $project->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        document.querySelectorAll('.js-delete-project').forEach((button) => {
            button.addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: 'Delete project?',
                    text: 'This project will be moved to trash.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                });

                if (!result.isConfirmed) {
                    return;
                }

                const response = await fetch(button.dataset.url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    toast.fire({ icon: 'success', title: data.message || 'Deleted successfully.' });
                    window.location.reload();
                    return;
                }

                toast.fire({ icon: 'error', title: 'Unable to delete project.' });
            });
        });
    </script>
@endpush
