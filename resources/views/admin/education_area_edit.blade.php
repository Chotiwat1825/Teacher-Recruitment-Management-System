@extends('adminlte::page')

@section('title', 'แก้ไขข้อมูลเขตพื้นที่การศึกษา')

@section('content_header')
    <h1>แก้ไขข้อมูลเขตพื้นที่การศึกษา</h1>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">แก้ไขข้อมูลเขตพื้นที่การศึกษา</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.education.area.update') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $education_area->id }}">

                            <div class="form-group row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-right">ชื่อเขตพื้นที่การศึกษา</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $education_area->name_education) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        บันทึกข้อมูล
                                    </button>
                                    <a href="{{ route('admin.show_education_area') }}" class="btn btn-secondary">
                                        ยกเลิก
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
