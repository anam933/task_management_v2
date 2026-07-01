@extends('adminlte::page')

@section('title', 'Edit User')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Edit User</h3>
    </div>

<div class="card-body">

    <form action="{{ route('users.update',$user->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text"
                   name="name"
                   value="{{ $user->name }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   value="{{ $user->email }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text"
                   name="phone"
                   value="{{ $user->phone }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            @if(auth()->user()->hasRole('manager'))
                <input type="hidden" name="role" value="employee">
                <div class="form-control bg-light">Employee</div>
                <small class="text-muted">Managers can edit employees only.</small>
            @else
                <select name="role" class="form-control">
                    <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ $user->role=='manager' ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ $user->role=='employee' ? 'selected' : '' }}>Employee</option>
                </select>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            Update User
        </button>

        <a href="{{ route('users.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </form>

</div>

</div>

@endsection
