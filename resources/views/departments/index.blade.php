@extends('adminlte::page')

@section('title','Departments')

@section('content_header')

<h1>Departments</h1>

@stop

@section('content')

<a href="{{ route('departments.create') }}" class="btn btn-primary mb-3">

Add Department

</a>

@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>

<th>Department</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

@forelse($departments as $department)

<tr>

<td>{{ $department->id }}</td>

<td>{{ $department->department_name }}</td>

<td>

@if($department->status)

<span class="badge bg-success">Active</span>

@else

<span class="badge bg-danger">Inactive</span>

@endif

</td>

<td>

<a href="{{ route('departments.edit',$department->id) }}" class="btn btn-warning btn-sm">

Edit

</a>

<form action="{{ route('departments.destroy',$department->id) }}"
      method="POST"
      style="display:inline;">



      <form action="{{ route('departments.destroy', $department->id) }}"
      method="POST"
      style="display:inline;">

    @csrf
    @method('DELETE')

    <button
        class="btn btn-danger btn-sm"
        onclick="return confirm('Are you sure?')">

        Delete

    </button>

</form>

@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm">

Delete

</button>

</form>

</td>

</tr>

@empty

<tr>

<td colspan="4" class="text-center">

No Departments Found

</td>

</tr>

@endforelse

</tbody>

</table>

@stop