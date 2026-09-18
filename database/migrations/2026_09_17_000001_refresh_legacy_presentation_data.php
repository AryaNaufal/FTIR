<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $projects = [
            'Marine Protective Coatings', 'Protective Coatings Indonesia', 'Yacht Finishes',
            'Automotive Refinish', 'Industrial Maintenance Coatings', 'Powder Coatings',
        ];
        $materials = [
            ['Epoxy Resin E-51', 'Hexion', 'Resin Epoksi'], ['Polyamide Hardener 115', 'Evonik', 'Hardener'],
            ['Titanium Dioxide R-706', 'Chemours', 'Pigmen'], ['Barium Sulfate Blanc Fixe', 'Sachtleben', 'Extender'],
            ['Xylene Mixed Isomers', 'Shell Chemicals', 'Pelarut'], ['Butyl Acetate', 'Eastman', 'Pelarut'],
            ['Talc Mistron Vapor', 'Imerys', 'Extender'], ['Dicyclopentadiene Resin', 'ExxonMobil', 'Resin'],
        ];

        DB::table('samples')
            ->where('code', 'like', 'DOC-DUMMY-%')
            ->orWhere('project', 'like', 'Project Dummy%')
            ->orderBy('id')
            ->get()
            ->each(function (object $sample, int $index) use ($projects): void {
                $number = $index + 1;
                $coa = sprintf('IPI-COA-%s-%04d', now()->format('ym'), $number);
                $batch = sprintf('%010d', 3265118000 + $number);
                DB::table('samples')->where('id', $sample->id)->update([
                    'code' => 'FTIR-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT),
                    'name' => $coa.' Part '.$sample->part_type,
                    'batch' => $batch,
                    'coa' => $coa,
                    'project' => $projects[$index % count($projects)],
                    'coa_part' => $coa,
                    'batch_part' => $batch,
                    'document_part_name' => 'FTIR_Part'.$sample->part_type.'_'.$coa.'_'.$batch.'.pdf',
                    'notes' => 'Dokumen FTIR diterima dari laboratorium QC untuk proses validasi.',
                    'updated_at' => now(),
                ]);
            });

        DB::table('raw_materials')
            ->where('name', 'like', 'Bahan Baku Dummy%')
            ->orderBy('id')
            ->get()
            ->each(function (object $material, int $index) use ($materials): void {
                [$name, $supplier, $category] = $materials[$index % count($materials)];
                DB::table('raw_materials')->where('id', $material->id)->update([
                    'name' => $name.' Grade '.str_pad((string) (101 + $index), 3, '0', STR_PAD_LEFT),
                    'supplier' => $supplier,
                    'category' => $category,
                    'reference_graph_name' => $material->code.'-referensi.pdf',
                    'updated_at' => now(),
                ]);
            });

        DB::table('ftir_validations')->where('notes', 'like', '%dummy%')->orderBy('id')->get()->each(function (object $validation): void {
            $notes = match ($validation->status) {
                'valid' => 'Spektrum dokumen sesuai dengan grafik referensi bahan baku.',
                'tidak_sesuai' => 'Terdapat perbedaan pada puncak karakteristik; perlu verifikasi pemasok.',
                'uji_ulang' => 'Pengukuran ulang diperlukan untuk memastikan kesesuaian spektrum.',
                default => 'Dokumen menunggu proses validasi oleh analis QC.',
            };
            DB::table('ftir_validations')->where('id', $validation->id)->update(['notes' => $notes, 'updated_at' => now()]);
        });

        DB::table('audit_logs')->where('action', 'seed_dummy')->update(['action' => 'document_received']);
    }

    public function down(): void
    {
        // Data operasional tidak dikembalikan ke label lama.
    }
};
