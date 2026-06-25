@extends('adminlte::page')

@section('content')

<a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
    Add User
</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->role }}</td>

        <td>
            <a href="{{ route('users.edit',$user->id) }}"
               class="btn btn-warning btn-sm">Edit</a>

            <form action="{{ route('users.destroy',$user->id) }}"
                  method="POST"
                  style="display:inline">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach

</table>

@endsection