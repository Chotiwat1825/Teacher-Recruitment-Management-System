@extends('adminlte::page')

@section('title', 'จัดการข้อมูลวิชาเอก')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>จัดการข้อมูลวิชาเอก</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSubjectModal">
            <i class="fas fa-plus"></i> เพิ่มวิชาเอก
        </button>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="subjects-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ลำดับ</th>
                            <th>กลุ่มวิชาเอก</th>
                            <th style="width: 150px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $index => $subject)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $subject->subject_group }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm edit-subject"
                                        data-id="{{ $subject->id }}" data-group="{{ $subject->subject_group }}">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-subject"
                                        data-id="{{ $subject->id }}" data-group="{{ $subject->subject_group }}">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal เพิ่มวิชาเอก --}}
    <div class="modal fade" id="addSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มวิชาเอก</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="subject_group">ชื่อกลุ่มวิชาเอก <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject_group" name="subject_group" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal แก้ไขวิชาเอก --}}
    <div class="modal fade" id="editSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขวิชาเอก</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editSubjectForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_subject_group">ชื่อกลุ่มวิชาเอก <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_subject_group" name="subject_group"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // DataTable
            $('#subjects-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
                }
            });

            // แก้ไขวิชาเอก
            $('.edit-subject').click(function() {
                let id = $(this).data('id');
                let group = $(this).data('group');

                $('#edit_subject_group').val(group);
                $('#editSubjectForm').attr('action', `/admin/subjects/${id}`);
                $('#editSubjectModal').modal('show');
            });

            // ลบวิชาเอก
            $('.delete-subject').click(function() {
                let id = $(this).data('id');
                let group = $(this).data('group');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: `ต้องการลบวิชาเอก "${group}" ใช่หรือไม่?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/subjects/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'ลบสำเร็จ!',
                                    'ลบข้อมูลวิชาเอกเรียบร้อยแล้ว',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    'ไม่สามารถลบข้อมูลได้',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
