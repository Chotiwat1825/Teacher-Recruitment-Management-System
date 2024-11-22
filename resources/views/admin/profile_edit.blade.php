{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'แก้ไขโปรไฟล์')

@section('content_header')
    <h1>แก้ไขโปรไฟล์</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label for="name">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> บันทึกข้อมูล
                            </button>
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- แยกส่วนการเปลี่ยนรหัสผ่าน --}}
            <div class="mt-3">
                <a href="{{ route('admin.change-password') }}" class="btn btn-warning">
                    <i class="fas fa-key mr-1"></i> เปลี่ยนรหัสผ่าน
                </a>
            </div>
        </div>
    </div>
@stop
