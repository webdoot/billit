@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card">
    <div class="brand-logo d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/logo.png') }}" alt="Billit Logo" style="height: 40px; width: auto; margin-right: 12px; border-radius: 8px;">
        <span>Billit</span>
    </div>
    <div class="brand-subtitle" style="margin-bottom: 0.25rem;">
        Service Billing & Renewal Management
    </div>
    <div class="brand-tagline text-center" style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 2rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 500;">
        Track Services. Manage Renewals. Collect Payments.
    </div>

    @if (session('status'))
        <div class="alert alert-success border-0 mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-light" style="opacity: 0.5; color: #fff;">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                       type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@company.com">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-light" style="opacity: 0.5; color: #fff;">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                       type="password" name="password" required placeholder="••••••••">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-secondary" style="font-size: 0.9rem;" for="remember_me">
                Remember my session
            </label>
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit" class="btn btn-primary-glow">
                Sign In <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection
