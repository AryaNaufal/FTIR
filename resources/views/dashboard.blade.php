@extends('layout')

@section('title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">RINGKASAN ARSIP DOKUMEN</div>
            <h1>Dashboard FTIR</h1>
            <p class="muted">Pantau dokumen FTIR dan status validasi manual pada periode terpilih.</p>
        </div>
    </div>

    <div class="stats simple-stats">
        <div class="stat"><span class="stat-label">Total dokumen<x-ui-icon
                    name="document-text" /></span><strong>{{ $total }}</strong><small>Dalam periode terpilih</small>
        </div>
        <div class="stat"><span class="stat-label">Menunggu validasi<x-ui-icon
                    name="clock" /></span><strong>{{ $pending }}</strong><small>Perlu diperiksa analis</small></div>
        <div class="stat"><span class="stat-label">Valid<x-ui-icon
                    name="check-circle" /></span><strong>{{ $valid }}</strong><small>Sudah sesuai referensi</small></div>
        <div class="stat"><span class="stat-label">Perlu tindak lanjut<x-ui-icon
                    name="exclamation-circle" /></span><strong>{{ $attention }}</strong><small>Tidak sesuai atau uji ulang</small></div>
    </div>

    <section class="panel mb-10">
        <div class="section-heading">
            <div>
                <h2>Grafik status validasi</h2>
                <p class="muted text-sm">Jumlah dokumen berdasarkan status validasi per tanggal input.</p>
            </div>
        </div>
        <form class="filters filter-panel">
            <label>Periode mulai
                <input type="date" name="from" value="{{ $from }}">
            </label>
            <label>Periode sampai
                <input type="date" name="to" value="{{ $to }}">
            </label>
            <button class="btn secondary"><x-ui-icon name="adjustments-horizontal" />Tampilkan grafik</button>
        </form>
        <div id="document-monitoring-chart" class="chart" aria-label="Grafik garis status validasi FTIR"></div>
        <script id="document-monitoring-payload" type="application/json">@json($series)</script>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <h2>Dokumen terbaru</h2>
                <p class="muted text-sm">Pilih dokumen untuk melihat atau memperbarui datanya.</p>
            </div>
            <a class="inline-link" href="{{ route('samples.index') }}">Lihat semua<x-ui-icon name="arrow-right" /></a>
        </div>
        @include('partials.sample-table', ['samples' => $recent])
    </section>
@endsection
