<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Project</th>
                <th>Part</th>
                <th>COA Part</th>
                <th>Batch number</th>
                <th>Status validasi</th>
                <th>Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($samples as $sample)
                <tr>
                    <td><a class="code"
                            href="{{ route('samples.show', $sample->id) }}">{{ $sample->project ?: 'Project belum diisi' }}</a>
                    </td>
                    <td><span class="badge">Part {{ $sample->part_type ?: '-' }}</span></td>
                    <td>{{ $sample->coa_part ?: '-' }}</td>
                    <td>{{ $sample->batch_part ?: '-' }}</td>
                    <td>
                        @php($statuses = ['menunggu_validasi' => ['Menunggu', 'warning'], 'valid' => ['Valid', 'success'], 'tidak_sesuai' => ['Tidak sesuai', 'danger'], 'uji_ulang' => ['Uji ulang', 'retest']])
                        @php([$label, $class] = $statuses[$sample->validation_status] ?? $statuses['menunggu_validasi'])
                        <span class="badge {{ $class }}">{{ $label }}</span>
                    </td>
                    <td>{{ $sample->document_part_name ?: 'Belum ada dokumen' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Belum ada dokumen FTIR yang sesuai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
