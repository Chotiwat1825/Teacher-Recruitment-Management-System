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
                            <select class="form-control" id="round_year" name="round_year" value="{{ old('round_year') }}"
                                required>
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
                            <input type="date" class="form-control" id="appointment_date" name="created_at" required
                                value="{{ old('created_at') }}">
                        </div>
                        @error('created_at')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="education_area_id">เขตพื้นที่การศึกษา</label>
                            <select class="form-control select2" id="education_area_id" name="education_area_id" required>
                                <option value="">-- เลือกเขตพื้นที่การศึกษา --</option>
                                @foreach ($education_area as $area)
                                    <option value="{{ $area->id }}"
                                        {{ $area->id == old('education_area_id') ? 'selected' : '' }}>
                                        {{ $area->id }} - {{ $area->name_education }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="round_number">รอบการเรียกบรรจุ</label>
                            <input type="number" class="form-control" id="round_number" name="round_number"
                                value="{{ old('round_number') }}" required>
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
                                            <select class="form-control" name="items[{{ $i }}][subject_id]"
                                                required>
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
                                                value="{{ old("items.$i.passed_exam") }}" required>
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
                                                value="{{ old("items.$i.vacancy") }}" required>
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
                                        <select class="form-control" name="items[0][subject_id]" required>
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
                                            value="{{ old('items.0.passed_exam') }}" required>
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
                                        <input type="number" class="form-control" name="items[0][vacancy]"
                                            value="{{ old('items.0.vacancy') }}" required>
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
            let itemCount = $('.subject-item').length - 1;

            // ฟังก์ชันตรวจสอบวิชาเอกซ้ำ
            function checkDuplicateSubject(selectElement) {
                let selectedValue = $(selectElement).val();
                let isDuplicate = false;

                // ตรวจสอบทุก select ยกเว้นตัวที่กำลังเลือก
                $('select[name*="[subject_id]"]').not(selectElement).each(function() {
                    if ($(this).val() === selectedValue && selectedValue !== '') {
                        isDuplicate = true;
                        return false; // break loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: 'กลุ่มวิชาเอกนี้ถูกเลือกไปแล้ว กรุณาเลือกกลุ่มวิชาเอกอื่น',
                        confirmButtonText: 'ตกลง'
                    });
                    $(selectElement).val(''); // รีเซ็ตค่าเป็นค่าว่าง
                    return false;
                }
                return true;
            }

            // เพิ่ม event listener สำหรับการเลือกวิชาเอก
            $(document).on('change', 'select[name*="[subject_id]"]', function() {
                checkDuplicateSubject(this);
            });

            // ตรวจสอบรอบการบรรจุเมื่อมีการเปลี่ยนแปลง
            $('#round_number').on('change', function() {
                let roundNumber = parseInt($(this).val()) || 0;
                if (roundNumber === 1) {
                    // ถ้าเป็นรอบ 1 ให้กำหนด appointed = 0
                    $('.appointed-display').val(0);
                    // อัพเดทการคำนวณทุกรายการ
                    $('.vacancy-input').trigger('input');
                }
            });

            // อัพเดทการคำนวณเมื่อมีการเปลี่ยนแปลงค่า
            $(document).on('input', '.passed-exam, .vacancy-input', function() {
                let $row = $(this).closest('.subject-item');
                let passed = parseInt($row.find('.passed-exam').val()) || 0;
                let vacancy = parseInt($row.find('.vacancy-input').val()) || 0;
                let roundNumber = parseInt($('#round_number').val()) || 0;
                
                // กำหนด appointed ตามรอบ
                let appointed = roundNumber === 1 ? 0 : parseInt($row.find('.appointed-display').val()) || 0;

                // ตรวจสอบไม่ให้บรรจุเกินจำนวนคงเหลือ
                let maxVacancy = passed - appointed;
                if (vacancy > maxVacancy) {
                    vacancy = maxVacancy;
                    $row.find('.vacancy-input').val(maxVacancy);
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: 'จำนวนบรรจุต้องไม่เกินจำนวนคงเหลือ',
                        confirmButtonText: 'ตกลง'
                    });
                }

                // คำนวณจำนวนคงเหลือ
                let remaining = passed - (appointed + vacancy);
                $row.find('.remaining-display').val(remaining);
            });

            // เพิ่มรายการใหม่
            $('#add-item').click(function() {
                let roundNumber = parseInt($('#round_number').val()) || 0;
                let template = $('.subject-item').first().clone();
                
                // รีเซ็ตค่าและอัพเดต name attributes
                template.find('select, input').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, '[' + itemCount + ']'));
                    }
                    $(this).val('');
                });

                // กำหนดค่า appointed ตามรอบ
                template.find('.appointed-display').val(roundNumber === 1 ? 0 : '');
                
                $('#subject-items').append(template);
                itemCount++;
            });

            // ลบรายการ
            $(document).on('click', '.remove-item', function() {
                if ($('.subject-item').length > 1) {
                    $(this).closest('.subject-item').remove();
                    updateAvailableOptions(); // อัพเดตตัวเลือกหลังจากลบรายการ
                }
            });

            // ตรวจสอบการกรอกข้อมูล
            $('form').on('submit', function(e) {
                let valid = true;
                let selectedSubjects = new Set();

                $('.subject-item').each(function() {
                    let subject = $(this).find('select[name*="[subject_id]"]').val();
                    let passed = $(this).find('input[name*="[passed_exam]"]').val();
                    let vacancy = $(this).find('input[name*="[vacancy]"]').val();

                    // ตรวจสอบค่าว่าง
                    if (!subject || !passed || !vacancy) {
                        valid = false;
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
                    if (!selectedSubjects.size) {
                        Swal.fire({
                            icon: 'error',
                            title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                            text: 'กรุณาตรวจสอบข้อมูลในทุกรายการ',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                }
            });

            // เริ่มต้นอัพเดตตัวเลือกที่มี
            updateAvailableOptions();

            // เพิ่มการตรวจสอบก่อนส่งฟอร์ม
            $('form').on('submit', function(e) {
                let valid = true;
                $('.subject-item').each(function() {
                    let passed = parseInt($(this).find('.passed-exam').val()) || 0;
                    let vacancy = parseInt($(this).find('.vacancy-input').val()) || 0;
                    let appointed = parseInt($(this).find('.appointed-display').val()) || 0;

                    if (vacancy > (passed - appointed)) {
                        valid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'พบข้อผิดพลาด',
                            text: 'จำนวนบรรจุต้องไม่เกินจำนวนคงเหลือ',
                            confirmButtonText: 'ตกลง'
                        });
                        return false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
