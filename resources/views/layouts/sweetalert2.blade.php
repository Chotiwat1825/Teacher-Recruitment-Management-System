@section('js')
    <script>
        $(document).ready(function() {
            // แสดง success alert
            const showSuccessAlert = (message) => {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                });
            }

            // แสดง error alert
            const showErrorAlert = (message) => {
                Swal.fire({
                    title: 'ผิดพลาด!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }

            // แสดง confirm alert
            const showConfirmAlert = () => {
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณต้องการลบข้อมูลนี้ใช่หรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ทำงานเมื่อกดยืนยัน
                    }
                });
            }
        });
    </script>
@stop
