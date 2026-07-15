@extends('adminlte::page')

@section('title', 'User Management')

@section('content')

@can('manage-employees')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h3 class="mb-0">
        <i class="fas fa-users"></i>
        User Management
    </h3>

    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        {{ auth()->user()->hasRole('manager') ? 'Add Employee' : 'Add User' }}
    </a>

</div>

<!-- ================= Filter Card ================= -->

<div class="card">

    <div class="card-header bg-primary">

        <h3 class="card-title">

            <i class="fas fa-filter"></i>

            User Filter

        </h3>

    </div>

    <div class="card-body">

        <form action="{{ route('users.index') }}" method="GET">

            <div class="row">

                <div class="col-md-6">

                    <label>Select User</label>

                    <select name="user_id" class="form-control">

                        <option value="">
                            All Users
                        </option>

                        @foreach($allUsers as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ $selectedUser == $item->id ? 'selected' : '' }}>

                                {{ $item->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 d-flex align-items-end">

                    <button class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Filter

                    </button>

                    <a href="{{ route('users.index') }}"
                       class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

@if($selectedUser)

<div class="card mt-4">

    <div class="card-header bg-info">

        <h3 class="card-title">

            <i class="fas fa-user"></i>

            User Summary

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <table class="table table-bordered">

                    <tr>
                        <th width="35%">Name</th>
                        <td>{{ $userInfo->name }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $userInfo->email }}</td>
                    </tr>

                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($userInfo->role) }}</td>
                    </tr>

                    <tr>
                        <th>Category</th>
                        <td>{{ optional($userInfo->category)->category_name }}</td>
                    </tr>

                    <tr>
                        <th>Reporting To</th>
                        <td>{{ optional($userInfo->manager)->name ?? 'N/A' }}</td>
                    </tr>

                    <tr>
                        <th>Created By</th>
                        <td>{{ optional($userInfo->creator)->name ?? 'System' }}</td>
                    </tr>

                    <tr>
                        <th>Joined On</th>
                        <td>{{ optional($userInfo->created_at)->format('d M Y') }}</td>
                    </tr>

                </table>

            </div>

            <div class="col-md-6">

                <div class="row">
                                        <div class="col-md-6 mb-3">

                        <div class="small-box bg-info">

                            <div class="inner">

                                <h3>{{ $stats['assigned'] }}</h3>

                                <p>Assigned Tasks</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-tasks"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <div class="small-box bg-success">

                            <div class="inner">

                                <h3>{{ $stats['created'] }}</h3>

                                <p>Created Tasks</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-plus-circle"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <div class="small-box bg-warning">

                            <div class="inner">

                                <h3>{{ $stats['pending'] }}</h3>

                                <p>Pending</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-clock"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <div class="small-box bg-primary">

                            <div class="inner">

                                <h3>{{ $stats['in_progress'] }}</h3>

                                <p>In Progress</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-spinner"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <div class="small-box bg-secondary">

                            <div class="inner">

                                <h3>{{ $stats['submitted'] }}</h3>

                                <p>Submitted</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-paper-plane"></i>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <div class="small-box bg-success">

                            <div class="inner">

                                <h3>{{ $stats['completed'] }}</h3>

                                <p>Completed</p>

                            </div>

                            <div class="icon">

                                <i class="fas fa-check-circle"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endif

@endcan
<div class="card mt-4">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-users"></i>

            Users List

        </h3>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover">

            <thead class="thead-light">

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Role</th>

                    <th>Reporting To</th>

                    <th>Created By</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($users as $user)

                <tr>

                    <td>{{ $user->id }}</td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>

                        <span class="badge badge-info">

                            {{ ucfirst($user->role) }}

                        </span>

                    </td>

                    <td>

                        {{ optional($user->manager)->name ?? 'N/A' }}

                    </td>

                    <td>

                        {{ optional($user->creator)->name ?? 'System' }}

                    </td>

                    <td>

                        @can('manage-employees')

                        <a href="{{ route('users.edit',$user->id) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('users.destroy',$user->id) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this user?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                        @endcan

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No users found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection