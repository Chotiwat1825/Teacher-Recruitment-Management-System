@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Hero Section -->
        <div class="bg-primary text-white p-5 rounded mb-4">
            <div class="text-center">
                <h1 class="display-4 fw-bold">ระบบค้นหาข้อมูลการบรรจุครู</h1>
                <p class="lead">ค้นหาข้อมูลการบรรจุครูในแต่ละเขตพื้นที่การศึกษาและกลุ่มวิชาเอก</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-book fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 text-dark">{{ number_format($totalSubjects) }}</h3>
                                <p class="mb-0 text-muted">กลุ่มวิชาเอกทั้งหมด</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-user-graduate fa-2x text-success"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 text-dark">{{ number_format($totalPassedExam) }}</h3>
                                <p class="mb-0 text-muted">ผู้สอบผ่านขึ้นบัญชีทั้งหมด</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-user-check fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 text-dark">{{ number_format($totalAppointed) }}</h3>
                                <p class="mb-0 text-muted">บรรจุแล้วทั้งหมด</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-dark"><i class="fas fa-search me-2"></i>ค้นหาข้อมูล</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('home.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">เขตพื้นที่การศึกษา</label>
                                <select class="form-select select2" name="education_area">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($educationAreas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ request('education_area') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name_education }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_area')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">กลุ่มวิชาเอก</label>
                                <select class="form-select select2" name="subject_group">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject_group') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_group }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_group')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">ปีการบรรจุ</label>
                                <select class="form-select" name="year">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-search me-2"></i>ค้นหา
                        </button>
                        <a href="{{ route('home.index') }}" class="btn btn-outline-secondary px-4 ms-2">
                            <i class="fas fa-redo me-2"></i>ล้างการค้นหา
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        @if (isset($results))
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark"><i class="fas fa-table me-2"></i>ผลการค้นหา</h5>
                        <span class="badge bg-primary">{{ $results->total() }} รายการ</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="results-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ปี</th>
                                    <th>เขตพื้นที่</th>
                                    <th>กลุ่มวิชาเอก</th>
                                    <th class="text-center">รอบที่</th>
                                    <th class="text-center">ผู้สอบผ่าน</th>
                                    <th class="text-center">บรรจุแล้ว</th>
                                    <th class="text-center">บรรจุรอบนี้</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th>วันที่ประกาศ</th>
                                    <th class="text-center">เอกสาร</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $result)
                                    <tr>
                                        <td>{{ $result->round_year }}</td>
                                        <td>{{ $result->name_education }}</td>
                                        <td>{{ $result->subject_group }}</td>
                                        <td class="text-center">{{ $result->round_number }}</td>
                                        <td class="text-center">{{ number_format($result->passed_exam) }}</td>
                                        <td class="text-center">{{ number_format($result->appointed) }}</td>
                                        <td class="text-center">{{ number_format($result->vacancy) }}</td>
                                        <td class="text-center">{{ number_format($result->remaining) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($result->created_at)->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('home.rounds.show', [
                                                'roundYear' => $result->round_year,
                                                'educationAreaId' => $result->education_area_id,
                                                'roundNumber' => $result->round_number,
                                            ]) }}"
                                                class="btn btn-sm btn-info me-2" title="ดูรายละเอียด">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($result->document_path)
                                                <a href="{{ route('admin.subjects.rounds.document', [
                                                    'year' => $result->round_year,
                                                    'area' => $result->education_area_id,
                                                    'round' => $result->round_number,
                                                ]) }}"
                                                    target="_blank" class="btn btn-sm btn-secondary" title="ดูเอกสาร">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-times"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-search fa-2x mb-3 d-block"></i>
                                                ไม่พบข้อมูลที่ค้นหา
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $results->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            color: #333;
            /* สีตัวอักษรหลัก */
            background-color: #f8f9fa;
            /* สีพื้นหลัง */
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 123, 255, 0.9), rgba(0, 123, 255, 0.9));
            padding: 60px 0;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .hero-section h1 {
            color: white !important;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            color: white !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Stat Cards */
        .stat-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-card h3,
        .stat-card p {
            color: white !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .bg-gradient-warning h3,
        .bg-gradient-warning p {
            color: #000 !important;
            /* สีดำสำหรับการ์ดสีเหลือง */
            text-shadow: none;
        }

        /* Search Card */
        .card {
            background: white;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
        }

        .card-header h5 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }

        /* Form Elements */
        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-select,
        .form-control {
            color: #333;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            color: #333;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        /* Select2 Customization */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            color: #333;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container--default .select2-results__option {
            color: #333;
            padding: 6px 12px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
            color: white;
        }

        /* Pagination */
        .pagination {
            margin: 1rem 0 0;
        }

        .page-link {
            color: #333;
            padding: 0.5rem 1rem;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        /* Results Count Badge */
        .badge.bg-primary {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        /* Empty State */
        .text-muted {
            color: #6c757d !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .table-responsive {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                language: {
                    noResults: function() {
                        return "ไม่พบข้อมูล";
                    }
                }
            });
        });
    </script>
@endsection
