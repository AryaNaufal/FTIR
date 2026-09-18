<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · IPI System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">
    <div class="login-story">
        <div class="brand">
            <x-app-brand />
        </div>
        <div class="login-message">
            <div class="eyebrow">RUANG KERJA LABORATORIUM</div>
            <h1>Setiap spektrum.<br>Data yang dapat ditelusuri.</h1>
            <p>Satu ruang kerja untuk analisis FTIR dan pengendalian kualitas laboratorium.</p>
            <div class="login-features">
                <span><x-ui-icon name="chart-bar-square" />Analisis spektrum</span>
                <span><x-ui-icon name="shield-check" />Ketertelusuran data</span>
            </div>
        </div>
        <small>PT INTERNATIONAL PAINT INDONESIA</small>
    </div>
    <div class="login-form">
        <form method="post" action="/login" class="panel">
            @csrf
            <div class="eyebrow">QUALITY CONTROL LABORATORY</div>
            <h2>Selamat datang kembali</h2>
            <p class="muted">Masuk untuk mengakses ruang kerja Anda.</p>
            @if ($errors->any())
                <div class="error" role="alert"><x-ui-icon name="exclamation-triangle" />
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif
            <label>Email atau username
                <input name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
            </label>
            <label>Kata sandi
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn w-full">Masuk ke ruang kerja<x-ui-icon name="arrow-right" /></button>
            <p class="auth-switch">Belum memiliki akun? <a href="{{ route('signup') }}">Daftar akun</a></p>
        </form>
    </div>
</body>

</html>
