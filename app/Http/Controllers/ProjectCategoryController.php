<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-project-categories');
    }

    public function index()
    {
        $categories = ProjectCategory::latest()->get();

        return view('Project_category.index', compact('categories'));
    }

    public function create()
    {
        return view('Project_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:project_categories,category_name',
            'description' => 'nullable|string',
        ]);

        ProjectCategory::create($request->only([
            'category_name',
            'description',
        ]));

        return redirect()
            ->route('Project_category.index')
            ->with('success', 'Project Category Created Successfully');
    }

    public function show(ProjectCategory $Project_category)
    {
        return redirect()->route('Project_category.index');
    }

    public function edit(ProjectCategory $Project_category)
    {
        return view('Project_category.edit', compact('Project_category'));
    }

    public function update(Request $request, ProjectCategory $Project_category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:project_categories,category_name,' . $Project_category->id,
            'description' => 'nullable|string',
        ]);

        $Project_category->update($request->only([
            'category_name',
            'description',
        ]));

        return redirect()
            ->route('Project_category.index')
            ->with('success', 'Project Category Updated Successfully');
    }

    public function destroy(ProjectCategory $Project_category)
    {
        $Project_category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Project Category Deleted Successfully',
            ]);
        }

        return redirect()
            ->route('Project_category.index')
            ->with('success', 'Project Category Deleted Successfully');
    }
}