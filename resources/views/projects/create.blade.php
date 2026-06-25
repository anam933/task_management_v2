@extends('adminlte::page')

@section('title', 'Create Project')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Create Project</h1>
            <p class="text-muted mb-0">Set up a new project with ownership, budget, and team members.</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Back to Projects</a>
    </div>
@stop

@section('content')
    @include('projects.partials.toasts')

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title mb-0">Project Information</h3>
        </div>

        <form action="{{ route('projects.store') }}" method="POST">
            @csrf

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

                @include('projects.partials.form', ['users' => $users, 'project' => null])
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="text-muted">Projects are the top-level delivery unit in the system.</span>
                <div>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Save Project</button>
                </div>
            </div>
        </form>
    </div>
@endsection

