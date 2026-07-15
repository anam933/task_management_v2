<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $departments = Department::latest()->get();

    return view('departments.index', compact('departments'));
}

    /**
     * Show the form for creating a new resource.
     */
  public function create()
{
    return view('departments.create');
}

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'department_name' => 'required|string|max:255|unique:departments,department_name',
        'description' => 'nullable|string',
        'status' => 'required|boolean',
    ]);

    Department::create([
        'department_name' => $request->department_name,
        'description'     => $request->description,
        'status'          => $request->status,
    ]);

    return redirect()
        ->route('departments.index')
        ->with('success', 'Department Added Successfully');
}

    /**
     * Display the specified resource.
     */
  public function show(string $id)
{
    $department = Department::findOrFail($id);

    return view('departments.show', compact('department'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
{
    return view('departments.edit', compact('department'));
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Department $department)
{
    $request->validate([
        'department_name' => 'required|string|max:255|unique:departments,department_name,' . $department->id,
        'status' => 'required|boolean',
    ]);

    $department->update([
        'department_name' => $request->department_name,
        'status' => $request->status,
    ]);

    return redirect()
        ->route('departments.index')
        ->with('success', 'Department Updated Successfully');
}

    /**
     * Remove the specified resource from storage.
     */
  public function destroy(Department $department)
{
    $department->delete();

    return redirect()
        ->route('departments.index')
        ->with('success', 'Department Deleted Successfully');
}
}
