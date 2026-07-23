@extends('adminlte::page')

@section('title', 'New Meeting Minutes')

@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1 text-dark font-weight-bold">New Meeting Minutes</h1>
            <p class="text-muted mb-0">Record meeting details, participants, discussions, decisions, and track action items.</p>
        </div>
        <a href="{{ route('meeting-minutes.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-lg border-0 rounded-lg">
        <div class="card-header bg-light">
            <h3 class="card-title text-primary font-weight-bold"><i class="fas fa-handshake mr-1"></i> MOM Form</h3>
        </div>

        <form action="{{ route('meeting-minutes.store') }}" method="POST" id="mom-form">
            @csrf

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i><strong>Validation errors encountered:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <!-- Title -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Title <span class="text-danger">*</span></label>
                            <input type="text" name="meeting_title" class="form-control shadow-sm" value="{{ old('meeting_title') }}" placeholder="e.g. Weekly Standup, Project Kickoff" required>
                        </div>
                    </div>

                    <!-- Project -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Project</label>
                            <select id="project_id" name="project_id" class="form-control select2 shadow-sm">
                                <option value="">Select Project (General / Internal)</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_name }} {{ $project->project_code ? '(' . $project->project_code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Select Employee (Checklist Progress Filter) -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Select Employee</label>
                            <select id="checklist_user_id" name="checklist_user_id" class="form-control select2 shadow-sm" disabled>
                                <option value="">Select a Project First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <!-- Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Date <span class="text-danger">*</span></label>
                            <input type="date" name="meeting_date" class="form-control shadow-sm" value="{{ old('meeting_date', now()->toDateString()) }}" required>
                        </div>
                    </div>

                    <!-- Time -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Time <span class="text-danger">*</span></label>
                            <input type="time" name="meeting_time" class="form-control shadow-sm" value="{{ old('meeting_time', now()->format('H:i')) }}" required>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Type <span class="text-danger">*</span></label>
                            <select name="meeting_type" id="meeting_type" class="form-control shadow-sm" required>
                                <option value="Online" {{ old('meeting_type') == 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Offline" {{ old('meeting_type', 'Offline') == 'Offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Location / Link</label>
                            <input type="text" name="location" id="location" class="form-control shadow-sm" value="{{ old('location') }}" placeholder="e.g. Conference Room A, Google Meet Link">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <!-- Participants -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Participants <span class="text-danger">*</span></label>
                            <select id="participants" name="participants[]" class="form-control select2 shadow-sm" data-placeholder="Select Attendees" multiple required disabled>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ is_array(old('participants')) && in_array($user->id, old('participants')) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Select users who attended the meeting. Selecting a project will automatically refine this list to project members.</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Agenda & Discussion Points -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Meeting Agenda</label>
                            <textarea name="agenda" rows="4" class="form-control shadow-sm" placeholder="Summarize the core topics on the agenda...">{{ old('agenda') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Discussion Points <span class="text-danger">*</span></label>
                            <textarea name="discussion_points" rows="4" class="form-control shadow-sm" placeholder="Detail the points of discussion during the meeting..." required>{{ old('discussion_points') }}</textarea>
                        </div>
                    </div>
                </div>



                <hr class="my-4">

                <!-- Structured Action Items -->
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="card-title text-secondary font-weight-bold mb-0"><i class="fas fa-tasks mr-1"></i> Tracked Action Items</h4>
                        <button type="button" class="btn btn-success btn-xs shadow-sm ml-auto" id="add-action-row">
                            <i class="fas fa-plus mr-1"></i> Add Action Item
                        </button>
                    </div>

                    <hr class="my-4">

                    <div class="card card-outline card-info shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title text-info font-weight-bold">
                                <i class="fas fa-tasks mr-1"></i>
                                Task Checklist Progress
                            </h3>
                        </div>

                        <div class="card-body">
                            <div id="task-checklists">
                                <div class="text-center text-muted py-4">
                                    Select a project and employee to view task checklists.
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="actions-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50%;">Action Title <span class="text-danger">*</span></th>
                                        <th style="width: 30%;">Assigned To <span class="text-danger">*</span></th>
                                        <th style="width: 20%;">Deadline <span class="text-danger">*</span></th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="no-actions-row">
                                        <td colspan="4" class="text-center py-4 text-muted">No tracked action items added yet. Click "Add Action Item" to register task assignments.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">MOM Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control shadow-sm font-weight-bold text-primary border-primary" required>
                                <option value="Draft" {{ old('status', 'Draft') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Published" {{ old('status') == 'Published' ? 'selected' : '' }}>Published</option>
                               
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted"><i class="fas fa-user-edit mr-1"></i> Recorded by <strong>{{ auth()->user()->name }}</strong></span>
                <div>
                    <a href="{{ route('meeting-minutes.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Save Meeting Minutes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {
    // Initialize select2
    $('.select2').select2({
        width: '100%'
    });

    // Cache of all users for dynamic actions dropdown
    let allUsers = @json($users);

    // Dynamic row addition counter
    let actionIndex = 0;

    function buildUserOptions(membersList = null) {
        let usersToUse = membersList ? membersList : allUsers;
        let options = '<option value="">Select Assignee</option>';
        $.each(usersToUse, function (index, u) {
            let displayName = u.name || u.text || '';
            options += `<option value="${u.id}">${displayName}</option>`;
        });
        return options;
    }

    // Function to add a new action row
    $('#add-action-row').on('click', function () {
        $('.no-actions-row').hide();
        
        // Fetch current project members if a project is selected, else use all users
        let projectId = $('#project_id').val();
        let userSelectOptions = '';
        
        if (projectId && window.currentProjectMembers) {
            userSelectOptions = buildUserOptions(window.currentProjectMembers);
        } else {
            userSelectOptions = buildUserOptions();
        }

        let newRow = `
            <tr class="action-row">
                <td>
                    <input type="text" name="actions[${actionIndex}][action_title]" class="form-control form-control-sm" placeholder="Action item description..." required>
                </td>
                <td>
                    <select name="actions[${actionIndex}][assigned_to]" class="form-control form-control-sm select-assignee" required>
                        ${userSelectOptions}
                    </select>
                </td>
                <td>
                    <input type="date" name="actions[${actionIndex}][deadline]" class="form-control form-control-sm" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-xs remove-row-btn" title="Remove action item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#actions-table tbody').append(newRow);
        actionIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row-btn', function () {
        $(this).closest('tr').remove();
        if ($('#actions-table tbody tr.action-row').length === 0) {
            $('.no-actions-row').show();
        }
    });

    // Project change: Load project-specific users
    $('#project_id').on('change', function () {
        let projectId = $(this).val();
        let participantsSelect = $('#participants');
        
        if (!projectId) {
            window.currentProjectMembers = null;
            // Clear and disable participants dropdown
            participantsSelect.empty().prop('disabled', true).trigger('change');
            
            // Re-build assignee dropdowns on existing rows
            $('.select-assignee').each(function() {
                let currentVal = $(this).val();
                $(this).html(buildUserOptions());
                $(this).val(currentVal);
            });

            // Reset checklist user dropdown
            $('#checklist_user_id').html('<option value="">Select a Project First</option>').prop('disabled', true).trigger('change');
            $('#task-checklists').html('<div class="text-center text-muted py-4">Select a project and employee to view task checklists.</div>');
            return;
        }

        // Fetch members of selected project
        $.ajax({
            url: `/projects/${projectId}/members`,
            type: 'GET',
            dataType: 'json',
            success: function (users) {
                window.currentProjectMembers = users;
                
                // Sync and enable participants dropdown
                participantsSelect.empty();
                $.each(users, function (index, user) {
                    let displayName = user.name || user.text || '';
                    let option = new Option(displayName, user.id, false, false);
                    participantsSelect.append(option);
                });
                participantsSelect.prop('disabled', false).trigger('change');

                // Update assignee dropdowns on existing action items
                $('.select-assignee').each(function() {
                    let currentVal = $(this).val();
                    $(this).html(buildUserOptions(users));
                    // Keep the value if it exists in the new list, else reset
                    if ($(this).find(`option[value="${currentVal}"]`).length > 0) {
                        $(this).val(currentVal);
                    } else {
                        $(this).val('');
                    }
                });

                // Populate and enable the checklist user dropdown
                let checklistUserSelect = $('#checklist_user_id');
                checklistUserSelect.empty().append('<option value="">Select Employee</option>');
                $.each(users, function (index, user) {
                    let displayName = user.name || user.text || '';
                    checklistUserSelect.append(new Option(displayName, user.id, false, false));
                });
                checklistUserSelect.prop('disabled', false).trigger('change');
                $('#task-checklists').html('<div class="text-center text-muted py-4">Select an employee to view task checklists.</div>');
            },
            error: function () {
                alert('Unable to load project members.');
            }
        });
    });

    // User change: Load user-specific checklists
    $('#checklist_user_id').on('change', function () {
        let userId = $(this).val();
        let projectId = $('#project_id').val();
        
        if (!projectId || !userId) {
            $('#task-checklists').html('<div class="text-center text-muted py-4">Select an employee to view task checklists.</div>');
            return;
        }

        $.ajax({
            url: `/projects/${projectId}/task-checklists?user_id=${userId}`,
            type: 'GET',
            dataType: 'json',
            success: function(tasks){
                let html = '';
                if(tasks.length === 0){
                    html = `
                        <div class="alert alert-info mb-0">
                            No task checklists found for this project and employee.
                        </div>
                    `;
                }else{
                    tasks.forEach(function(task){
                        html += `
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong>${task.task_name}</strong>
                                </div>
                                <div class="card-body">
                        `;
                        task.checklists.forEach(function(item){
                            let isChecked = item.is_completed ? 'checked' : '';
                            html += `
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="completed_checklists[]"
                                        value="${item.id}"
                                        ${isChecked}
                                    >
                                    <label class="form-check-label">
                                        ${item.checklist_item}
                                    </label>
                                </div>
                            `;
                        });
                        html += `
                                </div>
                            </div>
                        `;
                    });
                }
                $('#task-checklists').html(html);
            }
        });
    });
});
</script>
@stop