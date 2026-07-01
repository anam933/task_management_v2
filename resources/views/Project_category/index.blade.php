@extends('adminlte::page')

@section('title', 'Project Category')

@section('content')

<div class="container mt-3">

    <div class="d-flex justify-content-between mb-3">
        <h3>Project Category</h3>

        @can('manage-project-categories')
            <a href="{{ route('Project_category.create') }}" class="btn btn-primary">
                + Add Category
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>

                        @can('manage-project-categories')
                            <th>Action</th>
                        @endcan
                    </tr>
                </thead>

                <tbody>

                    @forelse($categories as $category)

                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->category_name }}</td>
                            <td>{{ $category->description }}</td>

                            @can('manage-project-categories')
                                <td>

                                    <a href="{{ route('Project_category.edit', $category->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('Project_category.destroy', $category->id) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this category?')">
                                            Delete
                                        </button>

                                    </form>

                                </td>
                            @endcan

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center">
                                No Project Categories Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection