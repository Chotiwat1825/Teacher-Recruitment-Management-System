@extends('adminlte::page')

@section('title', 'ข้อมูลการบรรจุ')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>ข้อมูลการบรรจุ</h1>
        <a href="{{ route('admin.create.rounds') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> สร้างข้อมูลการบรรจุ
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">ปีการบรรจุ</th>
                            <th>เขตพื้นที่การศึกษา</th>
                            <th class="text-center">รอบที่</th>
                            <th class="text-center">วันที่ประกาศ</th>
                            <th class="text-center">จำนวนวิชาเอก</th>
                            <th class="text-center">ผู้สอบผ่าน</th>
                            <th class="text-center">บรรจุแล้ว</th>
                            <th class="text-center">บรรจุรอบนี้</th>
                            <th class="text-center">คงเหลือ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rounds as $round)
                            <tr>
                                <td class="text-center">{{ $round->round_year }}</td>
                                <td>{{ $round->name_education }}</td>
                                <td class="text-center">{{ $round->round_number }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($round->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">{{ $round->subject_count }}</td>
                                <td class="text-center">{{ $round->total_passed }}</td>
                                <td class="text-center">{{ $round->total_appointed }}</td>
                                <td class="text-center">{{ $round->total_vacancy }}</td>
                                <td class="text-center">{{ $round->total_remaining }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.subjects.rounds.show', [
                                            'roundYear' => $round->round_year,
                                            'educationAreaId' => $round->education_area_id,
                                            'roundNumber' => $round->round_number,
                                        ]) }}"
                                            class="btn btn-info btn-sm" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subjects.rounds.edit', [
                                            'roundYear' => $round->round_year,
                                            'educationAreaId' => $round->education_area_id,
                                            'roundNumber' => $round->round_number,
                                        ]) }}"
                                            class="btn btn-warning btn-sm" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-round"
                                            data-id="{{ $round->round_year }}/{{ $round->education_area_id }}/{{ $round->round_number }}" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">ไม่พบข้อมูล</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $rounds->links() }}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // ลบข้อมูล
            $('.delete-round').click(function() {
                const roundId = $(this).data('id');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณต้องการลบข้อมูลการบรรจุนี้ใช่หรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่ง request ไปยัง route สำหรับลบข้อมูล
                        window.location.href =
                            `{{ url('admin/subjects/rounds') }}/${roundId}/delete`;
                    }
                });
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group .btn {
            margin: 0 2px;
        }
    </style>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#example1').DataTable();
        });
    </script>
@endsection
