@extends('adminlte::page')

@section('title', 'รายละเอียดข้อมูลการบรรจุ')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>รายละเอียดข้อมูลการบรรจุ</h1>
        <div>
            @if($round[0]->round_number == $latestRound)
                <button type="button" class="btn btn-success" id="nextRoundBtn">
                    <i class="fas fa-plus"></i> เพิ่มรอบการบรรจุ
                </button>
            @else
                <a href="{{ route('admin.subjects.rounds.show', [
                    'roundYear' => $round[0]->round_year,
                    'educationAreaId' => $round[0]->education_area_id,
                    'roundNumber' => $latestRound
                ]) }}" class="btn btn-success">
                    <i class="fas fa-arrow-right"></i> ไปที่รอบล่าสุด (รอบที่ {{ $latestRound }})
                </a>
            @endif
            <a href="{{ route('admin.subjects.rounds.edit', [
                'roundYear' => $round[0]->round_year,
                'educationAreaId' => $round[0]->education_area_id,
                'roundNumber' => $round[0]->round_number,
            ]) }}"
                class="btn btn-warning">
                <i class="fas fa-edit"></i> แก้ไข
            </a>
            <a href="{{ route('admin.subjects.rounds.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- ข้อมูลทั่วไป --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">ข้อมูลทั่วไป</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-1"><strong>ปีการบรรจุ:</strong></p>
                    <p>{{ $round[0]->round_year }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>วันที่ประกาศ:</strong></p>
                    <p>{{ \Carbon\Carbon::parse($round[0]->created_at)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>เขตพื้นที่การศึกษา:</strong></p>
                    <p>{{ $round[0]->name_education }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>รอบการเรียกบรรจุ:</strong></p>
                    <p>{{ $round[0]->round_number }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- รายละเอียดวิชาเอก --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">รายละเอียดวิชาเอก</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">ลำดับ</th>
                            <th>กลุ่มวิชาเอก</th>
                            <th class="text-center">ผู้สอบผ่าน<br>ขึ้นบัญชี</th>
                            <th class="text-center">บรรจุแล้ว</th>
                            <th class="text-center">บรรจุรอบนี้</th>
                            <th class="text-center">คงเหลือ</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $total = ['passed' => 0, 'appointed' => 0, 'vacancy' => 0, 'remaining' => 0];
                            $index = 1;
                        @endphp
                        @foreach ($round as $item)
                            @if($item->vacancy > 0)  {{-- แสดงเฉพาะรายการที่มี vacancy > 0 --}}
                                <tr>
                                    <td class="text-center">{{ $index++ }}</td>
                                    <td>{{ $item->subject_group }}</td>
                                    <td class="text-center font-weight-bold">{{ $item->passed_exam }}</td>
                                    <td class="text-center">{{ $item->appointed }}</td>
                                    <td class="text-center">{{ $item->vacancy }}</td>
                                    <td class="text-center">{{ $item->remaining }}</td>
                                    <td>{{ $item->notes }}</td>
                                </tr>
                                @php
                                    $total['passed'] += $item->passed_exam;
                                    $total['appointed'] += $item->appointed;
                                    $total['vacancy'] += $item->vacancy;
                                    $total['remaining'] += $item->remaining;
                                @endphp
                            @endif
                        @endforeach
                        @if($total['vacancy'] > 0)  {{-- แสดงแถวรวมเมื่อมีข้อมูล --}}
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2" class="text-center">รวมทั้งหมด</td>
                                <td class="text-center">{{ $total['passed'] }}</td>
                                <td class="text-center">{{ $total['appointed'] }}</td>
                                <td class="text-center">{{ $total['vacancy'] }}</td>
                                <td class="text-center">{{ $total['remaining'] }}</td>
                                <td></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="text-center">ไม่มีข้อมูลการบรรจุในรอบนี้</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- สรุปข้อมูล --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">สรุปข้อมูล</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @if($total['vacancy'] > 0)  {{-- แสดง info boxes เมื่อมีข้อมูล --}}
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">ผู้สอบผ่านขึ้นบัญชี</span>
                                <span class="info-box-number">{{ $total['passed'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">บรรจุแล้ว</span>
                                <span class="info-box-number">{{ $total['appointed'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">บรรจุรอบนี้</span>
                                <span class="info-box-number">{{ $total['vacancy'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-user-minus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">คงเหลือ</span>
                                <span class="info-box-number">{{ $total['remaining'] }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            ไม่มีข้อมูลการบรรจุในรอบนี้
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .info-box-content {
            color: white;
        }

        .info-box-number {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#nextRoundBtn').click(function() {
                Swal.fire({
                    title: 'ยืนยันการเพิ่มรอบการบรรจุ?',
                    text: "ต้องการเพิ่มรอบการบรรจุจากรอบที่ {{ $latestRound }} เป็นรอบที่ {{ $latestRound + 1 }} ใช่หรือไม่?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ใช่, เพิ่มรอบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('admin.subjects.rounds.next', [
                            'year' => $round[0]->round_year,
                            'area' => $round[0]->education_area_id,
                            'round' => $latestRound
                        ]) }}";
                    }
                });
            });
        });
    </script>
@endsection
