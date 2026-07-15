@extends('adminlte::page')

@section('title', auth()->user()->hasRole('manager') ? 'Add Employee' : 'Add User')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>{{ auth()->user()->hasRole('manager') ? 'Add Employee' : 'Add User' }}</h3>
    </div>

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
            @if(auth()->user()->hasRole('manager'))
                <input type="hidden" name="role" value="employee">
                <div class="form-control bg-light">Employee</div>
                <small class="text-muted">Managers can create employees only.</small>
            @else
                <select name="role" class="form-control @error('role') is-invalid @enderror">
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            @endif
        </div>

        <div class="mb-3">
    <label>Project Category</label>

    <select name="category_id" class="form-control">
        <option value="">Select Category</option>

        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->category_name }}
            </option>
        @endforeach
    </select>
</div>



        @if(auth()->user()->hasRole('admin'))
            <div class="mb-3" id="reports-to-group" style="display: none;">
                <label>Reporting Manager</label>
                <select name="reports_to" class="form-control @error('reports_to') is-invalid @enderror">
                    <option value="">Select Reporting Manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('reports_to') == $manager->id ? 'selected' : '' }}>
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
            <label>Password</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
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
