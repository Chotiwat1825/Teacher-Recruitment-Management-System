@extends('adminlte::page')

@section('title', 'เพิ่มรอบการบรรจุ')

@section('content_header')
    <h1>เพิ่มรอบการบรรจุ (รอบที่ {{ $nextRoundData['round_number'] }})</h1>
@stop

@section('content')
    <form action="{{ route('admin.subjects.rounds.create') }}" method="POST">
        @csrf
        
        {{-- ข้อมูลทั่วไป --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ปีการบรรจุ</label>
                            <input type="text" class="form-control" value="{{ $nextRoundData['round_year'] }}" readonly>
                            <input type="hidden" name="round_year" value="{{ $nextRoundData['round_year'] }}">
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
                            <label>เขตพื้นที่การศึกษา</label>
                            <input type="text" class="form-control" 
                                value="{{ $education_area->where('id', $nextRoundData['education_area_id'])->first()->name_education }}" 
                                readonly>
                            <input type="hidden" name="education_area_id" value="{{ $nextRoundData['education_area_id'] }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>รอบการเรียกบรรจุ</label>
                            <input type="text" class="form-control" value="{{ $nextRoundData['round_number'] }}" readonly>
                            <input type="hidden" name="round_number" value="{{ $nextRoundData['round_number'] }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- รายการวิชาเอก --}}
        <div class="card">
            <div class="card-body">
                <div id="subject-items">
                    @foreach($nextRoundData['items'] as $index => $item)
                    <div class="subject-item mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>กลุ่มวิชาเอก</label>
                                    <input type="text" class="form-control" value="{{ $item['subject_group'] }}" readonly>
                                    <input type="hidden" name="items[{{ $index }}][subject_id]" value="{{ $item['subject_id'] }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>คงเหลือจากรอบก่อน</label>
                                    <input type="number" class="form-control" 
                                        value="{{ $item['previous_remaining'] }}" 
                                        name="items[{{ $index }}][passed_exam]" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>บรรจุรอบนี้</label>
                                    <input type="number" class="form-control vacancy-input" 
                                        name="items[{{ $index }}][vacancy]" 
                                        max="{{ $item['previous_remaining'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>หมายเหตุ</label>
                                    <input type="text" class="form-control" name="items[{{ $index }}][notes]">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">ยกเลิก</a>
        </div>
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // ตรวจสอบจำนวนบรรจุไม่เกินจำนวนคงเหลือ
    $('.vacancy-input').on('input', function() {
        let max = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        
        if (value > max) {
            $(this).val(max);
            Swal.fire({
                icon: 'warning',
                title: 'แจ้งเตือน',
                text: 'จำนวนบรรจุต้องไม่เกินจำนวนคงเหลือ',
                confirmButtonText: 'ตกลง'
            });
        }
    });
});
</script>
@endsection 