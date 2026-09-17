<?php

// Read-only integrity check. Usage: php scripts/verify-backup.php <backup-directory>
$directory = $argv[1] ?? '';
$manifest = is_file($directory.'/manifest.json') ? json_decode(file_get_contents($directory.'/manifest.json'), true) : null;
if (! is_array($manifest) || empty($manifest['sha256']) || is_file($directory.'/FAILED.txt')) {
    fwrite(STDERR, "Backup tidak lengkap atau manifest tidak valid.\n");
    exit(1);
}
foreach ($manifest['sha256'] as $relative => $expected) {
    if (str_contains($relative, '..') || preg_match('/^[\\\\\/]|:/', $relative)) {
        fwrite(STDERR, "Path manifest tidak valid.\n");
        exit(1);
    }
    $file = $directory.DIRECTORY_SEPARATOR.$relative;
    if (! is_file($file) || ! hash_equals($expected, hash_file('sha256', $file))) {
        fwrite(STDERR, 'Checksum gagal: '.$relative."\n");
        exit(1);
    }
}
echo 'Integritas '.count($manifest['sha256'])." file backup terverifikasi.\n";
