@extends('adminlte::page')

@section('title', 'แผงควบคุม')

@section('content_header')
    <h1>แผงควบคุม</h1>
@stop

@section('content')
    {{-- สรุปข้อมูลภาพรวม --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSubjects }}</h3>
                    <p>กลุ่มวิชาเอกทั้งหมด</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
                <a href="{{ route('admin.subjects.index') }}" class="small-box-footer">
                    ดูรายละเอียด <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalEducationAreas }}</h3>
                    <p>เขตพื้นที่การศึกษา</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <a href="{{ route('admin.show_education_area') }}" class="small-box-footer">
                    ดูรายละเอียด <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPassedExam }}</h3>
                    <p>ผู้สอบผ่านขึ้นบัญชีทั้งหมด</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="{{ route('admin.subjects.rounds.index') }}" class="small-box-footer">
                    ดูรายละเอียด <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalAppointed }}</h3>
                    <p>บรรจุแล้วทั้งหมด</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.subjects.rounds.index') }}" class="small-box-footer">
                    ดูรายละเอียด <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- กราฟแสดงข้อมูล --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">สถิติการบรรจุรายเดือน</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyAppointmentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">สัดส่วนการบรรจุแยกตามกลุ่มวิชาเอก</h3>
                </div>
                <div class="card-body">
                    <canvas id="subjectsPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ตารางสรุปล่าสุด --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">การบรรจุล่าสุด</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เขตพื้นที่</th>
                            <th>กลุ่มวิชาเอก</th>
                            <th>รอบที่</th>
                            <th>จำนวนบรรจุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAppointments as $appointment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($appointment->created_at)->format('d/m/Y') }}</td>
                            <td>{{ $appointment->name_education }}</td>
                            <td>{{ $appointment->subject_group }}</td>
                            <td>{{ $appointment->round_number }}</td>
                            <td>{{ $appointment->vacancy }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // กราฟแท่งแสดงสถิติรายเดือน
    new Chart(document.getElementById('monthlyAppointmentChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyStats->pluck('month')) !!},
            datasets: [{
                label: 'จำนวนบรรจุ',
                data: {!! json_encode($monthlyStats->pluck('total')) !!},
                backgroundColor: 'rgba(60, 141, 188, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // กราฟวงกลมแสดงสัดส่วนวิชาเอก
    new Chart(document.getElementById('subjectsPieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($subjectsStats->pluck('subject_group')) !!},
            datasets: [{
                data: {!! json_encode($subjectsStats->pluck('total')) !!},
                backgroundColor: [
                    '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
                    '#d2d6de', '#6c757d', '#e83e8c', '#fd7e14', '#20c997'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@stop
