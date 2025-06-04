@extends('layouts.auth')

@section('title', 'Login')
@section('heading', 'Login ke Akun Anda')
@section('subheading', 'Selamat datang kembali! Silakan masuk ke akun Anda.')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-floating">
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               placeholder="name@example.com"
               value="{{ old('email') }}"
               required
               autofocus>
        <label for="email">Alamat Email</label>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-floating">
        <input type="password"
               class="form-control @error('password') is-invalid @enderror"
               id="password"
               name="password"
               placeholder="Password"
               required>
        <label for="password">Kata Sandi</label>
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Ingat saya
        </label>
    </div>

    <button type="submit" class="btn btn-primary">
        Masuk
    </button>

    <div class="auth-links">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
            <span class="separator">|</span>
        @endif
        <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
    </div>
</form>
@endsection
