@extends('adminlte::page')

@section('title', 'Add Account Category')

@section('content_header')
    <h1>Add Account Category</h1>
@stop

@section('content')

<div class="card card-primary">

    <div class="card-header">
        <h3 class="card-title">Create New Category</h3>
    </div>

    <form action="{{ route('Account_category.store') }}" method="POST">

        @csrf

        <div class="card-body">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Category Name --}}
            <div class="form-group">
                <label>Category Name <span class="text-danger">*</span></label>
                <input type="text"
                       name="category_name"
                       class="form-control"
                       value="{{ old('category_name') }}"
                       placeholder="Enter Category Name"
                       required>
            </div>

            {{-- Category Type --}}
            <div class="form-group">
                <label>Category Type <span class="text-danger">*</span></label>

                <select name="category_type"
                        class="form-control"
                        required>

                    <option value="">Select Type</option>
                    <option value="Asset" {{ old('category_type') === 'Asset' ? 'selected' : '' }}>Asset</option>
                    <option value="Income" {{ old('category_type') === 'Income' ? 'selected' : '' }}>Income</option>
                    <option value="Expense" {{ old('category_type') === 'Expense' ? 'selected' : '' }}>Expense</option>
                    <option value="Liability" {{ old('category_type') === 'Liability' ? 'selected' : '' }}>Liability</option>

                </select>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label>Description</label>

                <textarea name="description"
                          rows="4"
                          class="form-control"
                          placeholder="Enter Description">{{ old('description') }}</textarea>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label>Status</label>

                <select name="status"
                        class="form-control">

                    <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>

                </select>
            </div>

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save Category
            </button>

            <a href="{{ route('Account_category.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</div>

@stop
