@extends('adminlte::page')

@section('title', 'Edit User')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Edit User</h3>
    </div>

```
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
            <select name="role" class="form-control">
                <option value="Admin" {{ $user->role=='Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Manager" {{ $user->role=='Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Employee" {{ $user->role=='Employee' ? 'selected' : '' }}>Employee</option>
            </select>
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
```

</div>

@endsection
