@extends('adminlte::page')

@section('title', 'Daily Standup Reports')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Daily Standup Reports</h1>
            <p class="text-muted mb-0">Track yesterday's work, today's plan, and blockers in one place.</p>
        </div>
        @can('manage-standup-reports')
        <a href="{{ route('standup-reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Report
        </a>
        @endcan
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalReports }}</h3>
                    <p>Total Reports</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $todayReports }}</h3>
                    <p>Today</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $blockedReports }}</h3>
                    <p>With Blockers</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $weekReports }}</h3>
                    <p>This Week</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-week"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Report Date</label>
                    <input type="date" name="report_date" class="form-control" value="{{ $filters['report_date'] ?? '' }}">
                </div>

                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ ($filters['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->hasRole(['admin', 'manager']))
                <div class="col-lg-3 mb-2">
                    <label class="mb-1">Employee</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ ($filters['user_id'] ?? '') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-lg-3 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary mr-2 w-100">Filter</button>
                    <a href="{{ route('standup-reports.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Project</th>
                            <th>Yesterday</th>
                            <th>Today</th>
                            <th>Blockers</th>
                            <th>Notes</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ optional($report->report_date)->format('d M Y') }}</td>
                                <td>{{ optional($report->user)->name ?? 'Unknown' }}</td>
                                <td>{{ optional($report->project)->project_name ?? 'General' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($report->yesterday_work, 70) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($report->today_plan, 70) }}</td>
                                <td>{{ $report->blockers ? \Illuminate\Support\Str::limit($report->blockers, 70) : 'None' }}</td>
                                <td>{{ $report->notes ? \Illuminate\Support\Str::limit($report->notes, 70) : 'None' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('standup-reports.show', $report->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('manage-standup-reports')
                                        <a href="{{ route('standup-reports.edit', $report->id) }}" class="btn btn-warning">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger js-delete-report"
                                                data-url="{{ route('standup-reports.destroy', $report->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No standup reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.querySelectorAll('.js-delete-report').forEach((button) => {
    button.addEventListener('click', async () => {
        const result = await Swal.fire({
            title: 'Delete report?',
            text: 'This standup report will be removed permanently.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) {
            return;
        }

        const response = await fetch(button.dataset.url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const data = await response.json();
            toast.fire({ icon: 'success', title: data.message || 'Deleted successfully.' });
            window.location.reload();
            return;
        }

        toast.fire({ icon: 'error', title: 'Unable to delete standup report.' });
    });
});
</script>
@endpush
