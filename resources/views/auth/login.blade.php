@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <h1 class="fw-bold text-primary">เข้าสู่ระบบ</h1>
                <p class="text-muted">ระบบค้นหาข้อมูลการบรรจุครู</p>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       placeholder="กรอกอีเมลของคุณ">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="กรอกรหัสผ่านของคุณ">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember">
                                    จดจำการเข้าสู่ระบบ
                                </label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                            </button>
                        </div>

                        {{-- Forgot Password --}}
                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-muted text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>ลืมรหัสผ่าน?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="text-center mt-4">
                <p class="text-muted small">
                    &copy; {{ date('Y') }} พัฒนาโดย นายโชติวัฒน์ สัตตะพง.
                    
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 10px;
        background: white;
    }

    .form-control, .input-group-text {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #80bdff;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control {
        border-left: none;
    }

    .form-control:focus + .input-group-text {
        border-color: #80bdff;
    }

    .btn-primary {
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.15);
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    /* Animation */
    .card {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
    }
</style>
@endsection