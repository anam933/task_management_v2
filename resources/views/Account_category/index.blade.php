@extends('adminlte::page')

@section('title', 'Account Category')

@section('content')

<div class="container mt-3">

    <div class="d-flex justify-content-between mb-3">
        <h3>Account Category</h3>
        @can('manage-account-categories')
        <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">
            + Add Category
        </button>
        @endcan
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control"
               placeholder="Search category...">
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARDS --}}
    <div class="row">

        @foreach($category as $cat)
        <div class="col-md-4">

            <div class="card card-outline card-primary">

                <div class="card-header">
                    <h5>{{ $cat->category_name }}</h5>
                </div>

                <div class="card-body">
                    <p>{{ $cat->description }}</p>

                    <span class="badge {{ $cat->status === 'Active' ? 'badge-success' : 'badge-danger' }}">
                        {{ $cat->status }}
                    </span>
                </div>

                <div class="card-footer d-flex justify-content-between">

                    @can('manage-account-categories')
                    <button class="btn btn-warning btn-sm"
                        data-toggle="modal"
                        data-target="#editModal{{ $cat->id }}">
                        Edit
                    </button>

                    <button class="btn btn-danger btn-sm deleteBtn"
                        data-id="{{ $cat->id }}">
                        Delete
                    </button>
                    @endcan

                </div>

            </div>

        </div>

        {{-- EDIT MODAL --}}
        @can('manage-account-categories')
        <div class="modal fade" id="editModal{{ $cat->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="POST" action="{{ route('Account_category.update', $cat->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5>Edit Category</h5>
                        </div>

                        <div class="modal-body">

                            <input type="text" name="category_name"
                                   value="{{ $cat->category_name }}"
                                   class="form-control mb-2">

                            <select name="category_type" class="form-control mb-2">
                                <option value="">Select Type</option>
                                <option value="Asset" {{ $cat->category_type === 'Asset' ? 'selected' : '' }}>Asset</option>
                                <option value="Income" {{ $cat->category_type === 'Income' ? 'selected' : '' }}>Income</option>
                                <option value="Expense" {{ $cat->category_type === 'Expense' ? 'selected' : '' }}>Expense</option>
                                <option value="Liability" {{ $cat->category_type === 'Liability' ? 'selected' : '' }}>Liability</option>
                            </select>

                            <textarea name="description"
                                      class="form-control">{{ $cat->description }}</textarea>

                            <select name="status" class="form-control mt-2">
                                <option value="Active" {{ $cat->status === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ $cat->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Update</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
        @endcan

        @endforeach

    </div>

</div>

{{-- ADD MODAL --}}
@can('manage-account-categories')
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('Account_category.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Category</h5>
                </div>

                <div class="modal-body">

                    <input type="text" name="category_name"
                           class="form-control mb-2"
                           placeholder="Category Name"
                           value="{{ old('category_name') }}">

                    <select name="category_type" class="form-control mb-2">
                        <option value="">Select Type</option>
                        <option value="Asset" {{ old('category_type') === 'Asset' ? 'selected' : '' }}>Asset</option>
                        <option value="Income" {{ old('category_type') === 'Income' ? 'selected' : '' }}>Income</option>
                        <option value="Expense" {{ old('category_type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                        <option value="Liability" {{ old('category_type') === 'Liability' ? 'selected' : '' }}>Liability</option>
                    </select>


                    <textarea name="description"
                              class="form-control"
                              placeholder="Description">{{ old('description') }}</textarea>

                    <select name="status" class="form-control mt-2">
                        <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endcan

@endsection

{{-- AJAX DELETE --}}
@push('js')
<script>
document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', function () {

        if (!confirm('Delete this category?')) return;

        fetch('/Account_category/' + this.dataset.id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(() => location.reload());

    });
});
</script>
@endpush
