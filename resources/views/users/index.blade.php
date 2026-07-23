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
                <div class="card card-outline card-primary shadow-sm border-0 rounded-lg overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 1rem; height: 100%;">
                    <div class="card-header bg-light border-0 py-2">
                        <h3 class="card-title text-primary font-weight-bold mb-0" style="font-size: 1.1rem;">
                            <i class="fas fa-chart-pie mr-2"></i> Task Status Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Legend Details -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="p-3 rounded-lg bg-light shadow-sm">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-muted mr-1"></i> Summary</h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <span class="mr-2" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#ffc107;"></span> Pending Tasks
                                        </span>
                                        <span class="badge badge-warning font-weight-bold px-2 py-1 text-dark" style="font-size:0.8rem;">{{ $stats['pending'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <span class="mr-2" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#17a2b8;"></span> In Progress
                                        </span>
                                        <span class="badge badge-info font-weight-bold px-2 py-1" style="font-size:0.8rem;">{{ $stats['in_progress'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <span class="mr-2" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#007bff;"></span> Review Tasks
                                        </span>
                                        <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size:0.8rem;">{{ $stats['submitted'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <span class="mr-2" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#28a745;"></span> Completed
                                        </span>
                                        <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size:0.8rem;">{{ $stats['completed'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <i class="fas fa-tasks mr-2 text-primary"></i> Assigned Tasks
                                        </span>
                                        <span class="badge badge-secondary font-weight-bold px-2 py-1" style="font-size:0.8rem;">{{ $stats['assigned'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="d-flex align-items-center text-muted font-weight-bold" style="font-size: 0.85rem;">
                                            <i class="fas fa-plus-circle mr-2 text-success"></i> Created Tasks
                                        </span>
                                        <span class="badge badge-secondary font-weight-bold px-2 py-1" style="font-size:0.8rem;">{{ $stats['created'] }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pie Chart -->
                            <div class="col-md-6">
                                <div class="chart-container position-relative" style="height: 220px; width: 100%;">
                                    <canvas id="userStatusChart"></canvas>
                                </div>
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

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            @if($selectedUser)
                const ctx = document.getElementById('userStatusChart').getContext('2d');
                
                const pendingCount = {{ $stats['pending'] }};
                const inProgressCount = {{ $stats['in_progress'] }};
                const submittedCount = {{ $stats['submitted'] }};
                const completedCount = {{ $stats['completed'] }};
                
                const totalCount = pendingCount + inProgressCount + submittedCount + completedCount;
                
                const dataValues = totalCount > 0 
                    ? [pendingCount, inProgressCount, submittedCount, completedCount] 
                    : [1];
                const dataLabels = totalCount > 0 
                    ? ['Pending', 'In Progress', 'Review', 'Completed'] 
                    : ['No Tasks'];
                const bgColors = totalCount > 0 
                    ? ['#ffc107', '#17a2b8', '#007bff', '#28a745'] 
                    : ['#e2e8f0'];
                const hoverBgColors = totalCount > 0 
                    ? ['#e0a800', '#138496', '#0069d9', '#218838'] 
                    : ['#cbd5e1'];

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: dataLabels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: bgColors,
                            hoverBackgroundColor: hoverBgColors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: totalCount > 0,
                                callbacks: {
                                    label: function (context) {
                                        const value = context.raw;
                                        const pct = ((value / totalCount) * 100).toFixed(1);
                                        return ` ${context.label}: ${value} (${pct}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            @endif
        });
    </script>
@endpush

@endsection