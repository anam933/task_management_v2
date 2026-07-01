@extends('adminlte::page')

@section('title', 'Meeting Minutes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Meeting Minutes</h1>
            <p class="text-muted mb-0">Track meeting discussions, decisions, and action items in one place.</p>
        </div>
        @can('manage-meeting-minutes')
            <a href="{{ route('meeting-minutes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Add Meeting Notes
            </a>
        @endcan
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalMeetings }}</h3>
                    <p>Total Meetings</p>
                </div>
                <div class="icon"><i class="fas fa-handshake"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $todayMeetings }}</h3>
                    <p>Today</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $decisionMeetings }}</h3>
                    <p>Decisions Logged</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $weekMeetings }}</h3>
                    <p>This Week</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-week"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Meeting Date</label>
                    <input type="date" name="meeting_date" class="form-control" value="{{ $filters['meeting_date'] ?? '' }}">
                </div>
                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(auth()->user()->hasRole(['admin', 'manager']))
                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Employee</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ ($filters['user_id'] ?? '') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-lg-3 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary mr-2 w-100">Filter</button>
                    <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Attendees</th>
                            <th>Decisions</th>
                            <th>Action Items</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $meeting)
                            <tr>
                                <td>{{ optional($meeting->meeting_date)->format('d M Y') }}</td>
                                <td>{{ $meeting->title }}</td>
                                <td>{{ optional($meeting->project)->project_name ?? 'General' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($meeting->attendees, 60) }}</td>
                                <td>{{ $meeting->decisions ? \Illuminate\Support\Str::limit($meeting->decisions, 60) : 'None' }}</td>
                                <td>{{ $meeting->action_items ? \Illuminate\Support\Str::limit($meeting->action_items, 60) : 'None' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('meeting-minutes.show', $meeting->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('manage-meeting-minutes')
                                            <a href="{{ route('meeting-minutes.edit', $meeting->id) }}" class="btn btn-warning">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form method="POST" action="{{ route('meeting-minutes.destroy', $meeting->id) }}" class="d-inline" onsubmit="return confirm('Delete this meeting note?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No meeting notes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>
@endsection
