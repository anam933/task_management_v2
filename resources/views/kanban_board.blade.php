@extends('adminlte::page')

@section('title', 'Task Board')

@section('content_header')
    <h1>Task Board</h1>
@stop

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<style>

.board{
    display:flex;
    gap:20px;
    padding:20px;
    overflow-x:auto;
}





.column{
    min-width:350px;
    flex:1;
    background:#f8f9fa;
    border-radius:15px;
    padding:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.column-title{
    padding:15px;
    border-radius:12px;
    color:#fff;
    font-size:18px;
    font-weight:600;
    text-align:center;
    margin-bottom:15px;
}

.Pending{
    background:linear-gradient(135deg,#ff6b6b,#ee5253);
}

.Progress{
    background:linear-gradient(135deg,#feca57,#ff9f43);
}

.Completed{
    background:linear-gradient(135deg,#1dd1a1,#10ac84);
}

.task-list{
    min-height:500px;
}

.task{
    background:#fff;
    border-radius:15px;
    padding:15px;
    margin-bottom:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:all .3s ease;
    cursor:grab;
}

.task:hover{
    transform:translateY(-5px);
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

.task-title{
    font-size:16px;
    font-weight:600;
    color:#2d3436;
    margin-bottom:8px;
}

.task-desc{
    font-size:13px;
    color:#636e72;
    margin-bottom:10px;
}

.task-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.badge-priority{
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    background:#e3f2fd;
    color:#1976d2;
}

.task-id{
    font-size:12px;
    color:#95a5a6;
}

</style>

<div class="board">

    {{-- Pending --}}
    <div class="column">

        <div class="column-title Pending">
            Pending
            ({{ $tasks->where('status','Pending')->count() }})
        </div>

        <div class="task-list" data-status="Pending">

            @foreach($tasks as $task)

                @if($task->status == 'Pending')

                <a href="{{ route('task.details',$task->id) }}"
                   style="text-decoration:none;">

                    <div class="task" data-id="{{ $task->id }}">

                        <div class="task-title">
                            {{ $task->task_name }}
                        </div>

                        <div class="task-desc">
                            {{ Str::limit($task->task_details,80) }}
                        </div>

                        <div class="task-desc">
                            @forelse($task->tags->take(3) as $tag)
                                <span class="badge mr-1 mb-1" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                            @empty
                                <span class="text-muted">No tags</span>
                            @endforelse
                        </div>

                        <div class="task-footer">

                            <span class="badge-priority">
                                {{ $task->priority ?? 'Medium' }}
                            </span>

                            <span class="task-id">
                                #{{ $task->id }}
                            </span>

                        </div>

                        <div class="task-desc" style="margin-top:8px;">
                            <strong>Assigned by:</strong> {{ optional($task->assignedByUser)->name ?? 'System' }}<br>
                            <strong>Assigned to:</strong> {{ optional($task->assignedUser)->name ?? 'Unassigned' }}
                        </div>

                    </div>

                </a>

                @endif

            @endforeach

        </div>

    </div>

    {{-- Progress --}}
    <div class="column">

        <div class="column-title Progress">
            In Progress
            ({{ $tasks->where('status','In Progress')->count() }})
        </div>

        <div class="task-list" data-status="In Progress">

            @foreach($tasks as $task)

                @if($task->status == 'In Progress')

                <a href="{{ route('task.details',$task->id) }}"
                   style="text-decoration:none;">

                    <div class="task" data-id="{{ $task->id }}">

                        <div class="task-title">
                            {{ $task->task_name }}
                        </div>

                        <div class="task-desc">
                            {{ Str::limit($task->task_details,80) }}
                        </div>

                        <div class="task-desc">
                            @forelse($task->tags->take(3) as $tag)
                                <span class="badge mr-1 mb-1" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                            @empty
                                <span class="text-muted">No tags</span>
                            @endforelse
                        </div>

                        <div class="task-footer">

                            <span class="badge-priority">
                                {{ $task->priority ?? 'Medium' }}
                            </span>

                            <span class="task-id">
                                #{{ $task->id }}
                            </span>

                        </div>

                        <div class="task-desc" style="margin-top:8px;">
                            <strong>Assigned by:</strong> {{ optional($task->assignedByUser)->name ?? 'System' }}<br>
                            <strong>Assigned to:</strong> {{ optional($task->assignedUser)->name ?? 'Unassigned' }}
                        </div>

                    </div>

                </a>

                @endif

            @endforeach

        </div>

    </div>

    {{-- Completed --}}
    <div class="column">

        <div class="column-title Completed">
            Completed
            ({{ $tasks->where('status','Completed')->count() }})
        </div>

        <div class="task-list" data-status="Completed">

            @foreach($tasks as $task)

                @if($task->status == 'Completed')

                <a href="{{ route('task.details',$task->id) }}"
                   style="text-decoration:none;">

                    <div class="task" data-id="{{ $task->id }}">

                        <div class="task-title">
                            {{ $task->task_name }}
                        </div>

                        <div class="task-desc">
                            {{ Str::limit($task->task_details,80) }}
                        </div>

                        <div class="task-desc">
                            @forelse($task->tags->take(3) as $tag)
                                <span class="badge mr-1 mb-1" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                            @empty
                                <span class="text-muted">No tags</span>
                            @endforelse
                        </div>

                        <div class="task-footer">

                            <span class="badge-priority">
                                {{ $task->priority ?? 'Medium' }}
                            </span>

                            <span class="task-id">
                                #{{ $task->id }}
                            </span>

                        </div>

                        <div class="task-desc" style="margin-top:8px;">
                            <strong>Assigned by:</strong> {{ optional($task->assignedByUser)->name ?? 'System' }}<br>
                            <strong>Assigned to:</strong> {{ optional($task->assignedUser)->name ?? 'Unassigned' }}
                        </div>

                    </div>

                </a>

                @endif

            @endforeach

        </div>

    </div>

</div>

<script>

document.querySelectorAll('.task-list').forEach(list => {

    new Sortable(list,{
        group:'tasks',
        animation:150,

        onEnd:function(evt){

            let taskId = evt.item.getAttribute('data-id');
            let newStatus = evt.to.getAttribute('data-status');

            fetch('/task-update-status',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },

                body:JSON.stringify({
                    task_id:taskId,
                    status:newStatus
                })

            })
            .then(response => response.json())
            .then(data => console.log(data));

        }
    });

});

</script>

@stop

