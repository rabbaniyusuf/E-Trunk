<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Akun</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"/>
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST" action="{{ route('register') }}">
            @csrf
            <h2>Daftar Akun</h2>

            @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="color:red;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="input-group">
                <label>Nama</label>
                <input type="text" name="name" required placeholder="Nama">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@example.com">
            </div>

            <div class="input-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••" >
            </div>

             <button type="submit" class="btn-login">Masuk</button>
