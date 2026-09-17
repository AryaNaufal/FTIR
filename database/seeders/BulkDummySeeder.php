<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BulkDummySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        Storage::disk('local')->deleteDirectory('part-documents');
        Storage::disk('local')->deleteDirectory('raw');

        for ($number = 1; $number <= 100; $number++) {
            $part = $number % 2 === 0 ? 'A' : 'B';
            $date = $now->copy()->subDays(100 - $number);
            $coa = sprintf('COA-%s-%03d', $part, $number);
            $batch = sprintf('BN-%010d', 3265100000 + $number);
            $documentName = $coa.'_'.$batch.'.pdf';
            $documentPath = 'part-documents/part-'.strtolower($part).'/dummy-'.$number.'.pdf';
            $rawPath = 'raw/dummy-'.$number.'.csv';
            $pdf = "%PDF-1.4\n% Data dummy ".$coa."\n%%EOF";
            $csv = "wavenumber,absorbance\n4000,0.12\n3000,0.42\n2000,0.20\n";
            Storage::disk('local')->put($documentPath, $pdf);
            Storage::disk('local')->put($rawPath, $csv);

            $instrumentId = DB::table('instruments')->insertGetId([
                'name' => 'FTIR Dummy '.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'serial' => 'DUMMY-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT),
                'calibrated_at' => $date->copy()->subMonths(3)->toDateString(),
                'calibration_due' => $date->copy()->addMonths(9)->toDateString(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $sampleId = DB::table('samples')->insertGetId([
                'code' => 'DOC-DUMMY-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'name' => $coa.' Part '.$part,
                'type' => 'Dokumen FTIR',
                'batch' => $batch,
                'coa' => $coa,
                'part_type' => $part,
                'project' => 'Project Dummy '.(($number % 10) + 1),
                'coa_part' => $coa,
                'batch_part' => $batch,
                'document_part_path' => $documentPath,
                'document_part_name' => $documentName,
                'received_at' => $date->toDateString(),
                'tested_at' => $date->toDateString(),
                'status' => 'selesai',
                'ftir_status' => 'sudah_dibuat',
                'result_status' => 'sudah_ada',
                'user_id' => 2,
                'notes' => 'Data dummy untuk pengujian tampilan.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $measurementId = DB::table('ftir_measurements')->insertGetId([
                'sample_id' => $sampleId,
                'instrument_id' => $instrumentId,
                'user_id' => 2,
                'name' => 'Pengukuran dummy '.$number,
                'mode' => 'absorbance',
                'measured_at' => $date,
                'scans' => 32,
                'resolution' => 4,
                'raw_path' => $rawPath,
                'original_name' => 'dummy-'.$number.'.csv',
                'sha256' => hash('sha256', $csv),
                'notes' => 'Data dummy historis.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('spectrum_data')->insert([
                'measurement_id' => $measurementId,
                'points' => json_encode([[4000, 0.12], [3000, 0.42], [2000, 0.20]]),
            ]);
            DB::table('spectral_library')->insert([
                'measurement_id' => $measurementId,
                'category' => 'Dummy',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('instrument_events')->insert([
                'instrument_id' => $instrumentId,
                'user_id' => 2,
                'type' => 'kalibrasi',
                'performed_at' => $date->toDateString(),
                'notes' => 'Riwayat dummy.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('annotations')->insert([
                'measurement_id' => $measurementId,
                'user_id' => 2,
                'x' => 3000,
                'label' => 'Puncak dummy '.$number,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('comparisons')->insert([
                'measurement_id' => $measurementId,
                'reference_id' => $measurementId,
                'user_id' => 2,
                'correlation' => 1,
                'notes' => 'Perbandingan dummy.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $reportId = DB::table('reports')->insertGetId([
                'sample_id' => $sampleId,
                'user_id' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('report_versions')->insert([
                'report_id' => $reportId,
                'version' => 1,
                'status' => 'draft',
                'conclusion' => 'Laporan dummy.',
                'snapshot' => json_encode(['sample_id' => $sampleId]),
                'reviewer_id' => 1,
                'review_note' => 'Catatan dummy.',
                'reviewed_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('audit_logs')->insert([
                'user_id' => 2,
                'action' => 'seed_dummy',
                'entity' => 'samples',
                'entity_id' => $sampleId,
                'before' => null,
                'after' => json_encode(['source' => 'BulkDummySeeder']),
                'ip' => '127.0.0.1',
                'created_at' => $date,
            ]);
        }

        foreach ([
            'samples',
            'instruments',
            'instrument_events',
            'ftir_measurements',
            'spectrum_data',
            'spectral_library',
            'annotations',
            'comparisons',
            'reports',
            'report_versions',
            'audit_logs',
        ] as $table) {
            if (DB::table($table)->count() !== 100) {
                throw new \RuntimeException("Seeder dummy gagal: tabel {$table} harus berisi 100 data.");
            }
        }

        $this->command?->info('Setiap tabel bisnis berisi 100 data dummy.');
    }
}
