@extends('adminlte::page')

@section('title', 'Meeting Notes Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Meeting Notes</h1>
            <p class="text-muted mb-0">Detailed view of the meeting discussion and follow-up items.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary">Back to list</a>
            @can('manage-meeting-minutes')
                <a href="{{ route('meeting-minutes.edit', $meeting->id) }}" class="btn btn-warning">Edit</a>
            @endcan
        </div>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <strong>Date</strong>
                <p>{{ $meeting->meeting_date->format('d M Y') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Project</strong>
                <p>{{ optional($meeting->project)->project_name ?? 'General' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Author</strong>
                <p>{{ optional($meeting->user)->name ?? 'Unknown' }}</p>
            </div>
        </div>

        <h3 class="h5">{{ $meeting->title }}</h3>
        <div class="mb-4 text-muted">Recorded on {{ $meeting->created_at->format('d M Y \a\t H:i') }}</div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h4 class="card-title">Attendees</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $meeting->attendees }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h4 class="card-title">Decisions</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $meeting->decisions ?? 'No decisions recorded' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h4 class="card-title">Discussion Points</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $meeting->discussion_points }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h4 class="card-title">Action Items</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $meeting->action_items ?? 'No action items assigned' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h4 class="card-title">Additional Notes</h4>
            </div>
            <div class="card-body">
                <p>{{ $meeting->notes ?? 'No additional notes' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
