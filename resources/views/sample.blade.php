@extends('layout')

@section('title', 'Detail dokumen FTIR')

@section('content')
    <div class="page-heading">
        <div>
            <a class="inline-link text-sm" href="{{ route('samples.index') }}"><x-ui-icon name="arrow-left" />Pemantauan
                FTIR</a>
            <h1 class="mt-3">{{ $sample->project }}</h1>
            <p class="muted">Part {{ $sample->part_type }} · {{ $sample->coa_part }} · Batch {{ $sample->batch_part }}</p>
        </div>
        @if ($sample->document_part_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($sample->document_part_path))
            <a class="btn secondary" href="{{ route('samples.document.download', $sample->id) }}"><x-ui-icon
                    name="arrow-down-tray" />Unduh dokumen</a>
        @else
            <span class="download-unavailable" aria-disabled="true">Dokumen tidak tersedia</span>
        @endif
    </div>

    <section class="panel">
        <div class="eyebrow">DOKUMEN TERSIMPAN</div>
        <h2 class="mt-1">{{ $sample->document_part_name ?: 'Dokumen belum tersedia' }}</h2>
        <p class="muted text-sm">Dokumen ini hanya diarsipkan untuk pemantauan. Proses pencampuran dan pembuatan grafik
            dilakukan vendor di luar sistem.</p>
    </section>

    @php($statuses = ['menunggu_validasi' => ['Menunggu validasi', 'warning'], 'valid' => ['Valid', 'success'], 'tidak_sesuai' => ['Tidak sesuai', 'danger'], 'uji_ulang' => ['Uji ulang', 'retest']])
    @php([$label, $class] = $statuses[$sample->validation_status] ?? $statuses['menunggu_validasi'])
    <section class="panel mt-6">
        <div class="section-heading">
            <div>
                <div class="eyebrow">LANGKAH BERIKUTNYA</div>
                <h2>Status: <span class="badge {{ $class }}">{{ $label }}</span></h2>
                <p class="muted text-sm">{{ $material ? $material->code . ' - ' . $material->name : 'Bahan baku belum dipilih.' }}</p>
            </div>
            <a class="btn" href="{{ route('validations.index') }}"><x-ui-icon name="clipboard-document-check" />{{ $sample->validation_status === 'menunggu_validasi' ? 'Mulai validasi' : 'Lihat validasi' }}</a>
        </div>
        @if ($material?->reference_graph_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($material->reference_graph_path))
            <a class="inline-link" href="{{ route('materials.reference.download', $material->id) }}">Unduh grafik referensi<x-ui-icon name="arrow-down-tray" /></a>
        @else
            <span class="download-unavailable" aria-disabled="true">Grafik referensi tidak tersedia</span>
        @endif
    </section>

    <section class="panel mt-6">
        <div class="section-heading">
            <div>
                <div class="eyebrow">TRACKING VALIDASI</div>
                <h2>Riwayat pemeriksaan grafik FTIR</h2>
                <p class="muted text-sm">Jejak keputusan validasi untuk dokumen dan bahan baku ini.</p>
            </div>
            <a class="inline-link" href="{{ route('tracking.index') }}">Semua tracking<x-ui-icon name="arrow-right" /></a>
        </div>
        <div class="detail-list">
            @forelse ($validationHistory as $validation)
                @php($historyStatuses = ['menunggu_validasi' => ['Menunggu validasi', 'warning'], 'valid' => ['Valid', 'success'], 'tidak_sesuai' => ['Tidak sesuai', 'danger'], 'uji_ulang' => ['Uji ulang', 'retest']])
                @php([$historyLabel, $historyClass] = $historyStatuses[$validation->status] ?? $historyStatuses['menunggu_validasi'])
                <div>
                    <dt>{{ $validation->validated_at }}<small>{{ $validation->analyst }}</small></dt>
                    <dd><span class="badge {{ $historyClass }}">{{ $historyLabel }}</span><p class="mt-2 text-sm text-slate-600">{{ $validation->notes }}</p></dd>
                </div>
            @empty
                <div><dt>Belum ada pemeriksaan</dt><dd class="text-sm text-slate-600">Dokumen masih menunggu validasi manual.</dd></div>
            @endforelse
        </div>
    </section>

    <section class="panel mt-6">
        <div class="eyebrow">JEJAK AKTIVITAS</div>
        <h2>Riwayat pengelolaan dokumen</h2>
        <div class="detail-list">
            @forelse ($activityHistory as $activity)
                <div>
                    <dt>{{ $activity->created_at }}<small>{{ $activity->user_name ?: 'Sistem' }}</small></dt>
                    <dd class="text-sm text-slate-700">{{ str_replace('_', ' ', ucfirst($activity->action)) }}</dd>
                </div>
            @empty
                <div><dt>Belum ada aktivitas</dt><dd class="text-sm text-slate-600">Aktivitas dokumen akan tercatat di sini.</dd></div>
            @endforelse
        </div>
    </section>

    <section class="panel mt-6">
        <h2>Ubah data dan dokumen</h2>
        <form method="post" action="{{ route('samples.update', $sample->id) }}" enctype="multipart/form-data">
            @include('partials.sample-form')
        </form>
    </section>
@endsection
