@extends('adminlte::page')

@section('title', 'Tag Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1">Tag Management</h1>
            <p class="text-muted mb-0">Organize tasks with reusable labels and track tag usage.</p>
        </div>
        @can('manage-tags')
        <a href="{{ route('tags.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Tag
        </a>
        @endcan
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Color</th>
                            <th>Tasks</th>
                            <th>Description</th>
                            @can('manage-tags')
                                <th style="width: 150px;">Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $tag)
                            <tr>
                                <td>
                                    <span class="badge px-3 py-2" style="background-color: {{ $tag->color }}; color: #fff;">
                                        {{ $tag->name }}
                                    </span>
                                </td>
                                <td>{{ $tag->slug }}</td>
                                <td>
                                    <span class="badge badge-light border px-3 py-2">{{ $tag->color }}</span>
                                </td>
                                <td>{{ $tag->tasks_count }}</td>
                                <td>{{ $tag->description ?: 'No description' }}</td>
                                @can('manage-tags')
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tags.edit', $tag->id) }}" class="btn btn-warning">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-danger js-delete-tag"
                                                    data-url="{{ route('tags.destroy', $tag->id) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No tags found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.querySelectorAll('.js-delete-tag').forEach((button) => {
    button.addEventListener('click', async () => {
        const result = await Swal.fire({
            title: 'Delete tag?',
            text: 'This tag will be removed from all tasks.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) {
            return;
        }

        const response = await fetch(button.dataset.url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const data = await response.json();
            toast.fire({ icon: 'success', title: data.message || 'Deleted successfully.' });
            window.location.reload();
            return;
        }

        toast.fire({ icon: 'error', title: 'Unable to delete tag.' });
    });
});
</script>
@endpush
