@extends('adminlte::page')

@section('title', 'Edit Department')

@section('content_header')
    <h1>Edit Department</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Department</h3>
    </div>

    <form action="{{ route('departments.update', $department->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label>Department Name</label>

                <input
                    type="text"
                    name="department_name"
                    class="form-control @error('department_name') is-invalid @enderror"
                    value="{{ old('department_name', $department->department_name) }}">

                @error('department_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mt-3">
                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1" {{ $department->status ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ !$department->status ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>
            </div>

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-primary">
                Update
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</div>

@stop