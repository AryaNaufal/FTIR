<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar · IPI System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">
    <div class="login-story">
        <div class="brand"><x-app-brand /></div>
        <div class="login-message">
            <div class="eyebrow">RUANG KERJA LABORATORIUM</div>
            <h1>Mulai dari data.<br>Kerja lebih terarah.</h1>
            <p>Daftarkan akun untuk mengelola, memvalidasi, dan menelusuri dokumen FTIR bahan baku.</p>
            <div class="login-features">
                <span><x-ui-icon name="document-check" />Validasi terdokumentasi</span>
                <span><x-ui-icon name="clock" />Tracking terpusat</span>
            </div>
        </div>
        <small>PT INTERNATIONAL PAINT INDONESIA</small>
    </div>
    <div class="login-form">
        <form method="post" action="{{ route('signup.store') }}" class="panel">
            @csrf
            <div class="eyebrow">PENDAFTARAN AKUN</div>
            <h2>Buat akun</h2>
            <p class="muted">Akun pertama otomatis menjadi administrator sistem.</p>
            @if ($errors->any())
                <div class="error" role="alert"><x-ui-icon name="exclamation-triangle" /><div>{{ $errors->first() }}</div></div>
            @endif
            <label>Nama lengkap
                <input name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            </label>
            <label>Username
                <input name="username" value="{{ old('username') }}" required autocomplete="username">
                <small>Gunakan huruf, angka, tanda hubung, atau garis bawah.</small>
            </label>
            <label>Kata sandi
                <input type="password" name="password" required autocomplete="new-password">
                <small>Minimal 12 karakter, dengan huruf besar, huruf kecil, angka, dan simbol.</small>
            </label>
            <label>Konfirmasi kata sandi
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>
            <button class="btn w-full">Daftar dan masuk<x-ui-icon name="arrow-right" /></button>
            <p class="auth-switch">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a></p>
        </form>
    </div>
</body>

</html>
