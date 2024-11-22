@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
    <h1>เปลี่ยนรหัสผ่าน</h1>
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

                    <form method="POST" action="{{ route('admin.change-password.update') }}">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">รหัสผ่านปัจจุบัน</label>
                            <input id="current_password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror" name="current_password"
                                required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password">รหัสผ่านใหม่</label>
                            <input id="new_password" type="password"
                                class="form-control @error('new_password') is-invalid @enderror" name="new_password"
                                required>
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                            <input id="new_password_confirmation" type="password" class="form-control"
                                name="new_password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            เปลี่ยนรหัสผ่าน
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
