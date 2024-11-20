@extends('adminlte::page')

@section('title', 'รายละเอียดข้อมูลการบรรจุ')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>รายละเอียดข้อมูลการบรรจุ</h1>
        <div>
            <a href="{{ route('admin.subjects.rounds.edit', $round[0]->id) }}" class="btn btn-warning">
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
                        @php $total = ['passed' => 0, 'appointed' => 0, 'vacancy' => 0, 'remaining' => 0]; @endphp
                        @foreach ($round as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->subject_group }}</td>
                                <td class="text-center">{{ $item->passed_exam }}</td>
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
                        @endforeach
                        <tr class="font-weight-bold bg-light">
                            <td colspan="2" class="text-center">รวมทั้งหมด</td>
                            <td class="text-center">{{ $total['passed'] }}</td>
                            <td class="text-center">{{ $total['appointed'] }}</td>
                            <td class="text-center">{{ $total['vacancy'] }}</td>
                            <td class="text-center">{{ $total['remaining'] }}</td>
                            <td></td>
                        </tr>
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
