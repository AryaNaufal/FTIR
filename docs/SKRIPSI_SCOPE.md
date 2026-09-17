# Ruang Lingkup Skripsi

## Judul yang sesuai

**Rancang Bangun Sistem Informasi Pengarsipan Dokumen FTIR Berbasis Web pada PT International Paint Indonesia.**

## Masalah

Dokumen FTIR untuk COA dan batch Part A/B dapat tersimpan di banyak folder. QC membutuhkan tempat terpusat untuk menyimpan, mencari, dan memantau jumlah dokumen tersebut.

## Data setiap dokumen

| Data | Contoh |
| --- | --- |
| Project | Marine Coating |
| Part | A |
| COA Part | EAA485 |
| Batch number | 3265118021 |
| Dokumen PDF | COA_EAA485_BN_3265118021.pdf |

## Fitur inti

1. Login Admin dan Analis.
2. Form modal input Project, Part A/B, COA Part, batch number, dan PDF.
3. Pencarian, filter Part, perubahan data, unduh PDF, serta ekspor CSV.
4. Pencegahan dokumen duplikat.
5. Dashboard total dokumen, dokumen Part A, dokumen Part B, dan grafik garis dokumen per tanggal.

## Batasan penelitian

- Sistem tidak mencampurkan Part A dan Part B.
- Sistem tidak menerima atau mengolah hasil dari vendor.
- Sistem tidak membuat, membaca, atau menganalisis grafik FTIR.
- Pencampuran dan pembuatan grafik FTIR dilakukan vendor di luar sistem.
