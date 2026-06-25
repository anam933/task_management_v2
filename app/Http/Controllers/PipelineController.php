<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function kanbanBoard()
    {
        $tasks = Task::all();
        return view('kanban_board', compact('tasks'));
    }

    public function updateStatus(Request $request)
    {
        $task = Task::find($request->task_id);

        if ($task) {
            $task->status = $request->status;
            $task->save();
        }

        return response()->json(['success' => true]);
    }
    public function taskDetails($id)
        {
            $task = Task::findOrFail($id);

            return view('task_details', compact('task'));
        }
}