@extends('adminlte::page')

@section('title', 'Edit Project')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Edit Project</h1>
            <p class="text-muted mb-0">Update project details, team, schedule, and status.</p>
        </div>
        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-info mr-2">View Details</a>
    </div>
@stop

@section('content')
    @include('projects.partials.toasts')

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Project Information</h3>
            <span class="badge badge-light border px-3 py-2">Project #{{ $project->id }}</span>
        </div>

        <form action="{{ route('projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following issues:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('projects.partials.form', ['users' => $users, 'project' => $project])
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="text-muted">Keep the status aligned with the actual delivery stage.</span>
                <div>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Update Project</button>
                </div>
            </div>
        </form>
    </div>
@endsection

