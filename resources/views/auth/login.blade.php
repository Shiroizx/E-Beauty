@extends('layouts.app')

@section('title', 'Login - E-Beauty')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-white text-center py-4 border-bottom-0">
                    <h4 class="fw-bold text-gradient mb-1">Welcome Back!</h4>
                    <p class="text-muted small mb-0">Silakan login ke akun Anda</p>
                </div>
                
                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary fw-medium">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-secondary fw-medium">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="********">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted small">Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: var(--secondary-color)">Daftar disini</a></p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-uppercase small fw-bold text-muted mb-2">Demo Credentials</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-light text-dark border fw-normal">Admin: admin@ebeauty.com / password</span>
                    <span class="badge bg-light text-dark border fw-normal">User: customer@example.com / password</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
