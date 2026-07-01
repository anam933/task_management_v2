@extends('adminlte::page')

@section('title', 'Edit Meeting Notes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Edit Meeting Notes</h1>
            <p class="text-muted mb-0">Update the meeting discussion, decisions, or follow-up items.</p>
        </div>
        <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary">Back to Meetings</a>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <form action="{{ route('meeting-minutes.update', $meeting_minute->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Meeting Date <span class="text-danger">*</span></label>
                        <input type="date" name="meeting_date" class="form-control form-control-lg" value="{{ old('meeting_date', $meeting_minute->meeting_date->toDateString()) }}" required>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $meeting_minute->title) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Project</label>
                <select name="project_id" class="form-control form-control-lg">
                    <option value="">General / No project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $meeting_minute->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }} ({{ $project->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Attendees <span class="text-danger">*</span></label>
                <textarea name="attendees" class="form-control" rows="3" required>{{ old('attendees', $meeting_minute->attendees) }}</textarea>
            </div>

            <div class="form-group">
                <label>Discussion Points <span class="text-danger">*</span></label>
                <textarea name="discussion_points" class="form-control" rows="5" required>{{ old('discussion_points', $meeting_minute->discussion_points) }}</textarea>
            </div>

            <div class="form-group">
                <label>Decisions</label>
                <textarea name="decisions" class="form-control" rows="4">{{ old('decisions', $meeting_minute->decisions) }}</textarea>
            </div>

            <div class="form-group">
                <label>Action Items</label>
                <textarea name="action_items" class="form-control" rows="4">{{ old('action_items', $meeting_minute->action_items) }}</textarea>
            </div>

            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $meeting_minute->notes) }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Last updated {{ $meeting_minute->updated_at->format('d M Y H:i') }}</span>
            <div>
                <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Update Notes</button>
            </div>
        </div>
    </form>
</div>
@endsection
