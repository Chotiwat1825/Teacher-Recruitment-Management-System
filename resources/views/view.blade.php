@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">รายละเอียดข้อมูลการบรรจุ</h5>
                    <a href="{{ route('home.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                </div>
            </div>

            {{-- ข้อมูลทั่วไป --}}
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

        {{-- เอิ่มส่วนสรุปข้อมูลหลังจากข้อมูลทั่วไป --}}
        <div class="row mt-4">
            @php
                $currentRoundSummary = DB::table('subjects_rounds AS sr')
                    ->select(
                        DB::raw('SUM(sr.passed_exam) as total_passed'),
                        DB::raw('SUM(sr.appointed) as total_appointed'),
                        DB::raw('SUM(sr.vacancy) as total_vacancy'),
                        DB::raw('SUM(sr.remaining) as total_remaining'),
                    )
                    ->where([
                        'sr.round_year' => $round[0]->round_year,
                        'sr.education_area_id' => $round[0]->education_area_id,
                        'sr.round_number' => $round[0]->round_number,
                    ])
                    ->first();
            @endphp
            <div class="col-md-3">
                <div class="info-card bg-primary bg-gradient">
                    <div class="info-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="info-card-details">
                        <h3>{{ number_format($currentRoundSummary->total_passed) }}</h3>
                        <p>ผู้สอบผ่านขึ้นบัญชี</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card bg-success bg-gradient">
                    <div class="info-card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="info-card-details">
                        <h3>{{ number_format($currentRoundSummary->total_appointed) }}</h3>
                        <p>บรรจุแล้ว</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card bg-warning bg-gradient">
                    <div class="info-card-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="info-card-details">
                        <h3>{{ number_format($currentRoundSummary->total_vacancy) }}</h3>
                        <p>บรรจุรอบนี้</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card bg-info bg-gradient">
                    <div class="info-card-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="info-card-details">
                        <h3>{{ number_format($currentRoundSummary->total_remaining) }}</h3>
                        <p>คงเหลือ</p>
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
        @endif

        {{-- รายละเอียดวิชาเอก --}}
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">รายละเอียดวิชาเอก</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                    <tr>
                                        <td class="text-center">{{ $index++ }}</td>
                                        <td>{{ $item->subject_group }}</td>
                                        <td class="text-center">{{ number_format($item->passed_exam) }}</td>
                                        <td class="text-center">{{ number_format($item->appointed) }}</td>
                                        <td class="text-center">{{ number_format($item->vacancy) }}</td>
                                        <td class="text-center">{{ number_format($item->remaining) }}</td>
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
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="2" class="text-center">รวมทั้งหมด</td>
                                    <td class="text-center">{{ number_format($total['passed']) }}</td>
                                    <td class="text-center">{{ number_format($total['appointed']) }}</td>
                                    <td class="text-center">{{ number_format($total['vacancy']) }}</td>
                                    <td class="text-center">{{ number_format($total['remaining']) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- เพิ่มส่วนนี้ต่อจากตารางรายละเอียดวิชาเอก --}}
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">สรุปข้อมูลทุกรอบ</h5>
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
                                            <span class="badge bg-info">รอบปัจจุบัน</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        {{ number_format($roundSummary->total_passed) }}</td>
                                    <td class="text-center">{{ number_format($roundSummary->total_appointed) }}</td>
                                    <td class="text-center">{{ number_format($roundSummary->total_vacancy) }}</td>
                                    <td class="text-center">{{ number_format($roundSummary->total_remaining) }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($roundSummary->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('home.rounds.show', [
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
                                <td class="text-center">{{ number_format($allRounds->max('total_passed')) }}</td>
                                <td class="text-center">{{ number_format($allRounds->sum('total_vacancy')) }}</td>
                                <td class="text-center">{{ number_format($allRounds->sum('total_vacancy')) }}</td>
                                <td class="text-center">{{ number_format($allRounds->last()->total_remaining) }}</td>
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
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ข้อมูลสำหรับกราฟ
        const rounds = @json($allRounds->pluck('round_number'));
        const appointed = @json($allRounds->pluck('total_appointed'));
        const vacancy = @json($allRounds->pluck('total_vacancy'));
        const remaining = @json($allRounds->pluck('total_remaining'));

        // สร้างกราฟ
        new Chart(document.getElementById('roundSummaryChart'), {
            type: 'bar',
            data: {
                labels: rounds.map(round => 'รอบที่ ' + round),
                datasets: [{
                        label: 'บรรจุแล้ว',
                        data: appointed,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'บรรจุรอบนี้',
                        data: vacancy,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'คงเหลือ',
                        data: remaining,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'สรุปข้อมูลการบรรจุแต่ละรอบ'
                    }
                }
            }
        });
    </script>
@endpush

@section('css')
    <style>
        /* Card Styling */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f8f9fa;
            padding: 1rem 1.25rem;
        }

        .card-header h5 {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* General Information Styling */
        .mb-1 strong {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .card-body p:not(.mb-1) {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .table tbody td {
            vertical-align: middle;
            color: #2c3e50;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Badge Styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
            color: white;
        }

        /* Button Styling */
        .btn {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Document Section Styling */
        .img-fluid {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        /* Chart Styling */
        #roundSummaryChart {
            margin-top: 2rem;
            padding: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Summary Table Footer */
        .table tfoot tr {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table tfoot td {
            border-top: 2px solid #dee2e6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .table-responsive {
                margin: 0 -1rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        /* Animation */
        .card {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Print Styles */
        @media print {
            .btn {
                display: none;
            }

            .card {
                box-shadow: none;
                border: 1px solid #dee2e6;
            }

            .table {
                border-collapse: collapse !important;
            }

            .table td,
            .table th {
                background-color: #fff !important;
            }
        }

        /* Info Cards Styling */
        .info-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-radius: 1rem;
            color: white;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .info-card-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
            opacity: 0.8;
        }

        .info-card-details {
            flex-grow: 1;
        }

        .info-card-details h3 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }

        .info-card-details p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        /* ปรับสีพื้นหลังให้สวยงาม */
        .bg-primary.bg-gradient {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }

        .bg-success.bg-gradient {
            background: linear-gradient(45deg, #1cc88a, #169b6b);
        }

        .bg-warning.bg-gradient {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
        }

        .bg-info.bg-gradient {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }

        /* เพิ่ม Animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-card {
            animation: slideIn 0.5s ease-out forwards;
        }

        .info-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .info-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .info-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .info-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .info-card {
                margin-bottom: 1rem;
            }

            .info-card-icon {
                font-size: 2rem;
            }

            .info-card-details h3 {
                font-size: 1.5rem;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .info-card {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            }
        }
    </style>
@endsection

@push('js')
    <script>
        // Animate number counting
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const current = Math.floor(progress * (end - start) + start);
                element.textContent = new Intl.NumberFormat().format(current);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate all number displays when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const finalValue = parseInt(element.textContent.replace(/,/g, ''));
                    animateValue(element, 0, finalValue, 1000);
                    observer.unobserve(element);
                }
            });
        });

        document.querySelectorAll('.info-card-details h3').forEach(el => observer.observe(el));
    </script>
@endpush
