@extends('adminlte::page')

@section('title', 'Add User')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Add User</h3>
    </div>

```
<div class="card-body">

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text"
                   name="phone"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="Admin">Admin</option>
                <option value="Manager">Manager</option>
                <option value="Employee">Employee</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   required>
        </div>

        <button type="submit" class="btn btn-success">
            Save User
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
