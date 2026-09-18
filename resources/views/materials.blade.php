@extends('layout')

@section('title', 'Master bahan baku')

@section('content')
    <div x-data="{ inputOpen: @js($errors->any()) }" @keydown.escape.window="inputOpen = false">
        <div class="page-heading">
            <div>
                <div class="eyebrow">DATA REFERENSI</div>
                <h1>Master bahan baku</h1>
                <p class="muted">Kelola bahan baku dan dokumen PDF grafik referensi untuk validasi manual.</p>
            </div>
            <button class="btn" type="button" @click="inputOpen = true"><x-ui-icon name="plus" />Tambah bahan baku</button>
        </div>

        <section class="panel">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kode</th><th>Nama bahan baku</th><th>Pemasok</th><th>Kategori</th><th>Grafik referensi</th><th class="status-heading">Status</th><th class="action-heading">Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($materials as $material)
                            <tr>
                                <td class="code">{{ $material->code }}</td><td>{{ $material->name }}</td>
                                <td>{{ $material->supplier ?: '-' }}</td><td>{{ $material->category ?: '-' }}</td>
                                <td>@if ($material->reference_graph_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($material->reference_graph_path))<a class="inline-link" href="{{ route('materials.reference.download', $material->id) }}">Unduh PDF<x-ui-icon name="arrow-down-tray" /></a>@else<span class="download-unavailable" aria-disabled="true">File tidak tersedia</span>@endif</td>
                                <td class="status-cell"><span class="badge">{{ $material->active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="action-cell"><button class="text-button table-action" type="button" @click="inputOpen = {{ $material->id }}">Edit</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty">Belum ada bahan baku.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div x-cloak x-show="inputOpen === true" class="modal-backdrop" @click.self="inputOpen = false">
            <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="material-create-title">
                <div class="section-heading"><div><div class="eyebrow">INPUT BARU</div><h2 id="material-create-title">Tambah bahan baku</h2></div><button class="icon-button" type="button" @click="inputOpen = false" aria-label="Tutup"><x-ui-icon name="x-mark" /></button></div>
                @include('partials.material-form', ['material' => null, 'action' => route('materials.store')])
            </section>
        </div>

        @foreach ($materials as $material)
            <div x-cloak x-show="inputOpen === {{ $material->id }}" class="modal-backdrop" @click.self="inputOpen = false">
                <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="material-edit-{{ $material->id }}">
                    <div class="section-heading"><div><div class="eyebrow">UBAH MASTER</div><h2 id="material-edit-{{ $material->id }}">{{ $material->code }}</h2></div><button class="icon-button" type="button" @click="inputOpen = false" aria-label="Tutup"><x-ui-icon name="x-mark" /></button></div>
                    @include('partials.material-form', ['action' => route('materials.update', $material->id)])
                </section>
            </div>
        @endforeach
    </div>
@endsection
