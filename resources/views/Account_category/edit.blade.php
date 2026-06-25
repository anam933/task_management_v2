@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <h1>Edit Category</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('Account_category.update', $account_category->id) }}"
      method="POST">

@csrf
@method('PUT')

<div class="form-group">

<label>Category Name</label>

<input type="text"
       name="category_name"
       value="{{ $account_category->category_name }}"
       class="form-control">

</div>

<div class="form-group">

<label>Category Type</label>

<select name="category_type"
        class="form-control">

<option value="Asset" {{ $account_category->category_type=='Asset'?'selected':'' }}>
Asset
</option>

<option value="Income" {{ $account_category->category_type=='Income'?'selected':'' }}>
Income
</option>

<option value="Expense" {{ $account_category->category_type=='Expense'?'selected':'' }}>
Expense
</option>

<option value="Liability" {{ $account_category->category_type=='Liability'?'selected':'' }}>
Liability
</option>

</select>

</div>

<div class="form-group">

<label>Description</label>

<textarea name="description"
          class="form-control">{{ $account_category->description }}</textarea>

</div>

<div class="form-group">

<label>Status</label>

<select name="status"
        class="form-control">

<option value="Active" {{ $account_category->status=='Active'?'selected':'' }}>
Active
</option>

<option value="Inactive" {{ $account_category->status=='Inactive'?'selected':'' }}>
Inactive
</option>

</select>

</div>

<br>

<button class="btn btn-primary">
    Update Category
</button>

</form>

</div>
</div>

@stop
