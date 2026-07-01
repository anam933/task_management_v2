@php
    $project = $project ?? null;
    $selectedTeamMembers = array_map('intval', old('team_members', $project ? $project->teamMembers->pluck('id')->all() : []));
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="form-group">
            <label>Project Name <span class="text-danger">*</span></label>
            <input type="text"
                   name="project_name"
                   class="form-control form-control-lg"
                   value="{{ old('project_name', $project->project_name ?? '') }}"
                   placeholder="Enter project name"
                   required>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label>Project Code <span class="text-danger">*</span></label>
            <input type="text"
                   name="project_code"
                   class="form-control form-control-lg"
                   value="{{ old('project_code', $project->project_code ?? '') }}"
                   placeholder="PRJ-1001"
                   required>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="project_description"
              class="form-control"
              rows="4"
              placeholder="Project objectives, scope, or business notes">{{ old('project_description', $project->project_description ?? '') }}</textarea>
</div>


<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label>Project Category <span class="text-danger">*</span></label>

            <select name="category_id" class="form-control form-control-lg" required>
    <option value="">Select Project Category</option>

    @foreach($categories as $category)
        <option value="{{ $category->id }}"
            {{ old('category_id', $project->category_id ?? '') == $category->id ? 'selected' : '' }}>
            {{ $category->category_name }}
        </option>
    @endforeach
</select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label>Project Manager <span class="text-danger">*</span></label>
            <select name="project_manager_id" class="form-control form-control-lg" required>
                <option value="">Select Manager</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('project_manager_id', $project->project_manager_id ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label>Project Status <span class="text-danger">*</span></label>
            <select name="project_status" class="form-control form-control-lg" required>
                <option value="Planning" {{ old('project_status', $project->project_status ?? 'Planning') === 'Planning' ? 'selected' : '' }}>Planning</option>
                <option value="Active" {{ old('project_status', $project->project_status ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="On Hold" {{ old('project_status', $project->project_status ?? '') === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                <option value="Completed" {{ old('project_status', $project->project_status ?? '') === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ old('project_status', $project->project_status ?? '') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label>Priority <span class="text-danger">*</span></label>
            <select name="priority" class="form-control form-control-lg" required>
                <option value="Low" {{ old('priority', $project->priority ?? 'Medium') === 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ old('priority', $project->priority ?? 'Medium') === 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ old('priority', $project->priority ?? '') === 'High' ? 'selected' : '' }}>High</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label>Start Date <span class="text-danger">*</span></label>
                   <input type="date"
                   name="start_date"
                   class="form-control form-control-lg"
                   value="{{ old('start_date', $project ? optional($project->start_date)->format('Y-m-d') : '') }}"
                   required>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label>End Date</label>
                   <input type="date"
                   name="end_date"
                   class="form-control form-control-lg"
                   value="{{ old('end_date', $project ? optional($project->end_date)->format('Y-m-d') : '') }}">
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label>Budget</label>
            <input type="number"
                   name="budget"
                   step="0.01"
                   min="0"
                   class="form-control form-control-lg"
                   value="{{ old('budget', $project->budget ?? '') }}"
                   placeholder="0.00">
        </div>
    </div>
</div>

<div class="form-group">
    <label>Team Members</label>
    <select name="team_members[]"
            class="form-control"
            multiple
            size="6">
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ in_array((int) $user->id, $selectedTeamMembers, true) ? 'selected' : '' }}>
                {{ $user->name }} @if($user->role) ({{ $user->role }}) @endif
            </option>
        @endforeach
    </select>
    <small class="text-muted">Hold Ctrl/Command to select multiple users.</small>
</div>
