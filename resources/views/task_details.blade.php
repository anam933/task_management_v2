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

        <p><b>Assigned By:</b> {{ $task->assigned_by }}</p>

        <p><b>Assigned To:</b> {{ $task->assigned_to }}</p>

        <p><b>Priority:</b> {{ $task->priority }}</p>

        <p><b>Status:</b> {{ $task->status }}</p>

        <p><b>Start Date:</b> {{ $task->start_date }}</p>

        <p><b>Deadline:</b> {{ $task->deadline }}</p>

        <p><b>Created At:</b> {{ $task->created_at }}</p>

    </div>
</div>

@stop