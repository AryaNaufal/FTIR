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

        $statuses = ['menunggu_validasi', 'valid', 'tidak_sesuai', 'uji_ulang'];
        $samples = DB::table('samples')->orderBy('id')->get();

        foreach (range(1, 100) as $number) {
            $code = 'RM-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
            $path = 'reference-graphs/'.$code.'.pdf';
            Storage::disk('local')->put($path, "%PDF-1.4\n% Grafik referensi dummy {$code}\n");
            $materialId = DB::table('raw_materials')->insertGetId([
                'code' => $code,
                'name' => 'Bahan Baku Dummy '.$number,
                'supplier' => 'Pemasok Dummy '.(($number % 5) + 1),
                'category' => 'Kategori '.(($number % 4) + 1),
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
            DB::table('ftir_validations')->insert([
                'sample_id' => $sample->id,
                'raw_material_id' => $materialId,
                'user_id' => $sample->user_id,
                'status' => $status,
                'notes' => $status === 'menunggu_validasi' ? 'Data dummy menunggu validasi.' : 'Hasil validasi dummy.',
                'validated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
