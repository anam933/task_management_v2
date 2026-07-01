@extends('adminlte::page')

@section('title', 'New Standup Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">New Standup Report</h1>
            <p class="text-muted mb-0">Capture yesterday, today, and blockers in a quick daily update.</p>
        </div>
        <a href="{{ route('standup-reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <form action="{{ route('standup-reports.store') }}" method="POST">
        @csrf

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
                        <label>Report Date <span class="text-danger">*</span></label>
                        <input type="date" name="report_date" class="form-control form-control-lg" value="{{ old('report_date', now()->toDateString()) }}" required>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="form-group">
                        <label>Project</label>
                        <select name="project_id" class="form-control form-control-lg">
                            <option value="">General / No project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }} ({{ $project->project_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>What was done yesterday? <span class="text-danger">*</span></label>
                <textarea name="yesterday_work" class="form-control" rows="4" required>{{ old('yesterday_work') }}</textarea>
            </div>

            <div class="form-group">
                <label>What will you do today? <span class="text-danger">*</span></label>
                <textarea name="today_plan" class="form-control" rows="4" required>{{ old('today_plan') }}</textarea>
            </div>

            <div class="form-group">
                <label>Blockers</label>
                <textarea name="blockers" class="form-control" rows="3" placeholder="Mention anything that is blocking progress">{{ old('blockers') }}</textarea>
            </div>

            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Submitted by {{ auth()->user()->name }}</span>
            <div>
                <a href="{{ route('standup-reports.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Submit Report</button>
            </div>
        </div>
    </form>
</div>
@endsection
