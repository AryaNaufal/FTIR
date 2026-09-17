<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupLab extends Command
{
    protected $signature = 'ftir:backup';

    protected $description = 'Backup database konsisten dan arsip file asli ke direktori privat';

    public function handle(): int
    {
        $dir = config('ftir.backup_path').DIRECTORY_SEPARATOR.now()->format('Ymd-His').'-'.bin2hex(random_bytes(3));
        File::ensureDirectoryExists($dir);
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            if ($driver === 'sqlite') {
                $connection->getPdo()->exec('VACUUM INTO '.$connection->getPdo()->quote($dir.'/database.sqlite'));
            } elseif ($driver === 'mysql') {
                $c = $connection->getConfig();
                $process = new Process([config('ftir.mysqldump'), '--host='.$c['host'], '--port='.$c['port'], '--user='.$c['username'], '--single-transaction', '--quick', '--skip-lock-tables', '--no-tablespaces', '--result-file='.$dir.'/database.sql', $c['database']], null, ['MYSQL_PWD' => $c['password']]);
                $process->setTimeout(3600);
                $process->mustRun();
            } else {
                throw new \RuntimeException('Driver backup tidak didukung.');
            }
            $raw = Storage::disk('local')->path('raw');
            if (is_dir($raw) && ! File::copyDirectory($raw, $dir.'/raw')) {
                throw new \RuntimeException('Penyalinan file asli gagal.');
            }
            $hashes = [];
            foreach (File::allFiles($dir) as $file) {
                $hashes[$file->getRelativePathname()] = hash_file('sha256', $file->getPathname());
            }
            File::put($dir.'/manifest.json', json_encode(['created_at' => now()->toIso8601String(), 'driver' => $driver, 'sha256' => $hashes], JSON_PRETTY_PRINT));
            DB::table('audit_logs')->insert(['action' => 'backup', 'entity' => 'system', 'after' => json_encode(['directory' => basename($dir)]), 'created_at' => now()]);
            $this->info('Backup selesai: '.$dir);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            File::put($dir.'/FAILED.txt', 'Backup tidak lengkap. '.now()->toIso8601String());
            report($e);
            $this->error('Backup gagal. Lihat log aplikasi; jangan gunakan direktori tanpa manifest.json.');

            return self::FAILURE;
        }
    }
}
