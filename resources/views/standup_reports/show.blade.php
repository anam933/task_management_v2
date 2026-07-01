@extends('adminlte::page')

@section('title', 'Standup Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Standup Report</h1>
            <p class="text-muted mb-0">{{ optional($report->report_date)->format('d M Y') }}</p>
        </div>
        <div>
            @can('manage-standup-reports')
            <a href="{{ route('standup-reports.edit', $report->id) }}" class="btn btn-warning mr-2">
                <i class="fas fa-pen mr-1"></i> Edit
            </a>
            @endcan
            <a href="{{ route('standup-reports.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Report Details</h3>
                    <span class="badge badge-primary px-3 py-2">{{ optional($report->report_date)->format('d M Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Submitted By</small>
                                <strong>{{ optional($report->user)->name ?? 'Unknown' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Project</small>
                                <strong>{{ optional($report->project)->project_name ?? 'General' }}</strong>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-2">Yesterday's Work</small>
                                <div>{{ $report->yesterday_work }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-2">Today's Plan</small>
                                <div>{{ $report->today_plan }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-2">Blockers</small>
                                <div>{{ $report->blockers ?: 'None' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-2">Additional Notes</small>
                                <div>{{ $report->notes ?: 'None' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">Metadata</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Report Date</small>
                        <strong>{{ optional($report->report_date)->format('d M Y') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Created At</small>
                        <strong>{{ $report->created_at->format('d M Y H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Updated At</small>
                        <strong>{{ $report->updated_at->format('d M Y H:i') }}</strong>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Project Code</small>
                        <strong>{{ optional($report->project)->project_code ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
