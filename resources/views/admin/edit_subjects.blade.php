@extends('adminlte::page')

@section('title', 'แก้ไขข้อมูลการบรรจุ')

@section('content_header')
    <h1>แก้ไขข้อมูลการบรรจุ</h1>
@stop

@section('content')
    <form action="{{ route('admin.subjects.rounds.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- เก็บค่าเดิมไว้เปรียบเทียบ --}}
        <input type="hidden" name="old_round_year" value="{{ $round->round_year }}">
        <input type="hidden" name="old_education_area_id" value="{{ $round->education_area_id }}">
        <input type="hidden" name="old_round_number" value="{{ $round->round_number }}">

        {{-- ส่วนข้อมูลทั่วไป --}}
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
                                    <option value="{{ $year }}" {{ $year == $round->round_year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="appointment_date">ประกาศวันที่</label>
                            <input type="date" class="form-control" id="appointment_date" name="created_at"
                                value="{{ date('Y-m-d', strtotime($round->created_at)) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="education_area_id">เขตพื้นที่การศึกษา</label>
                            <select class="form-control select2" id="education_area_id" name="education_area_id" required>
                                <option value="">-- เลือกเขตพื้นที่การศึกษา --</option>
                                @foreach ($education_area as $area)
                                    <option value="{{ $area->id }}"
                                        {{ $area->id == $round->education_area_id ? 'selected' : '' }}>
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
                                value="{{ $round->round_number }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add this after the general information card and before the subject items card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="document">เอกสารแนบ (PDF, JPEG, PNG)</label>
                            @if ($round->document_path)
                                <div class="mb-3">
                                    <p class="text-success">
                                        <i class="fas fa-file"></i> มีไฟล์เอกสารแนบอยู่แล้ว
                                        <a href="{{ route('admin.subjects.rounds.document', [
                                            'year' => $round->round_year,
                                            'area' => $round->education_area_id,
                                            'round' => $round->round_number,
                                        ]) }}"
                                            target="_blank" class="btn btn-sm btn-info ml-2">
                                            <i class="fas fa-eye"></i> ดูเอกสาร
                                        </a>
                                    </p>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="document" name="document"
                                    accept=".pdf,.jpeg,.jpg,.png">
                                <label class="custom-file-label" for="document">
                                    {{ $round->document_path ? 'เลือกไฟล์ใหม่เพื่อแทนที่' : 'เลือกไฟล์' }}
                                </label>
                            </div>
                            <small class="form-text text-muted">รองรับไฟล์ PDF, JPEG, PNG ขนาดไม่เกิน 10MB</small>
                            @error('document')
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
                    @foreach ($items as $index => $item)
                        <div class="subject-item mb-3">
                            <div class="row">
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>กลุ่มวิชาเอก</label>
                                        <select class="form-control" name="items[{{ $index }}][subject_id]"
                                            required>
                                            <option value="">-- เลือกวิชาเอก --</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                    {{ $subject->id == $item->subject_id ? 'selected' : '' }}>
                                                    {{ $subject->subject_group }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>ผู้สอบผ่านขึ้นบัญชี</label>
                                        <input type="number" class="form-control"
                                            name="items[{{ $index }}][passed_exam]"
                                            value="{{ $item->passed_exam }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>บรรจุรอบนี้</label>
                                        <input type="number" class="form-control"
                                            name="items[{{ $index }}][vacancy]" value="{{ $item->vacancy }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>หมายเหตุ</label>
                                        <input type="text" class="form-control" name="items[{{ $index }}][notes]"
                                            value="{{ $item->notes }}">
                                    </div>
                                </div>
                                @if ($index > 0)
                                    <div class="col-md-1 d-flex align-items-end">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger btn-md remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-success" id="add-item">
                    <i class="fas fa-plus"></i> เพิ่มวิชาเอก
                </button>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            <a href="{{ route('admin.subjects.rounds.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </div>
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let itemCount = {{ count($items) - 1 }};

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

        // Add this for file input display
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || 'เลือกไฟล์');
        });
    </script>
@endsection
