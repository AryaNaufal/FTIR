<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · IPI System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <a class="skip-link" href="#main-content">Lewati navigasi</a>
    <button x-cloak x-show="menu" @click="menu = false" class="nav-backdrop" aria-label="Tutup navigasi"></button>
    <aside id="app-navigation" :class="menu ? 'open' : ''" class="sidebar" aria-label="Navigasi utama">
        <a href="/" class="brand">
            <x-app-brand />
        </a>
        <button class="sidebar-close icon-button" @click="menu = false" aria-label="Tutup menu"><x-ui-icon
                name="x-mark" /></button>
        <div class="nav-label">RUANG KERJA</div>
        @foreach ([
        '/' => ['squares-2x2', 'Dashboard'],
        '/samples' => ['clipboard-document-list', 'Pemantauan FTIR'],
        '/validations' => ['clipboard-document-check', 'Validasi FTIR'],
        '/tracking' => ['clock', 'Tracking FTIR'],
    ] as $url => $nav)
            @php($active = $url === '/' ? request()->is('/') : request()->is(ltrim($url, '/') . '*'))
            <a class="nav-item {{ $active ? 'active' : '' }}" href="{{ $url }}"
                @if ($active) aria-current="page" @endif>
                <x-ui-icon :name="$nav[0]" />{{ $nav[1] }}
            </a>
        @endforeach
        @if (auth()->user()->role === 'admin')
            <div class="nav-label">ADMINISTRASI</div>
            <a class="nav-item {{ request()->is('materials*') ? 'active' : '' }}" href="{{ route('materials.index') }}"
                @if (request()->is('materials*')) aria-current="page" @endif><x-ui-icon name="cube" />Master bahan baku</a>
            <a class="nav-item {{ request()->is('users') ? 'active' : '' }}" href="/users"
                @if (request()->is('users')) aria-current="page" @endif><x-ui-icon name="users" />Pengguna</a>
        @endif
        <div class="sidebar-bottom">
            <div class="inline-label"><x-ui-icon name="building-office-2" />Laboratorium QC</div>
            <small>PT International Paint Indonesia</small>
        </div>
    </aside>
    <div class="workspace">
        <header>
            <button class="mobile-button icon-button" @click="menu = !menu" :aria-expanded="menu.toString()"
                aria-controls="app-navigation" aria-label="Buka navigasi"><x-ui-icon name="bars-3" /></button>
            <span class="breadcrumb">Laboratorium <x-ui-icon name="chevron-right" />
                <b>@yield('title', 'Dashboard')</b>
            </span>
            <div class="account">
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span>{{ auth()->user()->name }}<small>{{ str_replace('_', ' ', auth()->user()->role) }}</small>
                </span>
                <form action="/logout" method="post">
                    @csrf
                    <button class="text-button"><x-ui-icon name="arrow-right-on-rectangle" />Keluar</button>
                </form>
            </div>
        </header>
        <main id="main-content" tabindex="-1">
            @if (session('success'))
                <div class="notice" role="status"><x-ui-icon name="check-circle" />
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if ($errors->any())
                <div class="error" role="alert">
                    <x-ui-icon name="exclamation-triangle" />
                    <div>
                        <b>Periksa kembali data Anda.</b>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
        <footer>IPI System <span>Manajemen data FTIR · Quality Control</span>
        </footer>
    </div>
</body>

</html>
