@extends('adminlte::page')

@section('title', 'Add Department')

@section('content_header')
    <h1>Add Department</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Create Department</h3>
    </div>

    <form action="{{ route('departments.store') }}" method="POST">

        @csrf

        <div class="card-body">

            <div class="form-group">
                <label>Department Name</label>

                <input
                    type="text"
                    name="department_name"
                    class="form-control @error('department_name') is-invalid @enderror"
                    value="{{ old('department_name') }}"
                    placeholder="Enter Department Name">

                @error('department_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mt-3">
                <label>Status</label>

                <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-primary">
                Save
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</div>

@stop