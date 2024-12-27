@extends('adminlte::page')

@section('title', 'รายละเอียดข้อมูลการบรรจุ')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>รายละเอียดข้อมูลการบรรจุ</h1>
        <div>
            @if ($round[0]->round_number == $latestRound)
                <button type="button" class="btn btn-success" id="nextRoundBtn">
                    <i class="fas fa-plus"></i> เพิ่มรอบการบรรจุ
                </button>
            @else
                <a href="{{ route('admin.subjects.rounds.show', [
                    'roundYear' => $round[0]->round_year,
                    'educationAreaId' => $round[0]->education_area_id,
                    'roundNumber' => $latestRound,
                ]) }}"
                    class="btn btn-success">
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

    {{-- เอกสารแนบ --}}

    @if ($round[0]->document_path)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">เอกสารแนบ</h5>
            </div>
            <div class="card-body">
                @php
                    $extension = pathinfo($round[0]->document_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                @endphp

                @if ($isImage)
                    <div class="mb-3">
                        {{-- แก้ไขการแสดงรูปภาพโดยใช้ route เดียวกับที่ใช้ดาวน์โหลด --}}
                        <img src="{{ route('admin.subjects.rounds.document', [
                            'year' => $round[0]->round_year,
                            'area' => $round[0]->education_area_id,
                            'round' => $round[0]->round_number,
                        ]) }}"
                            class="img-fluid" style="max-height: 300px;" alt="เอกสารแนบ"
                            onerror="this.style.display='none'">
                    </div>
                @endif

                <a href="{{ route('admin.subjects.rounds.document', [
                    'year' => $round[0]->round_year,
                    'area' => $round[0]->education_area_id,
                    'round' => $round[0]->round_number,
                ]) }}"
                    class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i>
                    ดาวน์โหลดเอกสาร {{ strtoupper($extension) }}
                </a>
            </div>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">เอกสารแนบ</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">ไม่มีเอกสารแนบ</p>
            </div>
        </div>
    @endif


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
                            @if ($item->vacancy > 0)
                                {{-- แสดงเฉพาะรายการที่มี vacancy > 0 --}}
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
                        @if ($total['vacancy'] > 0)
                            {{-- แสดงแถวรวมเมื่อมีข้อมูล --}}
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

    {{-- สรุปข้อมูลทุกรอบ --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">สรุปข้อมูลทุกรอบ</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">รอบที่</th>
                            <th class="text-center">ผู้สอบผ่าน<br>ขึ้นบัญชี</th>
                            <th class="text-center">บรรจุแล้ว</th>
                            <th class="text-center">บรรจุรอบนี้</th>
                            <th class="text-center">คงเหลือ</th>
                            <th class="text-center">วันที่ประกาศ</th>
                            <th class="text-center">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allRounds = DB::table('subjects_rounds AS sr')
                                ->select(
                                    'sr.round_number',
                                    DB::raw('SUM(sr.passed_exam) as total_passed'),
                                    DB::raw('SUM(sr.appointed) as total_appointed'),
                                    DB::raw('SUM(sr.vacancy) as total_vacancy'),
                                    DB::raw('SUM(sr.remaining) as total_remaining'),
                                    'sr.created_at',
                                )
                                ->where([
                                    'sr.round_year' => $round[0]->round_year,
                                    'sr.education_area_id' => $round[0]->education_area_id,
                                ])
                                ->groupBy('sr.round_number', 'sr.created_at')
                                ->orderBy('sr.round_number', 'asc')
                                ->get();
                        @endphp

                        @foreach ($allRounds as $roundSummary)
                            <tr>
                                <td class="text-center">
                                    {{ $roundSummary->round_number }}
                                    @if ($roundSummary->round_number == $round[0]->round_number)
                                        <span class="badge badge-info">รอบปัจจุบัน</span>
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold">{{ $roundSummary->total_passed }}</td>
                                <td class="text-center">{{ $roundSummary->total_appointed }}</td>
                                <td class="text-center">{{ $roundSummary->total_vacancy }}</td>
                                <td class="text-center">{{ $roundSummary->total_remaining }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($roundSummary->created_at)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.subjects.rounds.show', [
                                        'roundYear' => $round[0]->round_year,
                                        'educationAreaId' => $round[0]->education_area_id,
                                        'roundNumber' => $roundSummary->round_number,
                                    ]) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td class="text-center">รวมทั้งหมด</td>
                            <td class="text-center">{{ $allRounds->max('total_passed') }}</td>
                            <td class="text-center">{{ $allRounds->sum('total_vacancy') }}</td>
                            <td class="text-center">{{ $allRounds->last()->total_vacancy }}</td>
                            <td class="text-center">{{ $allRounds->last()->total_remaining }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- แสดงกราฟสรุป --}}
            <div class="mt-4">
                <canvas id="roundSummaryChart"></canvas>
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

        .badge.badge-info {
            font-size: 0.8em;
            padding: 0.3em 0.6em;
            margin-left: 0.5em;
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
                        window.location.href =
                            "{{ route('admin.subjects.rounds.next', [
                                'year' => $round[0]->round_year,
                                'area' => $round[0]->education_area_id,
                                'round' => $latestRound,
                            ]) }}";
                    }
                });
            });
        });
    </script>
@endsection
