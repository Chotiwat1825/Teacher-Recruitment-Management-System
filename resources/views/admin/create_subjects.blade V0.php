@extends('adminlte::page')

@section('title', 'สร้างข้อมูลการบรรจุ')

@section('content_header')
    <h1>สร้างข้อมูลการบรรจุ</h1>
@stop

@section('content')
    <form action="{{ route('admin.subjects.rounds.create') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="round_year">ปีการบรรจุ</label>
            <select class="form-control" id="round_year" name="round_year" required>
                @php
                    $currentYear = date('Y') + 543; // Convert to Buddhist year
                    $startYear = $currentYear - 5;
                    $endYear = $currentYear + 5;
                @endphp
                @for ($year = $startYear; $year <= $endYear; $year++)
                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="form-group">
            <label for="education_area_id">เลือกเขตพื้นที่การศึกษา</label>
            <input type="text" class="form-control" id="search_education" placeholder="ค้นหาเขตพื้นที่การศึกษา"
                onkeyup="searchEducation()">
            <div id="education_results" class="list-group mt-2" style="display:none;"></div>
            <select class="form-control select2 mt-2" id="education_area_id" name="education_area_id" required>
                <option value="">-- เลือกสถานศึกษา --</option>
                @foreach ($education_area as $area)
                    <option value="{{ $area->id }}">{{ $area->id }} - {{ $area->name_education }}</option>
                @endforeach
            </select>
        </div>
        <script>
            function searchEducation() {
                let input = document.getElementById('search_education').value;
                let results = document.getElementById('education_results');
                let select = document.getElementById('education_area_id');

                if (input.length > 0) {
                    results.style.display = 'block';
                    let filteredAreas = Array.from(select.options)
                        .filter(option =>
                            option.text.toLowerCase().includes(input.toLowerCase())
                        );

                    results.innerHTML = '';
                    filteredAreas.forEach(area => {
                        if (area.value) { // Skip the placeholder option
                            let div = document.createElement('a');
                            div.className = 'list-group-item list-group-item-action';
                            div.innerHTML = area.text;
                            div.onclick = function() {
                                select.value = area.value;
                                results.style.display = 'none';
                                document.getElementById('search_education').value = area.text;
                            };
                            results.appendChild(div);
                        }
                    });
                } else {
                    results.style.display = 'none';
                }
            }
        </script>

        <div class="form-group">
            <label for="round_number">รอบการเรียกบรรจุ</label>
            <input type="number" class="form-control" id="round_number" name="round_number" required>
        </div>

        <div class="form-group">
            <label for="subject_id">กลุ่มวิชาเอก</label>
            <select class="form-control" id="subject_id" name="subject_id" required>
                {{-- Populate with subjects from database --}}
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->subject_group }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="passed_exam">ผู้สอบผ่านขึ้นบัญชี</label>
            <input type="number" class="form-control" id="passed_exam" name="passed_exam" required>
        </div>

        <div class="form-group">
            <label for="appointed">รับการบรรจุและแต่งตั้งแล้ว</label>
            <input type="number" class="form-control" id="appointed" name="appointed" required>
        </div>

        <div class="form-group">
            <label for="vacancy">บรรจุรอบนี้</label>
            <input type="number" class="form-control" id="vacancy" name="vacancy" required>
        </div>

        <div class="form-group">
            <label for="remaining">คงเหลือ</label>
            <input type="number" class="form-control" id="remaining" name="remaining" required>
        </div>

        <div class="form-group">
            <label for="notes">หมายเหตุ</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#example1').DataTable();
        });
    </script>
@endsection
