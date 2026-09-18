<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ValidationFeatureSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('raw_materials')->exists()) {
            return;
        }

        $statuses = ['valid', 'valid', 'valid', 'menunggu_validasi', 'valid', 'tidak_sesuai', 'valid', 'uji_ulang'];
        $samples = DB::table('samples')->orderBy('id')->get();
        $materials = [
            ['Epoxy Resin E-51', 'Hexion', 'Resin Epoksi'], ['Polyamide Hardener 115', 'Evonik', 'Hardener'],
            ['Titanium Dioxide R-706', 'Chemours', 'Pigmen'], ['Barium Sulfate Blanc Fixe', 'Sachtleben', 'Extender'],
            ['Xylene Mixed Isomers', 'Shell Chemicals', 'Pelarut'], ['Butyl Acetate', 'Eastman', 'Pelarut'],
            ['Talc Mistron Vapor', 'Imerys', 'Extender'], ['Dicyclopentadiene Resin', 'ExxonMobil', 'Resin'],
        ];

        foreach (range(1, 100) as $number) {
            $code = 'RM-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
            $path = 'reference-graphs/'.$code.'.pdf';
            [$name, $supplier, $category] = $materials[($number - 1) % count($materials)];
            Storage::disk('local')->put($path, "%PDF-1.4\n% Grafik referensi {$code}\n");
            $materialId = DB::table('raw_materials')->insertGetId([
                'code' => $code,
                'name' => $name.' Grade '.str_pad((string) (100 + $number), 3, '0', STR_PAD_LEFT),
                'supplier' => $supplier,
                'category' => $category,
                'reference_graph_path' => $path,
                'reference_graph_name' => $code.'-referensi.pdf',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sample = $samples->get($number - 1);
            if (! $sample) {
                continue;
            }
            $status = $statuses[($number - 1) % count($statuses)];
            DB::table('samples')->where('id', $sample->id)->update([
                'raw_material_id' => $materialId,
                'validation_status' => $status,
                'updated_at' => now(),
            ]);
            if ($status === 'menunggu_validasi') {
                continue;
            }
            DB::table('ftir_validations')->insert([
                'sample_id' => $sample->id,
                'raw_material_id' => $materialId,
                'user_id' => $sample->user_id,
                'status' => $status,
                'notes' => match ($status) {
                    'valid' => 'Spektrum dokumen sesuai dengan grafik referensi bahan baku.',
                    'tidak_sesuai' => 'Terdapat perbedaan pada puncak karakteristik; perlu verifikasi pemasok.',
                    'uji_ulang' => 'Pengukuran ulang diperlukan untuk memastikan kesesuaian spektrum.',
                },
                'validated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
