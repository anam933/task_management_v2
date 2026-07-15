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
                <select name="role" class="form-control @error('role') is-invalid @enderror">
                    <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ (old('role', $user->role) == 'manager') ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ (old('role', $user->role) == 'employee') ? 'selected' : '' }}>Employee</option>
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            @endif
        </div>

        @if(auth()->user()->hasRole('admin'))
            <div class="mb-3" id="reports-to-group" style="display: none;">
                <label>Reporting Manager</label>
                <select name="reports_to" class="form-control @error('reports_to') is-invalid @enderror">
                    <option value="">Select Reporting Manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('reports_to', $user->reports_to) == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }} ({{ $manager->category?->category_name ?? 'No Category' }})
                        </option>
                    @endforeach
                </select>
                @error('reports_to')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        @endif

       <div class="mb-3">
    <label>Project Category</label>

    <select name="category_id" class="form-control">

        @foreach($categories as $category)

            <option value="{{ $category->id }}"
                {{ old('category_id',$user->category_id)==$category->id?'selected':'' }}>

                {{ $category->category_name }}

            </option>

        @endforeach

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

</div>

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.querySelector('select[name="role"]');
        const reportsToGroup = document.getElementById('reports-to-group');

        function toggleReportsTo() {
            if (roleSelect && roleSelect.value === 'employee') {
                if (reportsToGroup) reportsToGroup.style.display = 'block';
            } else {
                if (reportsToGroup) reportsToGroup.style.display = 'none';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleReportsTo);
            toggleReportsTo(); // Initialize
        }
    });
</script>
@endsection
