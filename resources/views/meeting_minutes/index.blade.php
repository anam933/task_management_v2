@extends('adminlte::page')

@section('title', 'Meeting Minutes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1 text-dark font-weight-bold">Meeting Minutes</h1>
            <p class="text-muted mb-0">Track and manage discussions, decisions, attendees, and action items in one place.</p>
        </div>
        @can('manage-meeting-minutes')
            <a href="{{ route('meeting-minutes.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> New Meeting Minutes
            </a>
        @endcan
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Stat Boxes -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-white border shadow-sm rounded-lg overflow-hidden">
                <div class="inner p-3">
                    <h3 class="font-weight-bold text-primary">{{ $totalMeetings }}</h3>
                    <p class="text-muted text-uppercase mb-0 small font-weight-bold">Total Meetings</p>
                </div>
                <div class="icon text-primary opacity-25 p-3"><i class="fas fa-handshake fa-2x"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-white border shadow-sm rounded-lg overflow-hidden">
                <div class="inner p-3">
                    <h3 class="font-weight-bold text-success">{{ $todayMeetings }}</h3>
                    <p class="text-muted text-uppercase mb-0 small font-weight-bold">Today's Meetings</p>
                </div>
                <div class="icon text-success opacity-25 p-3"><i class="fas fa-calendar-day fa-2x"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-white border shadow-sm rounded-lg overflow-hidden">
                <div class="inner p-3">
                    <h3 class="font-weight-bold text-warning">{{ $decisionMeetings }}</h3>
                    <p class="text-muted text-uppercase mb-0 small font-weight-bold">Decisions Logged</p>
                </div>
                <div class="icon text-warning opacity-25 p-3"><i class="fas fa-gavel fa-2x"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-white border shadow-sm rounded-lg overflow-hidden">
                <div class="inner p-3">
                    <h3 class="font-weight-bold text-info">{{ $weekMeetings }}</h3>
                    <p class="text-muted text-uppercase mb-0 small font-weight-bold">This Week</p>
                </div>
                <div class="icon text-info opacity-25 p-3"><i class="fas fa-calendar-week fa-2x"></i></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-outline card-primary shadow-sm mb-4 border-0 rounded-lg">
        <div class="card-header bg-light">
            <h3 class="card-title text-dark font-weight-bold mb-0"><i class="fas fa-filter mr-1"></i> Search & Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('meeting-minutes.index') }}" class="row align-items-end">
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="mb-1 font-weight-bold text-muted small">Search Title</label>
                    <input type="text" name="search" class="form-control form-control-sm shadow-sm" placeholder="Search by title..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="mb-1 font-weight-bold text-muted small">Project</label>
                    <select name="project_id" class="form-control form-control-sm shadow-sm">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-2">
                    <label class="mb-1 font-weight-bold text-muted small">Status</label>
                    <select name="status" class="form-control form-control-sm shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="Draft" {{ ($filters['status'] ?? '') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Published" {{ ($filters['status'] ?? '') == 'Published' ? 'selected' : '' }}>Published</option>
                        <option value="Completed" {{ ($filters['status'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-2">
                    <label class="mb-1 font-weight-bold text-muted small">Meeting Date</label>
                    <input type="date" name="meeting_date" class="form-control form-control-sm shadow-sm" value="{{ $filters['meeting_date'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-4 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm mr-2 w-100 shadow-sm">Filter</button>
                    <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary btn-sm w-100 shadow-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Meeting Minutes Table -->
    <div class="card card-outline card-primary shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-right" style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $meeting)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ optional($meeting->meeting_date)->format('d M Y') }}</div>
                                    <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $meeting->meeting_title }}</div>
                                    <small class="text-muted"><i class="fas fa-users mr-1"></i> {{ $meeting->participants->count() }} attendee(s)</small>
                                </td>
                                <td>
                                    @if($meeting->project)
                                        <span class="badge badge-light border text-dark"><i class="fas fa-folder text-warning mr-1"></i> {{ $meeting->project->project_name }}</span>
                                    @else
                                        <span class="text-muted font-italic">General / Internal</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-secondary">{{ $meeting->meeting_type }}</span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $meeting->location ?? 'N/A' }}">
                                        {{ $meeting->location ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($meeting->status) {
                                            'Draft' => 'badge-secondary',
                                            'Published' => 'badge-success',
                                            'Completed' => 'badge-info',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-2 py-1">{{ $meeting->status }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <a href="{{ route('meeting-minutes.show', $meeting->id) }}" class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('manage-meeting-minutes', $meeting)
                                            <a href="{{ route('meeting-minutes.edit', $meeting->id) }}" class="btn btn-warning text-dark font-weight-bold" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="POST" action="{{ route('meeting-minutes.destroy', $meeting->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this meeting minute? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-handshake fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">No meeting minutes found matching the criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($meetings->hasPages())
            <div class="card-footer clearfix bg-light">
                <div class="float-right m-0">
                    {{ $meetings->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
