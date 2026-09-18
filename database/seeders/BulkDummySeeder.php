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
        $projects = [
            'Marine Protective Coatings', 'Protective Coatings Indonesia', 'Yacht Finishes',
            'Automotive Refinish', 'Industrial Maintenance Coatings', 'Powder Coatings',
        ];
        $dailyVolumes = [1, 3, 7, 12, 16, 20, 5, 10, 14, 12];
        $dayIndex = 0;
        $documentsOnDay = 0;

        for ($number = 1; $number <= 100; $number++) {
            if ($documentsOnDay === $dailyVolumes[$dayIndex]) {
                $dayIndex++;
                $documentsOnDay = 0;
            }
            $part = $number % 2 === 0 ? 'A' : 'B';
            $date = $now->copy()->subDays(count($dailyVolumes) - 1 - $dayIndex);
            $documentsOnDay++;
            $coa = sprintf('IPI-COA-%s-%04d', $date->format('ym'), $number);
            $batch = sprintf('%010d', 3265118000 + $number);
            $documentName = 'FTIR_Part'.$part.'_'.$coa.'_'.$batch.'.pdf';
            $documentPath = 'part-documents/part-'.strtolower($part).'/'.$coa.'.pdf';
            $rawPath = 'raw/'.$coa.'.csv';
            $pdf = "%PDF-1.4\n% FTIR document ".$coa."\n%%EOF";
            $csv = "wavenumber,absorbance\n4000,0.12\n3000,0.42\n2000,0.20\n";
            Storage::disk('local')->put($documentPath, $pdf);
            Storage::disk('local')->put($rawPath, $csv);

            $instrumentId = DB::table('instruments')->insertGetId([
                'name' => 'Bruker ALPHA II - Unit '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'serial' => 'ALPHAII-IPI-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                'calibrated_at' => $date->copy()->subMonths(3)->toDateString(),
                'calibration_due' => $date->copy()->addMonths(9)->toDateString(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $sampleId = DB::table('samples')->insertGetId([
                'code' => 'FTIR-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT),
                'name' => $coa.' Part '.$part,
                'type' => 'Dokumen FTIR',
                'batch' => $batch,
                'coa' => $coa,
                'part_type' => $part,
                'project' => $projects[($number - 1) % count($projects)],
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
                'notes' => 'Dokumen FTIR diterima dari laboratorium QC untuk proses validasi.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            $measurementId = DB::table('ftir_measurements')->insertGetId([
                'sample_id' => $sampleId,
                'instrument_id' => $instrumentId,
                'user_id' => 2,
                'name' => 'Pengukuran FTIR '.$coa,
                'mode' => 'absorbance',
                'measured_at' => $date,
                'scans' => 32,
                'resolution' => 4,
                'raw_path' => $rawPath,
                'original_name' => $coa.'.csv',
                'sha256' => hash('sha256', $csv),
                'notes' => 'Pengukuran spektrum untuk verifikasi bahan baku.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('spectrum_data')->insert([
                'measurement_id' => $measurementId,
                'points' => json_encode([[4000, 0.12], [3000, 0.42], [2000, 0.20]]),
            ]);
            DB::table('spectral_library')->insert([
                'measurement_id' => $measurementId,
                'category' => 'Referensi bahan baku',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('instrument_events')->insert([
                'instrument_id' => $instrumentId,
                'user_id' => 2,
                'type' => 'kalibrasi',
                'performed_at' => $date->toDateString(),
                'notes' => 'Kalibrasi berkala instrumen FTIR.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('annotations')->insert([
                'measurement_id' => $measurementId,
                'user_id' => 2,
                'x' => 3000,
                'label' => 'Puncak karakteristik '.$coa,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('comparisons')->insert([
                'measurement_id' => $measurementId,
                'reference_id' => $measurementId,
                'user_id' => 2,
                'correlation' => 1,
                'notes' => 'Perbandingan dengan spektrum referensi bahan baku.',
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
                'conclusion' => 'Dokumen pengukuran FTIR siap ditinjau analis.',
                'snapshot' => json_encode(['sample_id' => $sampleId]),
                'reviewer_id' => 1,
                'review_note' => 'Menunggu proses peninjauan QC.',
                'reviewed_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            DB::table('audit_logs')->insert([
                'user_id' => 2,
                'action' => 'document_received',
                'entity' => 'samples',
                'entity_id' => $sampleId,
                'before' => null,
                'after' => json_encode(['source' => 'laboratory_qc']),
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
                throw new \RuntimeException("Seeder data operasional gagal: tabel {$table} harus berisi 100 data.");
            }
        }

        $this->command?->info('Data operasional laboratorium berhasil disiapkan.');
    }
}
