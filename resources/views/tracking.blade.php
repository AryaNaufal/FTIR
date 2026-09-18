@extends('layout')

@section('title', 'Tracking FTIR')

@section('content')
    <div class="page-heading">
        <div>
            <div class="eyebrow">DATABASE TERPUSAT</div>
            <h1>Tracking Grafik FTIR</h1>
            <p class="muted">Telusuri dokumen Part, bahan baku, grafik referensi, dan status validasinya dalam satu tempat.</p>
        </div>
        <a class="btn secondary" href="{{ route('samples.index') }}"><x-ui-icon name="clipboard-document-list" />Kelola dokumen</a>
    </div>

    <section class="panel">
        <form class="filters">
            <label class="grow">Cari dokumen atau bahan baku
                <input name="q" value="{{ request('q') }}" placeholder="Project, COA, batch, atau bahan baku">
            </label>
            <label>Status validasi
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="menunggu_validasi" @selected(request('status') === 'menunggu_validasi')>Menunggu validasi</option>
                    <option value="valid" @selected(request('status') === 'valid')>Valid</option>
                    <option value="tidak_sesuai" @selected(request('status') === 'tidak_sesuai')>Tidak sesuai</option>
                    <option value="uji_ulang" @selected(request('status') === 'uji_ulang')>Uji ulang</option>
                </select>
            </label>
            <label>Grafik referensi
                <select name="reference">
                    <option value="">Semua data</option>
                    <option value="available" @selected(request('reference') === 'available')>Tersedia</option>
                </select>
            </label>
            <button class="btn"><x-ui-icon name="magnifying-glass" />Telusuri</button>
            <a class="filter-reset" href="{{ route('tracking.index') }}">Reset filter</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Dokumen</th><th>Bahan baku</th><th>Grafik referensi</th><th class="status-heading">Status validasi</th><th>Pemeriksaan terakhir</th><th class="action-heading">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($samples as $sample)
                        @php($statuses = ['menunggu_validasi' => ['Menunggu', 'warning'], 'valid' => ['Valid', 'success'], 'tidak_sesuai' => ['Tidak sesuai', 'danger'], 'uji_ulang' => ['Uji ulang', 'retest']])
                        @php([$label, $class] = $statuses[$sample->validation_status] ?? $statuses['menunggu_validasi'])
                        <tr>
                            <td><a class="code" href="{{ route('samples.show', $sample->id) }}">{{ $sample->project }}</a><small>Part {{ $sample->part_type }} · COA {{ $sample->coa_part }} · Batch {{ $sample->batch_part }}</small></td>
                            <td>{{ $sample->material_code ?: '-' }}<small>{{ $sample->material_name ?: 'Belum dipilih' }}</small></td>
                            <td><span class="badge {{ $sample->reference_graph_path ? 'success' : 'warning' }}">{{ $sample->reference_graph_path ? 'Tersedia' : 'Belum tersedia' }}</span></td>
                            <td class="status-cell"><span class="badge {{ $class }}">{{ $label }}</span></td>
                            <td>{{ $sample->last_validated_at ?: '-' }}</td>
                            <td class="action-cell"><a class="table-action" href="{{ route('samples.show', $sample->id) }}">Detail<x-ui-icon name="arrow-right" /></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Tidak ada data yang sesuai dengan pencarian tracking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $samples->links() }}</div>
    </section>
@endsection
