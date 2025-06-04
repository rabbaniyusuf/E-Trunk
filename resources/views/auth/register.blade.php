@extends('layouts.auth')

@section('title', 'Daftar')
@section('heading', 'Buat Akun Baru')
@section('subheading', 'Bergabunglah dengan kami dan mulai perjalanan Anda.')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-floating">
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               placeholder="Nama Lengkap"
               value="{{ old('name') }}"
               required
               autofocus>
        <label for="name">Nama Lengkap</label>
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-floating">
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               placeholder="name@example.com"
               value="{{ old('email') }}"
               required>
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

    <div class="form-floating">
        <input type="password"
               class="form-control"
               id="password_confirmation"
               name="password_confirmation"
               placeholder="Konfirmasi Password"
               required>
        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input @error('terms') is-invalid @enderror"
               type="checkbox"
               name="terms"
               id="terms"
               required>
        <label class="form-check-label" for="terms">
            Saya setuju dengan <a href="#" target="_blank">syarat dan ketentuan</a>
        </label>
        @error('terms')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">
        Daftar Sekarang
    </button>

    <div class="auth-links">
        <a href="{{ route('login') }}">Sudah punya akun? Masuk</a>
    </div>
</form>
@endsection
