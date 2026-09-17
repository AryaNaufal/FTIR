# IPI Spectra: Arsip Dokumen FTIR

Aplikasi web Laravel untuk menyimpan dan memantau dokumen FTIR di PT International Paint Indonesia. Satu data mewakili satu dokumen Part A atau Part B.

## Data yang disimpan

- Project
- Part A atau Part B
- COA Part, misalnya `EAA485`
- Batch number
- Satu dokumen PDF
- Catatan dan pembuat data

Sistem dipakai untuk pengarsipan serta pencarian dokumen. Vendor menggabungkan Part A/B dan membuat grafik FTIR di luar sistem. Proses tersebut tidak dicatat atau diproses aplikasi.

## Fitur

1. Login Admin dan Analis.
2. Input PDF dokumen Part melalui modal pada menu Pemantauan FTIR.
3. Pencarian dan filter berdasarkan project, COA, batch, dan Part.
4. Pencegahan dokumen duplikat untuk kombinasi Part, COA, dan batch.
5. Unduh PDF dari halaman detail dokumen.
6. Ekspor CSV.
7. Dashboard jumlah dokumen total, Part A, Part B, serta grafik garis per tanggal.

## Menjalankan lokal

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm.cmd run build
php artisan serve --host=127.0.0.1 --port=8000
```

Gunakan MySQL atau MariaDB melalui konfigurasi `DB_*` di `.env`. Seeder demo menyediakan `admin@ipi.local` dan `analis@ipi.local`, dengan password `password` untuk demo lokal.

## Pemeriksaan

```powershell
php vendor/bin/pint --test
npm.cmd run format:views:check
npm.cmd run build
php artisan test
```
