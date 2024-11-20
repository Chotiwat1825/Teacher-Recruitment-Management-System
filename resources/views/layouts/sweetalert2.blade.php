@if (Session::has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: "{{ Session::get('success') }}",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                {{ Session::forget('success') }}
            });
        });
    </script>
@endif

@if (Session::has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: "{{ Session::get('error') }}",
                showConfirmButton: true,
                confirmButtonText: 'ตกลง'
            }).then(() => {
                {{ Session::forget('error') }}
            });
        });
    </script>
@endif
