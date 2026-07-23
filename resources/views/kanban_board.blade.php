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
    align-items: flex-start;
}

.column{
    min-width: 280px;
    max-width: 320px;
    flex: 1;
    background: #f1f5f9;
    border-radius: 10px;
    padding: 12px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

.column-title{
    padding: 10px 15px;
    border-radius: 8px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.Pending{
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.Progress{
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.Completed{
    background: linear-gradient(135deg, #10b981, #059669);
}

.Submitted{
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.task-list{
    min-height: 400px;
}

.task{
    background: #fff;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
    cursor: grab;
    position: relative;
    border-left: 3px solid #cbd5e1;
}

.task:hover{
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border-color: #cbd5e1;
}

.task-list[data-status="Pending"] .task { border-left-color: #f59e0b; }
.task-list[data-status="In Progress"] .task { border-left-color: #3b82f6; }
.task-list[data-status="Submitted"] .task { border-left-color: #8b5cf6; }
.task-list[data-status="Completed"] .task { border-left-color: #10b981; }

.task-title{
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.4;
}

.task-desc{
    font-size: 12px;
    color: #64748b;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.task-footer{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
}

.badge-priority{
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    background: #f1f5f9;
    color: #475569;
}

.task-id{
    font-size: 11px;
    color: #94a3b8;
    font-family: monospace;
}

.task-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
}

.task-tags .badge {
    font-size: 10px;
    padding: 3px 6px;
    font-weight: 500;
}

</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-lg border-0 rounded-lg overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 1rem;">
            <div class="card-header bg-light border-0 py-3">
                <h3 class="card-title text-primary font-weight-bold mb-0">
                    <i class="fas fa-chart-pie mr-2"></i> Task Status Distribution
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Legend Details -->
                    <div class="col-md-5 mb-4 mb-md-0">
                        <div class="p-3 rounded-lg bg-light shadow-sm">
                            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-info-circle text-muted mr-1"></i> Statistics Summary</h5>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#ffc107;"></span> Pending Tasks
                                </span>
                                <span class="badge badge-warning font-weight-bold px-3 py-2 text-dark" style="font-size:0.9rem;">{{ $pendingTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#17a2b8;"></span> In Progress Tasks
                                </span>
                                <span class="badge badge-info font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $inProgressTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#007bff;"></span> Review Tasks
                                </span>
                                <span class="badge badge-primary font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $submittedTasks }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pb-1">
                                <span class="d-flex align-items-center text-muted font-weight-bold">
                                    <span class="mr-2" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#28a745;"></span> Completed Tasks
                                </span>
                                <span class="badge badge-success font-weight-bold px-3 py-2" style="font-size:0.9rem;">{{ $completedTasks }}</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 font-weight-bold text-dark mb-0">Total Tracked Tasks</span>
                                <span class="badge badge-dark font-weight-bold px-3 py-2" style="font-size:1rem; border-radius: 0.5rem;">{{ $totalTasks }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Pie Chart -->
                    <div class="col-md-7 d-flex justify-content-center align-items-center">
                        <div style="position: relative; width: 100%; max-width: 320px; aspect-ratio: 1 / 1;">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            const ctx = document.getElementById('taskStatusChart').getContext('2d');
            
            const pendingCount = {{ $pendingTasks }};
            const inProgressCount = {{ $inProgressTasks }};
            const submittedCount = {{ $submittedTasks }};
            const completedCount = {{ $completedTasks }};
            
            const totalCount = pendingCount + inProgressCount + submittedCount + completedCount;
            
            const dataValues = totalCount > 0 
                ? [pendingCount, inProgressCount, submittedCount, completedCount] 
                : [1];
            const dataLabels = totalCount > 0 
                ? ['Pending', 'In Progress', 'Review', 'Completed'] 
                : ['No Tasks'];
            const bgColors = totalCount > 0 
                ? ['#ffc107', '#17a2b8', '#007bff', '#28a745'] 
                : ['#e2e8f0'];
            const hoverBgColors = totalCount > 0 
                ? ['#e0a800', '#138496', '#0069d9', '#218838'] 
                : ['#cbd5e1'];

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: dataLabels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: bgColors,
                        hoverBackgroundColor: hoverBgColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: totalCount > 0,
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw;
                                    const pct = ((value / totalCount) * 100).toFixed(1);
                                    return ` ${context.label}: ${value} (${pct}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });

</script>

@stop

