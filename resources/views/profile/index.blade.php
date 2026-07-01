@extends('adminlte::page')

@section('title', 'My Profile')

@section('content_header')
    <h1>My Profile</h1>
@stop

@section('content')

<div class="row">

    <!-- PROFILE CARD -->
    <div class="col-md-4">

        <div class="card card-primary card-outline">
            <div class="card-body box-profile">

                <div class="text-center">

                    @if(!empty($user->image))
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('storage/profile/'.$user->image) }}"
                             alt="Profile Image">
                    @else
                        <img class="profile-user-img img-fluid img-circle"
                             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200"
                             alt="Profile Image">
                    @endif

                </div>

                <form action="{{ route('Profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="file"
                        name="image"
                        class="form-control">

                    <button type="submit"
                            class="btn btn-primary mt-2">
                        Update Profile
                    </button>
                </form>

                <h3 class="profile-username text-center">
                    {{ $user->name }}
                </h3>

                <p class="text-muted text-center">
                    {{ $user->role_label ?? 'User' }}
                </p>

                <ul class="list-group list-group-unbordered mb-3">

                    <li class="list-group-item">
                        <b>Status</b>
                        <span class="float-right badge badge-success">
                            {{ $user->status ?? 'Active' }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        <b>User ID</b>
                        <span class="float-right">
                            {{ $user->id }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        <b>Email Verified</b>
                        <span class="float-right">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </li>

                </ul>

            </div>
        </div>

    </div>

    


    <!-- RIGHT SIDE -->
    <div class="col-md-8">

        <!-- PERSONAL INFO -->
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Personal Information
                </h3>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="200">Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>

                    <tr>
                        <th>Phone</th>
                        <td>{{ $user->phone ?? 'Not Added' }}</td>
                    </tr>

                    <tr>
                        <th>Role</th>
                        <td>{{ $user->role_label ?? 'User' }}</td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ $user->created_at }}</td>
                    </tr>

                </table>

            </div>

        </div>


        <!-- TASK STATS -->
        <div class="row">

    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalTasks }}</h3>
                <p>Total Tasks</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingTasks }}</h3>
                <p>Pending</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $progressTasks }}</h3>
                <p>In Progress</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $completedTasks }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

</div>


        <!-- CHANGE PASSWORD -->
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Change Password
                </h3>
            </div>

            <div class="card-body">

                <form action="{{ route('change.password') }}" method="POST">

                    @csrf

                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password"
                               name="current_password"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password"
                               name="new_password"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password"
                               name="new_password_confirmation"
                               class="form-control">
                    </div>

                    <button type="submit"
                            class="btn btn-primary">
                        Update Password
                    </button>

                </form>

            </div>

        </div>


        <!-- ACTIVITY TIMELINE -->
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Recent Activity
                </h3>
            </div>

            <div class="card-body">

                <ul class="list-group">

                    <li class="list-group-item">
                        Account Created
                        <span class="float-right text-muted">
                            {{ $user->created_at }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        Last Updated
                        <span class="float-right text-muted">
                            {{ $user->updated_at }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        Current Status
                        <span class="float-right badge badge-success">
                            {{ $user->status ?? 'Active' }}
                        </span>
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>

@stop
