@extends('layouts.app')

@section('title', 'Register - E-Beauty')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-white text-center py-4 border-bottom-0">
                    <h4 class="fw-bold text-gradient mb-1">Create Account</h4>
                    <p class="text-muted small mb-0">Bergabunglah dengan E-Beauty sekarang</p>
                </div>

                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label text-secondary fw-medium">Full Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary fw-medium">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-secondary fw-medium">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="********">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label text-secondary fw-medium">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="********">
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm">
                                <i class="fas fa-user-plus me-2"></i> Register
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted small">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: var(--secondary-color)">Login disini</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
