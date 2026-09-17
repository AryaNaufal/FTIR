# Desain Teknis

Laravel menangani autentikasi, validasi, MySQL, dan penyimpanan PDF privat. Blade dan Tailwind membentuk antarmuka. ApexCharts membuat grafik garis jumlah dokumen per tanggal di Dashboard.

Satu baris pada tabel `samples` menyimpan project, `part_type`, `coa_part`, `batch_part`, nama file, dan lokasi file PDF. Kombinasi Part, COA Part, dan batch number diperiksa untuk mencegah duplikasi.

File PDF disimpan pada disk `local` dan hanya dapat diunduh melalui pengguna yang telah login. Sistem tidak menggabungkan Part A/B, tidak menyimpan hasil vendor, dan tidak memproses spektrum FTIR.
