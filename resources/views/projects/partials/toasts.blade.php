@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true
        });

        @if (session('success'))
            toast.fire({ icon: 'success', title: @json(session('success')) });
        @endif

        @if (session('error'))
            toast.fire({ icon: 'error', title: @json(session('error')) });
        @endif
    </script>
@endpush
