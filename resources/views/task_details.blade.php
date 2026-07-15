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

        <p>
            <b>Tags:</b>

            @forelse($task->tags as $tag)
                <span class="badge"
                      style="background-color: {{ $tag->color }}; color:#fff;">
                    {{ $tag->name }}
                </span>
            @empty
                <span class="text-muted">No tags assigned</span>
            @endforelse
        </p>

        <p><b>Priority:</b> {{ $task->priority }}</p>

       <p>
    <b>Status:</b>

    @if($task->status == 'Pending')
        <span class="badge badge-warning">Pending</span>

    @elseif($task->status == 'In Progress')
        <span class="badge badge-primary">In Progress</span>

    @elseif($task->status == 'Submitted')
        <span class="badge badge-info">Submitted</span>

    @elseif($task->status == 'Completed')
        <span class="badge badge-success">Completed</span>

    @endif
</p>
        <p><b>Start Date:</b> {{ \Carbon\Carbon::parse($task->start_date)->format('d M Y') }}</p>

        <p><b>Deadline:</b> {{ \Carbon\Carbon::parse($task->deadline_date)->format('d M Y') }}</p>

        <p><b>Created At:</b> {{ $task->created_at }}</p>

        @if($task->review_comment)
            <div class="alert alert-warning mt-3">
                <h5><i class="fas fa-exclamation-circle"></i> Manager's Review Comment</h5>
                <p class="mb-0">{{ $task->review_comment }}</p>
            </div>
        @endif

        @if($task->submission_remarks)
            <div class="alert alert-info mt-3">
                <h5><i class="fas fa-info-circle"></i> Submission Remarks</h5>
                <p class="mb-0">{{ $task->submission_remarks }}</p>
                @if($task->attachments && $task->attachments->count() > 0)
                    <div class="mt-3">
                        <h6>Attachments:</h6>
                        <ul class="list-unstyled">
                            @foreach($task->attachments as $attachment)
                                <li>
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" download>
                                        <i class="fas fa-paperclip"></i> {{ $attachment->file_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <hr>

        <!-- ================= TASK ACTIONS ================= -->

<div class="mb-4">

    {{-- ================= Employee Actions ================= --}}
    @if((int)$task->assigned_to === (int)auth()->id())

        {{-- Pending --}}
        @if($task->status == 'Pending')

            <form action="{{ route('tasks.start', $task->id) }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-play"></i>
                    Start Task
                </button>
            </form>

        {{-- In Progress --}}
        @elseif($task->status == 'In Progress')

            <button
                type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#submitTaskModal">

                <i class="fas fa-paper-plane"></i>
                Submit for Review

            </button>

            <!-- Submit Task Modal -->
            <div class="modal fade"
                id="submitTaskModal"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <form action="{{ route('tasks.submit', $task->id) }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf

                            <div class="modal-header">

                                <h5 class="modal-title">
                                    Submit Task
                                </h5>

                                <button type="button"
                                        class="close"
                                        data-dismiss="modal">

                                    <span>&times;</span>

                                </button>

                            </div>

                            <div class="modal-body">

                                <div class="form-group">

                                    <label>
                                        Submission Remarks
                                    </label>

                                    <textarea
                                        name="submission_remarks"
                                        class="form-control"
                                        rows="3"
                                        required></textarea>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Attachments
                                    </label>

                                    <input
                                        type="file"
                                        name="attachments[]"
                                        class="form-control-file"
                                        multiple>

                                    <small class="text-muted">
                                        PDF, DOC, Excel, Images, ZIP (Max 10MB)
                                    </small>

                                </div>

                            </div>

                            <div class="modal-footer">

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal">

                                    Cancel

                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-primary">

                                    Submit

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        {{-- Submitted --}}
        @elseif($task->status == 'Submitted')

            <div class="alert alert-info mb-0">
                <i class="fas fa-clock"></i>
                Task submitted successfully.
                Waiting for Reporting Manager approval.
            </div>

        {{-- Completed --}}
        @elseif($task->status == 'Completed')

            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle"></i>
                This task has been completed.
            </div>
          

        @endif   {{-- Status if close --}}

@endif   {{-- Employee if close --}}


    



    {{-- ================= Manager Actions ================= --}}

    @if(
        ((int)$task->reports_to === (int)auth()->id() || auth()->user()->hasRole('admin'))
        && $task->status == 'Submitted'
    )

        <div class="card card-outline card-primary mt-3">

            <div class="card-header">

                <h3 class="card-title">
                    Manager Actions
                </h3>

            </div>

            <div class="card-body">

                <div class="mb-3 d-flex" style="gap:10px;">

                    <form action="{{ route('manager.tasks.approve', $task->id) }}"
                          method="POST">

                        @csrf

                        <button
                         type="submit"
                            class="btn btn-success"
                            onclick="return confirm('Approve this task?')">

                            <i class="fas fa-check-double"></i>

                            Approve

                        </button>

                    </form>

                    <button
                        type="button"
                        class="btn btn-danger"
                        onclick="document.getElementById('reject-form-container').style.display='block'">

                        <i class="fas fa-times"></i>

                        Reject

                    </button>

                </div>

                <div id="reject-form-container"
                     style="display:none;">

                    <form action="{{ route('manager.tasks.reject', $task->id) }}"
                          method="POST">

                        @csrf

                        <div class="form-group">

                            <label>
                                Rejection Comment
                            </label>

                            <textarea
                                name="review_comment"
                                class="form-control"
                                rows="3"
                                required></textarea>

                        </div>

                        <button
                            class="btn btn-danger">

                            Confirm Reject

                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            onclick="document.getElementById('reject-form-container').style.display='none'">

                            Cancel

                        </button>

                    </form>

                </div>

            </div>

        </div>

    @endif

</div>

<hr>

<h4 class="mb-3">
    <i class="fas fa-list-check"></i>
    Task Checklist
</h4>

@if($task->checklists->count())

<div class="list-group">

    @foreach($task->checklists as $item)

    <div class="list-group-item d-flex justify-content-between align-items-center">

        <div>

            <form action="{{ route('task-checklists.toggle', $item->id) }}"
                  method="POST"
                  style="display:inline;">

                @csrf
                @method('PATCH')

                <input
                    type="checkbox"
                    onchange="this.form.submit()"
                    {{ $item->is_completed ? 'checked' : '' }}>

            </form>

            <span class="{{ $item->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                {{ $item->checklist_item }}
            </span>

        </div>

    </div>

    @endforeach

</div>

@else

<div class="alert alert-info">
    No checklist items available.
</div>

@endif

</div> {{-- card-body --}}
</div> {{-- card --}}

@stop