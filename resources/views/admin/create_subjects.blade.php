@extends('adminlte::page')

@section('title', 'สร้างข้อมูลการบรรจุ')

@section('content_header')
    <h1>สร้างข้อมูลการบรรจุ</h1>
@stop



@section('content')
    <form action="{{ route('admin.subjects.rounds.create') }}" method="POST">
        @csrf

        {{-- ส่วนข้อมูลทั่วไปของรอบการบรรจุ --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="round_year">ปีการบรรจุ</label>
                            <select class="form-control" id="round_year" name="round_year" required>
                                @php
                                    $currentYear = date('Y') + 543;
                                    $startYear = $currentYear - 5;
                                    $endYear = $currentYear + 5;
                                @endphp
                                @for ($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="appointment_date">ประกาศวันที่</label>
                            <input type="date" class="form-control" id="appointment_date" name="created_at" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="education_area_id">เขตพื้นที่การศึกษา</label>
                            <select class="form-control select2" id="education_area_id" name="education_area_id" required>
                                <option value="">-- เลือกเขตพื้นที่การศึกษา --</option>
                                @foreach ($education_area as $area)
                                    <option value="{{ $area->id }}">{{ $area->id }} - {{ $area->name_education }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="round_number">รอบการเรียกบรรจุ</label>
                            <input type="number" class="form-control" id="round_number" name="round_number" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ส่วนของรายการวิชาเอก --}}
        <div class="card">
            <div class="card-body">
                <div id="subject-items">
                    <div class="subject-item mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>กลุ่มวิชาเอก</label>
                                    <select class="form-control" name="items[0][subject_id]" required>
                                        <option value="">-- เลือกวิชาเอก --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->subject_group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>ผู้สอบผ่านขึ้นบัญชี</label>
                                    <input type="number" class="form-control" name="items[0][passed_exam]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>บรรจุรอบนี้</label>
                                    <input type="number" class="form-control" name="items[0][vacancy]" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>หมายเหตุ</label>
                                    <input type="text" class="form-control" name="items[0][notes]">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-md remove-item" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success" id="add-item">
                    <i class="fas fa-plus"></i> เพิ่มวิชาเอก
                </button>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let itemCount = 0;

            // เพิ่มรายการใหม่
            $('#add-item').click(function() {
                itemCount++;
                let template = $('.subject-item').first().clone();

                // อัพเดต name attributes
                template.find('select, input').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace('[0]', '[' + itemCount + ']'));
                    }
                    $(this).val(''); // เคลียร์ค่า
                });

                // แสดงปุ่มลบ
                template.find('.remove-item').show();

                $('#subject-items').append(template);
            });

            // ลบรายการ
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.subject-item').remove();
            });
        });
    </script>
@endsection
