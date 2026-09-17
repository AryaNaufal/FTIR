<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class ImportSqlite extends Command
{
    protected $signature = 'ftir:import-sqlite {source : Path database SQLite sumber}';

    protected $description = 'Salin data bisnis SQLite ke database MySQL kosong yang sudah dimigrasikan';

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->error('Koneksi tujuan harus mysql.');

            return self::FAILURE;
        }
        $source = realpath($this->argument('source'));
        if (! $source || ! is_file($source)) {
            $this->error('File SQLite sumber tidak ditemukan.');

            return self::FAILURE;
        }
        $tables = ['users', 'instruments', 'samples', 'instrument_events', 'ftir_measurements', 'spectrum_data', 'spectral_library', 'annotations', 'comparisons', 'reports', 'report_versions', 'audit_logs'];
        foreach ($tables as $table) {
            if (DB::table($table)->exists()) {
                $this->error('Tujuan harus kosong. Tabel '.$table.' sudah berisi data.');

                return self::FAILURE;
            }
        }
        $sqlite = new PDO('sqlite:'.$source, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $sqlite->exec('PRAGMA query_only = ON');
        $sqlite->beginTransaction();
        try {
            DB::transaction(function () use ($sqlite, $tables) {
                foreach ($tables as $table) {
                    $rows = $sqlite->query('SELECT * FROM "'.$table.'" ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
                    foreach (array_chunk($rows, 100) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                    if (DB::table($table)->count() !== count($rows)) {
                        throw new \RuntimeException('Jumlah baris berbeda pada '.$table);
                    }
                    foreach ($rows as $row) {
                        $copy = (array) DB::table($table)->find($row['id']);
                        foreach ($row as $key => $value) {
                            if ($value === null ? $copy[$key] !== null : (string) $copy[$key] !== (string) $value) {
                                // MySQL can normalize numeric and JSON representations.
                                if (is_numeric($value) && is_numeric($copy[$key]) && (float) $value === (float) $copy[$key]) {
                                    continue;
                                }
                                if (is_string($value) && in_array($key, ['points', 'snapshot', 'before', 'after']) && json_decode($value, true) === json_decode($copy[$key], true)) {
                                    continue;
                                }
                                throw new \RuntimeException('Verifikasi berbeda: '.$table.'.'.$key.' #'.$row['id']);
                            }
                        }
                    }
                    $this->line($table.': '.count($rows).' baris terverifikasi');
                }
                DB::table('audit_logs')->insert(['action' => 'import_sqlite', 'entity' => 'system', 'after' => json_encode(['tables' => $tables]), 'created_at' => now()]);
            });
            $sqlite->commit();
        } catch (\Throwable $e) {
            $sqlite->rollBack();
            throw $e;
        }
        $this->info('Impor selesai. File SQLite sumber tidak diubah. Sesi login/cache tidak disalin.');

        return self::SUCCESS;
    }
}
