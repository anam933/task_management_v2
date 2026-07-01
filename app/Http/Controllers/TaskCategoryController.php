<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-task-categories');
    }

    public function index()
    {
        $categories = TaskCategory::latest()->get();

        return view('Task_category.index', compact('categories'));
    }

    public function create()
    {
        return view('Task_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:task_categories,category_name',
            'description' => 'nullable|string',
        ]);

        TaskCategory::create($request->only([
            'category_name',
            'description',
        ]));

        return redirect()
            ->route('Task_category.index')
            ->with('success', 'Task Category Created Successfully');
    }

    public function edit(TaskCategory $Task_category)
    {
        return view('Task_category.edit', compact('Task_category'));
    }

    public function show(TaskCategory $Task_category)
    {
        return redirect()->route('Task_category.index');
    }

    public function update(Request $request, TaskCategory $Task_category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:task_categories,category_name,' . $Task_category->id,
            'description' => 'nullable|string',
        ]);

        $Task_category->update($request->only([
            'category_name',
            'description',
        ]));

        return redirect()
            ->route('Task_category.index')
            ->with('success', 'Task Category Updated Successfully');
    }

    public function destroy(TaskCategory $Task_category)
    {
        $Task_category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Task Category Deleted Successfully',
            ]);
        }

        return redirect()
            ->route('Task_category.index')
            ->with('success', 'Task Category Deleted Successfully');
    }
}
