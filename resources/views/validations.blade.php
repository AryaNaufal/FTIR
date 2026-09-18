@extends('layout')

@section('title', 'Validasi FTIR')

@section('content')
    <div x-data="{ selected: null }" @keydown.escape.window="selected = null">
        <div class="page-heading">
            <div><div class="eyebrow">PEMERIKSAAN MANUAL</div><h1>Validasi FTIR</h1><p class="muted">Bandingkan dokumen Part dengan grafik referensi bahan baku, kemudian simpan keputusan analis.</p></div>
        </div>
        <section class="panel workflow mb-6">
            <h2>Urutan validasi</h2>
            <div class="steps">
                <span>1 <b>Unduh dokumen Part</b></span>
                <span>2 <b>Unduh grafik referensi</b></span>
                <span>3 <b>Periksa secara manual</b></span>
                <span>4 <b>Simpan hasil validasi</b></span>
            </div>
        </section>
        <section class="panel">
            <div class="table-wrap"><table>
                <thead><tr><th>Project</th><th class="part-heading">Part</th><th>COA dan batch</th><th>Bahan baku</th><th>Dokumen Part</th><th>Grafik referensi</th><th class="status-heading">Status</th><th class="action-heading">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($samples as $sample)
                        <tr>
                            <td>{{ $sample->project }}</td><td class="part-cell"><span class="badge">Part {{ $sample->part_type }}</span></td>
                            <td>{{ $sample->coa_part }}<br><small>{{ $sample->batch_part }}</small></td>
                            <td>{{ $sample->material_code ? $sample->material_code . ' - ' . $sample->material_name : 'Belum dipilih' }}</td>
                            <td>@if ($sample->document_part_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($sample->document_part_path))<a class="inline-link" href="{{ route('samples.document.download', $sample->id) }}">Unduh PDF<x-ui-icon name="arrow-down-tray" /></a>@else<span class="download-unavailable" aria-disabled="true">File tidak tersedia</span>@endif</td>
                            <td>@if ($sample->reference_graph_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($sample->reference_graph_path))<a class="inline-link" href="{{ route('materials.reference.download', $sample->raw_material_id) }}">Unduh PDF<x-ui-icon name="arrow-down-tray" /></a>@else<span class="download-unavailable" aria-disabled="true">File tidak tersedia</span>@endif</td>
                            @php($statuses = ['menunggu_validasi' => ['Menunggu', 'warning'], 'valid' => ['Valid', 'success'], 'tidak_sesuai' => ['Tidak sesuai', 'danger'], 'uji_ulang' => ['Uji ulang', 'retest']])
                            @php([$label, $class] = $statuses[$sample->validation_status] ?? $statuses['menunggu_validasi'])
                            <td class="status-cell"><span class="badge {{ $class }}">{{ $label }}</span></td>
                            <td class="action-cell"><button class="text-button table-action" type="button" @click="selected = {{ Js::from(['id' => $sample->id, 'material' => $sample->raw_material_id, 'project' => $sample->project, 'part' => $sample->part_type]) }}">{{ $sample->validation_status === 'menunggu_validasi' ? 'Mulai validasi' : 'Validasi ulang' }}</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty">Belum ada dokumen untuk divalidasi.</td></tr>
                    @endforelse
                </tbody>
            </table></div>
            <div class="mt-5">{{ $samples->links() }}</div>
        </section>

        <div x-cloak x-show="selected" class="modal-backdrop" @click.self="selected = null">
            <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="validation-title">
                <div class="section-heading"><div><div class="eyebrow">VALIDASI MANUAL</div><h2 id="validation-title" x-text="selected ? selected.project + ' - Part ' + selected.part : ''"></h2><p class="muted text-sm">Pilih satu hasil setelah kedua dokumen diperiksa.</p></div><button class="icon-button" type="button" @click="selected = null" aria-label="Tutup"><x-ui-icon name="x-mark" /></button></div>
                <form method="post" action="{{ route('validations.store') }}">
                    @csrf
                    <input type="hidden" name="sample_id" :value="selected?.id">
                    <div class="form-grid">
                        <label>Bahan baku
                            <select name="raw_material_id" required :value="selected?.material">
                                <option value="">Pilih bahan baku</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->code }} - {{ $material->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Hasil validasi
                            <select name="status" required><option value="valid">Valid: sesuai referensi</option><option value="tidak_sesuai">Tidak sesuai: perlu tindak lanjut</option><option value="uji_ulang">Uji ulang: perlu pemeriksaan baru</option></select>
                        </label>
                    </div>
                    <label>Catatan pemeriksaan<textarea name="notes" required placeholder="Tuliskan alasan keputusan, misalnya: spektrum sesuai dengan grafik referensi bahan baku."></textarea><small>Catatan ini akan menjadi riwayat pemeriksaan dokumen.</small></label>
                    <button class="btn">Simpan hasil validasi</button>
                </form>
            </section>
        </div>
    </div>
@endsection
