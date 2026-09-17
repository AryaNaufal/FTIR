<form method="post" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
        <label>Kode bahan baku<input name="code" required value="{{ old('code', $material->code ?? '') }}"></label>
        <label>Nama bahan baku<input name="name" required value="{{ old('name', $material->name ?? '') }}"></label>
        <label>Pemasok<input name="supplier" value="{{ old('supplier', $material->supplier ?? '') }}"></label>
        <label>Kategori<input name="category" value="{{ old('category', $material->category ?? '') }}"></label>
        <label>Status
            <select name="active" required>
                <option value="1" @selected((string) old('active', $material->active ?? 1) === '1')>Aktif</option>
                <option value="0" @selected((string) old('active', $material->active ?? 1) === '0')>Nonaktif</option>
            </select>
        </label>
        <label class="upload-box"><x-ui-icon name="document-arrow-up" />Grafik referensi (PDF)
            <input type="file" name="reference_graph" accept="application/pdf">
            <small>{{ $material?->reference_graph_name ? 'Kosongkan jika tidak ingin mengganti file: ' . $material->reference_graph_name : 'Opsional, maksimum 10 MB.' }}</small>
        </label>
    </div>
    <button class="btn">Simpan bahan baku</button>
</form>
