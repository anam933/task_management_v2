@extends('adminlte::page')

@section('title', 'Add Task')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Add New Task</h1>
            <p class="text-muted mb-0">Create a task and assign its category, owner, and timeline.</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Back to Tasks</a>
    </div>
@stop

@section('content')

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Task Details</h3>
        <span class="badge badge-primary p-2">All required fields must be completed</span>
    </div>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

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
                               value="{{ old('task_name') }}"
                               placeholder="Enter task title"
                               required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-control form-control-lg">
                            <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('priority', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High</option>
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
                                <option value="{{ $category->id }}" {{ old('task_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Every task should have one category.</small>
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
                        <small class="text-muted">Hold Ctrl or Cmd to pick multiple tags.</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Project <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-control form-control-lg" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }} ({{ $project->project_code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Each task must be linked to a project.</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date"
                               name="start_date"
                               class="form-control form-control-lg"
                               value="{{ old('start_date') }}"
                               required>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Deadline <span class="text-danger">*</span></label>
                        <input type="date"
                               name="deadline_date"
                               class="form-control form-control-lg"
                               value="{{ old('deadline_date') }}"
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
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                            <option value="Pending" {{ old('status', 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Task Details</label>
                <textarea name="task_details"
                          class="form-control"
                          rows="5"
                          placeholder="Add notes, acceptance criteria, or implementation details">{{ old('task_details') }}</textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted">Tip: Use a clear title and a specific category.</span>
            <div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Save Task</button>
            </div>
        </div>
    </form>
</div>

@stop
