@csrf
<div class="form-grid">
    <label>Project
        <input name="project" required value="{{ old('project', $sample->project ?? '') }}">
    </label>
    <label>Part
        <select name="part_type" required>
            <option value="A" @selected(old('part_type', $sample->part_type ?? '') === 'A')>Part A</option>
            <option value="B" @selected(old('part_type', $sample->part_type ?? '') === 'B')>Part B</option>
        </select>
    </label>
    <label>COA Part
        <input name="coa_part" required placeholder="Contoh: EAA485"
            value="{{ old('coa_part', $sample->coa_part ?? '') }}">
    </label>
    <label>Batch number
        <input name="batch_part" required value="{{ old('batch_part', $sample->batch_part ?? '') }}">
    </label>
    <label>Bahan baku
        <select name="raw_material_id" required>
            <option value="">Pilih bahan baku</option>
            @foreach ($materials as $material)
                <option value="{{ $material->id }}" @selected((string) old('raw_material_id', $sample->raw_material_id ?? '') === (string) $material->id)>
                    {{ $material->code }} - {{ $material->name }}
                </option>
            @endforeach
        </select>
    </label>
    <label class="upload-box"><x-ui-icon name="document-arrow-up" />Dokumen Part (PDF)
        <input type="file" name="document_part" accept="application/pdf" @required(!isset($sample))>
        <small>{{ isset($sample) && $sample->document_part_name ? 'Kosongkan jika tidak ingin mengganti file: ' . $sample->document_part_name : 'Maksimum 10 MB.' }}</small>
    </label>
</div>
<label>Catatan
    <textarea name="notes">{{ old('notes', $sample->notes ?? '') }}</textarea>
</label>
<button class="btn">Simpan dokumen Part</button>
