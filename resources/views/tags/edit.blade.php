@extends('adminlte::page')

@section('title', 'Edit Tag')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Edit Tag</h1>
            <p class="text-muted mb-0">Update label name, color, and description.</p>
        </div>
        <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">Back to Tags</a>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <form action="{{ route('tags.update', $tag->id) }}" method="POST">
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
                <div class="col-lg-8">
                    <div class="form-group">
                        <label>Tag Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name', $tag->name) }}" required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="color" name="color" class="form-control form-control-lg" value="{{ old('color', $tag->color) }}" style="height: 52px; padding: 6px;">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Optional note about this tag">{{ old('description', $tag->description) }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Tag updates apply immediately to linked tasks.</span>
            <div>
                <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Update Tag</button>
            </div>
        </div>
    </form>
</div>
@endsection
