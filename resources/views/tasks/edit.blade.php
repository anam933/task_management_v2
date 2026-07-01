@extends('adminlte::page')

@section('title', 'Edit Task')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Edit Task</h1>
            <p class="text-muted mb-0">Update task details, category, assignee, and progress.</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Back to Tasks</a>
    </div>
@stop

@section('content')

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Task Details</h3>
        <span class="badge badge-info p-2">Editing #{{ $task->id }}</span>
    </div>

    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following issues:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label>Task Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="task_name"
                               class="form-control form-control-lg"
                               value="{{ old('task_name', $task->task_name) }}"
                               required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-control form-control-lg">
                            <option value="Low" {{ old('priority', $task->priority) === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('priority', $task->priority) === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('priority', $task->priority) === 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Task Category <span class="text-danger">*</span></label>
                        <select name="task_category_id" class="form-control form-control-lg" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('task_category_id', $task->task_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Tags</label>
                        <select name="tag_ids[]" class="form-control form-control-lg" multiple size="5">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', $selectedTagIds ?? [])) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select one or more tags for quick grouping.</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control form-control-lg" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $projectOption)
                                <option value="{{ $projectOption->id }}" {{ old('project_id', $task->project_id) == $projectOption->id ? 'selected' : '' }}>
                                    {{ $projectOption->project_name }} ({{ $projectOption->project_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date"
                               name="start_date"
                               class="form-control form-control-lg"
                               value="{{ old('start_date', $task->start_date) }}"
                               required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Deadline <span class="text-danger">*</span></label>
                        <input type="date"
                               name="deadline_date"
                               class="form-control form-control-lg"
                               value="{{ old('deadline_date', $task->deadline_date) }}"
                               required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="assigned_to" class="form-control form-control-lg">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control form-control-lg">
                            <option value="Pending" {{ old('status', $task->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status', $task->status) === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status', $task->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Task Details</label>
                <textarea name="task_details"
                          class="form-control"
                          rows="5"
                          placeholder="Add notes, acceptance criteria, or implementation details">{{ old('task_details', $task->task_details) }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Keep the category aligned with the task scope.</span>
            <div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Update Task</button>
            </div>
        </div>
    </form>
</div>

@stop
