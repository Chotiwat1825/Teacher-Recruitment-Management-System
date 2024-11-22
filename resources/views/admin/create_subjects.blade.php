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
                            <select class="form-control" id="round_year" name="round_year" value="{{ old('round_year') }}">
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
                            @error('round_year')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">

                        <div class="form-group">
                            <label for="appointment_date">ประกาศวันที่</label>
                            <input type="date" class="form-control" id="appointment_date" name="created_at"
                                value="{{ old('created_at') }}">
                        </div>
                        @error('created_at')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="education_area_id">เขตพื้นที่การศึกษา</label>
                            <select class="form-control select2" id="education_area_id" name="education_area_id">
                                <option value="">-- เลือกเขตพื้นที่การศึกษา --</option>
                                @foreach ($education_area as $area)
                                    <option value="{{ $area->id }}"
                                        {{ $area->id == old('education_area_id') ? 'selected' : '' }}>
                                        {{ $area->id }} - {{ $area->name_education }}
                                    </option>
                                @endforeach
                            </select>
                            @error('education_area_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="round_number">รอบการเรียกบรรจุ</label>
                            <input type="number" class="form-control" id="round_number" name="round_number"
                                value="{{ old('round_number') }}">
                            @error('round_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ส่วนของรายการวิชาเอก --}}
        <div class="card">
            <div class="card-body">
                <div id="subject-items">
                    @if (old('items'))
                        {{-- แสดงข้อมูลเดิมเมื่อเกิด validation error --}}
                        @foreach (old('items') as $i => $item)
                            <div class="subject-item mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>กลุ่มวิชาเอก</label>
                                            <select class="form-control" name="items[{{ $i }}][subject_id]">
                                                <option value="">-- เลือกวิชาเอก --</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ $subject->id == $item['subject_id'] ? 'selected' : '' }}>
                                                        {{ $subject->subject_group }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("items.$i.subject_id")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>ผู้สอบผ่านขึ้นบัญชี</label>
                                            <input type="number" class="form-control passed-exam"
                                                name="items[{{ $i }}][passed_exam]"
                                                value="{{ old("items.$i.passed_exam") }}">
                                            @error("items.$i.passed_exam")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>บรรจุสะสม</label>
                                            <input type="text" class="form-control appointed-display" value="0"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>บรรจุรอบนี้</label>
                                            <input type="number" class="form-control vacancy-input"
                                                name="items[{{ $i }}][vacancy]"
                                                value="{{ old("items.$i.vacancy") }}">
                                            @error("items.$i.vacancy")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>คงเหลือ</label>
                                            <input type="text" class="form-control remaining-display" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger btn-md remove-item"
                                                {{ $i == 0 ? 'style=display:none' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- แสดงฟอร์มเปล่าเมื่อเข้ามาครั้งแรก --}}
                        <div class="subject-item mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>กลุ่มวิชาเอก</label>
                                        <select class="form-control" name="items[0][subject_id]">
                                            <option value="">-- เลือกวิชาเอก --</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                    {{ $subject->id == old('items.0.subject_id') ? 'selected' : '' }}>
                                                    {{ $subject->subject_group }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>ผู้สอบผ่านขึ้นบัญชี</label>
                                        <input type="number" class="form-control" name="items[0][passed_exam]"
                                            value="{{ old('items.0.passed_exam') }}">
                                        @error('items.0.passed_exam')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="form-group">
                                        <label>บรรจุสะสม</label>
                                        <input type="text" class="form-control appointed-display" value="0"
                                            readonly>
                                    </div>
                                </div> --}}
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>บรรจุรอบนี้</label>
                                        <input type="number" class="form-control" name="items[0][vacancy]"
                                            value="{{ old('items.0.vacancy') }}">
                                        @error('items.0.vacancy')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="form-group">
                                        <label>คงเหลือ</label>
                                        <input type="text" class="form-control remaining-display" readonly>
                                    </div>
                                </div> --}}
                                <div class="col-md-1 d-flex align-items-end">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-danger btn-md remove-item"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
            // นับจำนวนรายการปัจจุบัน
            let itemCount = {{ old('items') ? count(old('items')) : 1 }};

            // ฟังก์ชันตำนวณ remaining
            function calculateRemaining($row) {
                let passed = parseInt($row.find('.passed-exam').val()) || 0;
                let appointed = parseInt($row.find('.appointed-display').val()) || 0;
                let vacancy = parseInt($row.find('.vacancy-input').val()) || 0;
                let remaining = passed - (appointed + vacancy);
                
                // อัพเดทค่า remaining
                $row.find('.remaining-display').val(remaining);
                
                // เพิ่มสีแสดงสถานะ
                let $remainingDisplay = $row.find('.remaining-display');
                if (remaining < 0) {
                    $remainingDisplay.addClass('text-danger');
                } else {
                    $remainingDisplay.removeClass('text-danger');
                }
            }

            // เรียกใช้ calculateRemaining เมื่อมีการเปลี่ยนแปลงค่า
            $(document).on('input', '.passed-exam, .vacancy-input', function() {
                let $row = $(this).closest('.subject-item');
                let $passedExam = $row.find('.passed-exam');
                let $vacancy = $row.find('.vacancy-input');
                let $appointed = $row.find('.appointed-display');
                
                let passed = parseInt($passedExam.val()) || 0;
                let appointed = parseInt($appointed.val()) || 0;
                let vacancy = parseInt($vacancy.val()) || 0;
                
                // ตรวจสอบค่าติดลบ
                if (passed < 0) {
                    passed = 0;
                    $passedExam.val(0);
                }
                if (vacancy < 0) {
                    vacancy = 0;
                    $vacancy.val(0);
                }

                // ตรวจสอบไม่ให้บรรจุเกินจำนวนคงเหลือ
                let maxVacancy = passed - appointed;
                if (vacancy > maxVacancy) {
                    vacancy = maxVacancy;
                    $vacancy.val(maxVacancy);
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: 'จำนวนบรรจุต้องไม่เกินจำนวนคงเหลือ',
                        confirmButtonText: 'ตกลง'
                    });
                }

                // คำนวณ remaining
                calculateRemaining($row);
            });

            // เรียกใช้ calculateRemaining เมื่อเปลี่ยนรอบการบรรจุ
            $('#round_number').on('change', function() {
                let roundNumber = parseInt($(this).val()) || 0;
                $('.subject-item').each(function() {
                    let $row = $(this);
                    if (roundNumber === 1) {
                        $row.find('.appointed-display').val(0);
                    }
                    calculateRemaining($row);
                });
            });

            // เพียกใช้ calculateRemaining เมื่อเพิ่มรายการใหม่
            $('#add-item').click(function() {
                let roundNumber = parseInt($('#round_number').val()) || 0;
                let template = $('.subject-item').first().clone();
                
                // อัพเดต index ให้ถูกต้อง
                itemCount = $('.subject-item').length; // นับจำนวนรายการปัจจุบัน

                // รีเซ็ตค่าและอัพเดต name attributes
                template.find('select, input').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/\[\d+\]/, '[' + itemCount + ']');
                        $(this).attr('name', newName);
                        $(this).val(''); // รีเซ็ตค่า
                        $(this).removeClass('is-invalid'); // ลบ class แสดงข้อผิดพลาด
                    }
                });

                // ลบข้อความ error ถ้ามี
                template.find('.text-danger').remove();

                // กำหนดค่า appointed ตามรอบ
                template.find('.appointed-display').val(roundNumber === 1 ? 0 : '');

                // แสดงปุ่มลบสำหรับรายการที่เพิ่มใหม่
                template.find('.remove-item').show();

                // คำนวณ remaining สำหรับรายการใหม่
                calculateRemaining(template);

                $('#subject-items').append(template);
                itemCount++;
            });

            // คำนวณ remaining ครั้งแรกเมื่อโหลดหน้า
            $('.subject-item').each(function() {
                calculateRemaining($(this));
            });

            // เพิ่ม CSS สำหรับแสดงสถานะ
            $('<style>')
                .text(`
                    .remaining-display.text-danger {
                        color: #dc3545;
                        font-weight: bold;
                    }
                `)
                .appendTo('head');

            // แก้ไขการตรวจสอบก่อนส่งฟอร์ม
            $('form').on('submit', function(e) {
                let valid = true;
                let selectedSubjects = new Set();
                let items = [];

                $('.subject-item').each(function(index) {
                    let $item = $(this);
                    let subject = $item.find('select[name*="[subject_id]"]').val();
                    let passed = $item.find('input[name*="[passed_exam]"]').val();
                    let vacancy = $item.find('input[name*="[vacancy]"]').val();
                    let appointed = $item.find('.appointed-display').val();

                    // เก็บข้อมูลรายการ
                    items.push({
                        subject_id: subject,
                        passed_exam: passed,
                        vacancy: vacancy,
                        appointed: appointed
                    });

                    // ตรวจสอบค่าว่าง
                    if (!subject || !passed || !vacancy) {
                        valid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                            text: 'กรุณากรอกข้อมูลทุกช่องในรายการที่ ' + (index + 1),
                            confirmButtonText: 'ตกลง'
                        });
                        return false;
                    }

                    // ตรวจสอบวิชาซ้ำ
                    if (selectedSubjects.has(subject)) {
                        valid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'พบข้อผิดพลาด',
                            text: 'กรุณาเลือกกลุ่มวิชาเอกที่ไม่ซ้ำกัน',
                            confirmButtonText: 'ตกลง'
                        });
                        return false;
                    }
                    selectedSubjects.add(subject);
                });

                if (!valid) {
                    e.preventDefault();
                    return false;
                }

                // เพิ่ม hidden input เก็บจำนวน items
                $('<input>').attr({
                    type: 'hidden',
                    name: 'items_count',
                    value: items.length
                }).appendTo($(this));
            });
        });
    </script>
@endsection
