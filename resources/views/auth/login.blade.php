<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - E-TRANK</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="login-container">
    <form class="login-form" method="POST" action="{{ route('login') }}">
      @csrf
      <h2>Login ke Akun Anda</h2>

      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="email@example.com" />
      </div>

      <div class="input-group">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" required placeholder="••••••••" />
      </div>

      <button type="submit" class="btn-login">Masuk</button>

      <div class="extra-links">
        <a href="#">Lupa Kata Sandi?</a>
        <span>|</span>
        <a href="{{ route('register')}}">Daftar</a>
      </div>
    </form>
  </div>
</body>
</html>
