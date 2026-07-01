@extends('adminlte::page')

@section('title', 'Task Details')

@section('content_header')
    <h1>Task Details</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3>{{ $task->task_name }}</h3>
    </div>

    <div class="card-body">

        <p><b>Description:</b> {{ $task->task_details }}</p>

        <p><b>Assigned By:</b> {{ optional($task->assignedByUser)->name ?? 'System' }}</p>

        <p><b>Assigned To:</b> {{ optional($task->assignedUser)->name ?? 'Unassigned' }}</p>

        <p><b>Tags:</b>
            @forelse($task->tags as $tag)
                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
            @empty
                <span class="text-muted">No tags assigned</span>
            @endforelse
        </p>

        <p><b>Priority:</b> {{ $task->priority }}</p>

        <p><b>Status:</b> {{ $task->status }}</p>

        <p><b>Start Date:</b> {{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</p>

        <p><b>Deadline:</b> {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}</p>

        <p><b>Created At:</b> {{ $task->created_at }}</p>

    </div>
</div>

@stop
