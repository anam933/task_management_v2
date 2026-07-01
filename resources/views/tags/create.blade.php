@extends('adminlte::page')

@section('title', 'Add Tag')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Add New Tag</h1>
            <p class="text-muted mb-0">Create reusable task labels for filtering and grouping.</p>
        </div>
        <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">Back to Tags</a>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <form action="{{ route('tags.store') }}" method="POST">
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
                <div class="col-lg-8">
                    <div class="form-group">
                        <label>Tag Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" name="color" class="form-control form-control-lg" value="{{ old('color', '#0d6efd') }}" style="height: 52px; padding: 6px;">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Optional note about this tag">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Tip: keep tag names short and action-focused.</span>
            <div>
                <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Save Tag</button>
            </div>
        </div>
    </form>
</div>
@endsection
