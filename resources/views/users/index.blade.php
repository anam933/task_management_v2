@extends('adminlte::page')

@section('content')

@can('manage-employees')
<a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
    {{ auth()->user()->hasRole('manager') ? 'Add Employee' : 'Add User' }}
</a>
@endcan

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Reporting To</th>
        <th>Created By</th>
        <th>Action</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->role) }}</td>
        <td>{{ optional($user->manager)->name ?? 'None' }}</td>
        <td>{{ optional($user->creator)->name ?? 'System' }}</td>

        <td>
            @can('manage-employees')
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
            @endcan
        </td>
    </tr>
    @endforeach

</table>

@endsection
