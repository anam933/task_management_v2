@extends('adminlte::page')

@section('title', 'Meeting Minutes Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1 text-dark font-weight-bold">Meeting Minutes</h1>
            <p class="text-muted mb-0">Detailed minutes of the discussion, decisions, and follow-up tracking.</p>
        </div>
        <div class="btn-group shadow-sm">
            <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
            @can('manage-meeting-minutes', $meeting)
                <a href="{{ route('meeting-minutes.edit', $meeting->id) }}" class="btn btn-warning btn-sm text-dark font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            @endcan
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge badge-pill badge-primary px-3 py-2">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $meeting->meeting_date->format('d M Y') }}
                        </span>
                        
                        @php
                            $statusClass = match ($meeting->status) {
                                'Draft' => 'badge-secondary',
                                'Published' => 'badge-success',
                                'Completed' => 'badge-info',
                                default => 'badge-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} px-3 py-2 font-weight-bold">
                            {{ $meeting->status }}
                        </span>
                    </div>

                    <h2 class="h3 font-weight-bold text-dark mb-1">{{ $meeting->meeting_title }}</h2>
                    <p class="text-muted small mb-4">
                        Recorded by <strong>{{ optional($meeting->user)->name ?? 'Unknown' }}</strong> on {{ $meeting->created_at->format('d M Y \a\t H:i') }}
                    </p>

                    <!-- Agenda -->
                    <div class="mb-4">
                        <h4 class="h6 font-weight-bold text-secondary text-uppercase border-bottom pb-2">
                            <i class="fas fa-bullseye text-primary mr-1"></i> Meeting Agenda
                        </h4>
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $meeting->agenda ?? 'No agenda specified.' }}</div>
                    </div>

                    <!-- Discussion Points -->
                    <div class="mb-4">
                        <h4 class="h6 font-weight-bold text-secondary text-uppercase border-bottom pb-2">
                            <i class="fas fa-comments text-success mr-1"></i> Discussion Points
                        </h4>
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $meeting->discussion_points }}</div>
                    </div>

                    <!-- Decisions -->
                    <div class="mb-4">
                        <h4 class="h6 font-weight-bold text-secondary text-uppercase border-bottom pb-2">
                            <i class="fas fa-gavel text-warning mr-1"></i> Key Decisions
                        </h4>
                        <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $meeting->decisions ?? 'No major decisions logged.' }}</div>
                    </div>

                    <!-- General Action Items Text -->
                    @if($meeting->action_items)
                        <div class="mb-4">
                            <h4 class="h6 font-weight-bold text-secondary text-uppercase border-bottom pb-2">
                                <i class="fas fa-clipboard-list text-info mr-1"></i> Action Items Notes
                            </h4>
                            <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $meeting->action_items }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Structured Action Items Table -->
            <div class="card card-outline card-info shadow-sm border-0 rounded-lg mt-4">
                <div class="card-header bg-light">
                    <h3 class="card-title text-info font-weight-bold mb-0">
                        <i class="fas fa-tasks mr-1"></i> Tracked Action Assignments
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Action Title</th>
                                    <th>Assigned To</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($meeting->actions as $action)
                                    <tr>
                                        <td class="font-weight-bold">{{ $action->action_title }}</td>
                                        <td>
                                            <i class="fas fa-user-circle text-muted mr-1"></i> {{ optional($action->assignee)->name ?? 'Unassigned' }}
                                        </td>
                                        <td>
                                            <span class="text-muted"><i class="far fa-calendar-times mr-1"></i> {{ $action->deadline->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $actionStatusClass = match ($action->status) {
                                                    'Completed' => 'badge-success',
                                                    'In Progress' => 'badge-info',
                                                    'Pending' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $actionStatusClass }} px-3 py-1">{{ $action->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No structured action assignments for this meeting.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Logistics Info -->
            <div class="card card-outline card-secondary shadow-sm border-0 rounded-lg">
                <div class="card-header bg-light">
                    <h3 class="card-title text-dark font-weight-bold mb-0"><i class="fas fa-info-circle mr-1"></i> Logistics</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-unbordered mb-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-top-0 px-0">
                            <span><strong>Time</strong></span>
                            <span class="text-dark"><i class="far fa-clock text-primary mr-1"></i> {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong>Meeting Type</strong></span>
                            <span class="badge badge-pill badge-secondary px-3">{{ $meeting->meeting_type }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong>Project</strong></span>
                            <span class="text-dark">
                                @if($meeting->project)
                                    <a href="{{ route('projects.show', $meeting->project->id) }}" class="font-weight-bold">
                                        <i class="fas fa-folder text-warning mr-1"></i> {{ $meeting->project->project_name }}
                                    </a>
                                @else
                                    <span class="text-muted font-italic">General / Internal</span>
                                @endif
                            </span>
                        </li>
                        @if($meeting->location)
                            <li class="list-group-item border-bottom-0 px-0 pt-3">
                                <strong>Location / Meeting Link</strong>
                                <div class="mt-2 text-wrap bg-light p-2 rounded text-dark font-weight-bold">
                                    @if(filter_var($meeting->location, FILTER_VALIDATE_URL))
                                        <a href="{{ $meeting->location }}" target="_blank" class="text-truncate d-block">
                                            <i class="fas fa-external-link-alt mr-1"></i> Join Meeting
                                        </a>
                                    @else
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $meeting->location }}
                                    @endif
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Participants Card -->
            <div class="card card-outline card-secondary shadow-sm border-0 rounded-lg mt-4">
                <div class="card-header bg-light">
                    <h3 class="card-title text-dark font-weight-bold mb-0">
                        <i class="fas fa-users mr-1"></i> Attendees ({{ $meeting->participants->count() }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mb-0">
                        @forelse($meeting->participants as $participant)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                                <div>
                                    <i class="fas fa-user-check text-success mr-2"></i><strong>{{ $participant->name }}</strong>
                                </div>
                                <span class="badge badge-light border text-muted small">{{ ucfirst($participant->role) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3 text-muted">No attendees recorded.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
