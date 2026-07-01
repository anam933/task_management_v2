@extends('adminlte::page')

@section('title', 'Edit Project Category')

@section('content')

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Edit Project Category</h3>
    </div>

    <form action="{{ route('Project_category.update', $Project_category->id) }}" method="POST">
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

            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="category_name" class="form-control" value="{{ $Project_category->category_name }}" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4">{{ $Project_category->description }}</textarea>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('Project_category.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
