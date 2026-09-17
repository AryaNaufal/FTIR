# Product Requirements Document (PRD)
# Sistem Manajemen Data dan Visualisasi Grafik FTIR
## Laboratorium Quality Control, PT International Paint Indonesia

| Atribut | Keterangan |
|---|---|
| Nama Dokumen | PRD Sistem Manajemen Data & Visualisasi Grafik FTIR |
| Departemen Terkait | Quality Control (QC) Laboratory |
| Versi Dokumen | 1.0 |
| Tanggal | 05 September 2026 |
| Status | Draft untuk Review |
| Teknologi Utama | Laravel, MySQL, Tailwind CSS |

---

## 1. Ringkasan Eksekutif

Laboratorium Quality Control (QC) PT International Paint Indonesia menggunakan instrumen **FTIR (Fourier Transform Infrared Spectroscopy)** untuk mengidentifikasi dan memverifikasi bahan baku, resin, serta produk cat jadi. Saat ini, proses pengelolaan data hasil pengujian FTIR, mulai dari penyimpanan hasil scan, pembandingan spektrum, hingga pembuatan laporan, masih dilakukan secara manual menggunakan file lepas (Excel, PDF, folder lokal) yang tersebar dan sulit ditelusuri.

Dokumen ini mendefinisikan kebutuhan untuk membangun **sistem berbasis web** yang memungkinkan tim QC melakukan input, penyimpanan, visualisasi grafik, pembandingan spektrum, dan pelaporan data FTIR secara terpusat, aman, dan mudah ditelusuri (traceable). Sistem akan dibangun menggunakan **Laravel** (backend/framework), **MySQL** (database), dan **Tailwind CSS** (styling antarmuka).

---

## 2. Latar Belakang & Permasalahan

### 2.1 Kondisi Saat Ini
- Data hasil pengujian FTIR disimpan secara terpisah di komputer instrumen, folder shared drive, atau hardcopy.
- Tidak ada sistem penomoran/pengarsipan sampel yang terstandarisasi dan terpusat.
- Perbandingan spektrum sampel dengan spektrum referensi/standar dilakukan manual (overlay visual di software bawaan instrumen atau Excel).
- Laporan hasil uji dibuat manual di Word/Excel, rawan human error dan tidak konsisten formatnya.
- Sulit melakukan audit trail (siapa input, kapan, revisi apa) untuk kebutuhan audit ISO/pelanggan.
- Riwayat data historis sulit dicari kembali ketika dibutuhkan untuk investigasi komplain atau re-analisis.

### 2.2 Dampak Masalah
- Waktu analisis dan pelaporan menjadi lebih lama.
- Risiko kehilangan data atau data tidak konsisten antar analis.
- Sulit melakukan tren analisis kualitas bahan baku/produk dari waktu ke waktu.
- Ketertelusuran (traceability) data lemah, berisiko saat audit internal/eksternal (ISO 9001/ISO 17025).

### 2.3 Solusi yang Diusulkan
Membangun **Sistem Manajemen Data dan Visualisasi Grafik FTIR** berbasis web yang terintegrasi dengan alur kerja laboratorium QC, mencakup input data, visualisasi grafik interaktif, pembandingan spektrum, pelaporan otomatis, serta pengarsipan data yang terstruktur dan dapat diaudit.

---

## 3. Tujuan & Sasaran

| # | Tujuan | Indikator Keberhasilan |
|---|---|---|
| 1 | Sentralisasi data hasil pengujian FTIR | 100% data pengujian baru tercatat dalam sistem |
| 2 | Mempercepat proses analisis & pelaporan | Waktu pembuatan laporan berkurang minimal 50% |
| 3 | Meningkatkan akurasi & konsistensi data | Berkurangnya revisi laporan akibat kesalahan input |
| 4 | Menyediakan visualisasi spektrum yang interaktif | Analis dapat overlay & zoom spektrum tanpa software tambahan |
| 5 | Mendukung ketertelusuran (traceability) | Tersedia audit log lengkap untuk setiap data |
| 6 | Memudahkan pencarian data historis | Waktu pencarian data lama berkurang signifikan |

---

## 4. Ruang Lingkup (Scope)

### 4.1 Termasuk dalam Ruang Lingkup (In-Scope)
- Modul autentikasi & manajemen pengguna dengan role-based access control.
- Modul manajemen data sampel (registrasi, status, metadata).
- Modul input/upload data hasil pengujian FTIR (upload file mentah + input metadata).
- Modul visualisasi grafik spektrum interaktif (zoom, pan, overlay, peak labeling).
- Modul pembandingan spektrum (sampel vs referensi/standar, vs sampel lain).
- Modul pustaka spektrum referensi (spectral library).
- Modul pelaporan hasil uji (generate, review, approval, export PDF).
- Modul dashboard ringkasan & statistik laboratorium.
- Modul pencarian dan arsip data historis.
- Modul audit trail/log aktivitas.
- Modul manajemen instrumen (riwayat kalibrasi & maintenance dasar).

### 4.2 Tidak Termasuk dalam Ruang Lingkup (Out-of-Scope), Fase 1
- Integrasi otomatis real-time langsung dengan software instrumen FTIR (direct API/driver ke alat). Pada fase 1, sistem menggunakan upload file hasil ekspor instrumen.
- Analisis kimia lanjutan berbasis machine learning (identifikasi otomatis senyawa). Fitur ini dapat menjadi roadmap fase berikutnya.
- Modul pengujian QC selain FTIR (misalnya viscometer, gloss meter, dsb.). Pengujian tersebut dapat dikembangkan sebagai modul terpisah di masa depan.
- Aplikasi mobile native (fokus fase 1 adalah web responsive).

---

## 5. Target Pengguna (User Personas)

| Role | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Admin Sistem** | IT/Admin yang mengelola user, konfigurasi sistem | Manajemen user, backup, konfigurasi master data |
| **Kepala Lab / Supervisor QC** | Menyetujui hasil pengujian & laporan | Review & approval, dashboard monitoring, laporan tren |
| **Analis QC (Laborant)** | Melakukan pengujian FTIR sehari-hari | Input data, upload hasil scan, visualisasi & bandingkan spektrum |
| **Viewer (Manajemen/QA/Produksi)** | Melihat hasil & laporan tanpa hak edit | Akses laporan, pencarian data, dashboard |

---

## 6. Kebutuhan Fungsional (Functional Requirements)

### 6.1 Modul Autentikasi & Manajemen Pengguna
- FR-1.1: Sistem menyediakan login dengan email/username dan password (autentikasi Laravel Breeze/Fortify).
- FR-1.2: Sistem mendukung role-based access control (Admin, Kepala Lab, Analis, Viewer).
- FR-1.3: Admin dapat menambah, mengedit, menonaktifkan akun pengguna.
- FR-1.4: Sistem mencatat riwayat login (waktu, IP) untuk keperluan keamanan.
- FR-1.5: Password mengikuti kebijakan minimal kompleksitas dan dapat direset oleh admin.

### 6.2 Modul Manajemen Data Sampel
- FR-2.1: Analis dapat mendaftarkan sampel baru dengan kode sampel unik (auto-generate atau manual sesuai format lab).
- FR-2.2: Metadata sampel mencakup: nama sampel, jenis produk/bahan baku, nomor batch, pelanggan/supplier (jika relevan), tanggal terima, tanggal uji, status (baru/diuji/selesai/ditolak).
- FR-2.3: Sistem mendukung pencarian dan filter sampel berdasarkan kode, tanggal, jenis produk, status.
- FR-2.4: Riwayat perubahan status sampel tercatat otomatis.

### 6.3 Modul Input & Upload Data FTIR
- FR-3.1: Sistem mendukung upload file hasil ekspor FTIR dengan format umum: `.csv`, `.txt`, `.jdx`/JCAMP-DX.
- FR-3.2: Sistem melakukan parsing otomatis data wavenumber (cm⁻¹) dan nilai absorbansi/transmitansi dari file yang diunggah.
- FR-3.3: Analis dapat melengkapi metadata pengukuran: instrumen yang digunakan, tanggal pengukuran, jumlah scan, resolusi, mode (absorbance/transmittance), operator, dan catatan tambahan.
- FR-3.4: Sistem melakukan validasi format file dan menampilkan pesan error yang jelas jika file tidak sesuai.
- FR-3.5: File mentah hasil upload tetap disimpan sebagai arsip (tidak hanya data hasil parsing).

### 6.4 Modul Visualisasi Grafik Spektrum
- FR-4.1: Sistem menampilkan grafik spektrum FTIR (sumbu X: wavenumber, sumbu Y: absorbansi/transmitansi) secara interaktif.
- FR-4.2: Pengguna dapat melakukan zoom, pan, dan reset tampilan grafik.
- FR-4.3: Sistem mendukung overlay lebih dari satu spektrum dalam satu grafik (misal: sampel vs referensi, atau beberapa batch sekaligus).
- FR-4.4: Pengguna dapat menandai (label) puncak (peak) penting secara manual pada grafik.
- FR-4.5: Grafik dapat diunduh dalam format gambar (PNG/JPG) untuk keperluan laporan.

### 6.5 Modul Pembandingan Spektrum (Spectral Comparison)
- FR-5.1: Pengguna dapat memilih dua atau lebih spektrum untuk dibandingkan secara visual (overlay).
- FR-5.2: Sistem menyediakan pustaka spektrum referensi/standar (spectral library) yang dapat digunakan sebagai pembanding.
- FR-5.3: Sistem menampilkan indikator kemiripan sederhana (misalnya berdasarkan korelasi/selisih puncak utama) antara sampel dan referensi.
- FR-5.4: Hasil pembandingan dapat disimpan sebagai bagian dari catatan pengujian.

### 6.6 Modul Pustaka Spektrum Referensi
- FR-6.1: Admin/Kepala Lab dapat menambahkan spektrum standar/referensi ke dalam pustaka, lengkap dengan kategori bahan.
- FR-6.2: Pustaka dapat dicari berdasarkan nama bahan/kategori.

### 6.7 Modul Pelaporan
- FR-7.1: Sistem dapat men-generate laporan hasil uji FTIR dalam format PDF dengan template standar perusahaan (logo, header, tabel hasil, grafik spektrum, kesimpulan).
- FR-7.2: Laporan melalui alur kerja: Draft (dibuat Analis) → Review (Kepala Lab) → Disetujui/Ditolak.
- FR-7.3: Laporan yang sudah disetujui bersifat read-only (tidak dapat diubah, hanya dapat dibuat revisi baru).
- FR-7.4: Sistem mencatat riwayat versi laporan.

### 6.8 Modul Dashboard & Statistik
- FR-8.1: Dashboard menampilkan ringkasan jumlah sampel diuji per periode (harian/bulanan).
- FR-8.2: Dashboard menampilkan status laporan (draft, menunggu review, disetujui).
- FR-8.3: Dashboard menampilkan status kalibrasi instrumen (mendekati jatuh tempo/lewat jatuh tempo).

### 6.9 Modul Pencarian & Arsip Data
- FR-9.1: Pengguna dapat mencari data historis berdasarkan kode sampel, rentang tanggal, jenis produk, atau nama analis.
- FR-9.2: Data hasil pencarian dapat diekspor ke Excel/CSV.

### 6.10 Modul Audit Trail
- FR-10.1: Setiap aksi penting (input, edit, hapus, approval) tercatat dalam log audit (user, waktu, aksi, data sebelum/sesudah).
- FR-10.2: Log audit hanya dapat dilihat oleh Admin dan Kepala Lab, tidak dapat diubah/dihapus oleh pengguna.

### 6.11 Modul Manajemen Instrumen
- FR-11.1: Admin/Kepala Lab dapat mendaftarkan instrumen FTIR beserta riwayat kalibrasi dan jadwal kalibrasi berikutnya.
- FR-11.2: Sistem memberi notifikasi/alert saat jadwal kalibrasi mendekati/terlewati.

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| Kategori | Kebutuhan |
|---|---|
| **Kinerja (Performance)** | Grafik spektrum dengan jumlah titik data standar (±2000–4000 titik) harus dapat dirender dalam waktu < 3 detik. |
| **Keamanan (Security)** | Password di-hash (bcrypt), koneksi menggunakan HTTPS, role-based access control, proteksi CSRF/SQL Injection/XSS bawaan Laravel. |
| **Ketersediaan (Reliability)** | Backup database terjadwal (harian), target uptime sistem ≥ 99% pada jam kerja. |
| **Skalabilitas** | Struktur database mampu menampung data pengujian bertahun-tahun tanpa penurunan performa signifikan. |
| **Usability** | Antarmuka responsif (mobile/tablet/desktop) menggunakan Tailwind CSS, bahasa antarmuka Indonesia. |
| **Auditabilitas** | Seluruh perubahan data penting tercatat dan dapat ditelusuri (mendukung kebutuhan audit ISO 9001/17025). |
| **Kompatibilitas Data** | Mendukung format ekspor umum instrumen FTIR (CSV, JCAMP-DX/.jdx, TXT). |
| **Maintainability** | Kode mengikuti struktur standar Laravel (MVC), terdokumentasi, mudah dikembangkan untuk fase berikutnya. |

---

## 8. Arsitektur Teknis

### 8.1 Stack Teknologi
| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 11.x (PHP 8.2+) |
| Database | MySQL 8.0 |
| Frontend Styling | Tailwind CSS |
| Interaktivitas Frontend | Blade + Alpine.js / Laravel Livewire (untuk komponen dinamis tanpa reload penuh) |
| Visualisasi Grafik | Chart.js atau Plotly.js (rendering interaktif grafik spektrum) |
| Export PDF | Laravel DomPDF / Snappy PDF |
| Export Excel | Laravel Excel (Maatwebsite/Excel) |
| Autentikasi | Laravel Breeze/Fortify |
| Web Server | Nginx/Apache |
| Storage File | Local storage / disk terenkripsi (dapat diarahkan ke storage on-premise perusahaan) |

### 8.2 Pertimbangan Arsitektur
- Mengingat data pengujian bersifat sensitif/rahasia perusahaan, sistem direkomendasikan di-deploy **on-premise** atau di **private server/cloud milik perusahaan**, bukan shared public cloud.
- Data mentah hasil FTIR (file asli) disimpan terpisah dari data hasil parsing di database, untuk menjaga integritas data asli.
- Arsitektur mengikuti pola MVC standar Laravel agar mudah dipelihara oleh tim IT internal maupun vendor.

---

## 9. Rancangan Skema Database (Ringkasan)

| Tabel | Deskripsi Singkat |
|---|---|
| `users` | Data pengguna & role |
| `roles` | Master role (admin, kepala_lab, analis, viewer) |
| `samples` | Data master sampel yang diuji |
| `ftir_measurements` | Data pengukuran FTIR per sampel (metadata instrumen, operator, tanggal) |
| `spectrum_data` | Data titik spektrum (wavenumber, absorbansi) per pengukuran |
| `spectral_library` | Pustaka spektrum referensi/standar |
| `reports` | Data laporan hasil uji beserta status approval |
| `report_versions` | Riwayat versi laporan |
| `instruments` | Master data instrumen FTIR & jadwal kalibrasi |
| `audit_logs` | Log seluruh aktivitas penting pengguna |

> Catatan: Skema detail (kolom, tipe data, relasi/ERD) akan disusun pada dokumen technical design terpisah setelah PRD disetujui.

---

## 10. Alur Kerja Utama (Key User Flow)

1. **Registrasi Sampel** → Analis membuat entri sampel baru dengan kode unik.
2. **Input Hasil Pengujian** → Analis mengunggah file hasil FTIR + melengkapi metadata pengukuran.
3. **Visualisasi & Analisis** → Analis melihat grafik spektrum, melakukan overlay dengan referensi, memberi anotasi puncak.
4. **Penyusunan Laporan** → Analis membuat draf laporan berbasis data pengujian.
5. **Review & Approval** → Kepala Lab meninjau laporan, menyetujui atau mengembalikan untuk revisi.
6. **Distribusi/Arsip** → Laporan final tersimpan dan dapat diakses/diunduh oleh pihak terkait (Viewer).
7. **Audit** → Seluruh proses di atas tercatat dalam audit log.

---

## 11. Kriteria Keberhasilan (Success Metrics)

| Metrik | Target |
|---|---|
| Adopsi sistem oleh tim QC | 100% pengujian FTIR tercatat dalam sistem dalam 3 bulan setelah go-live |
| Waktu pembuatan laporan | Berkurang ≥ 50% dibanding proses manual |
| Insiden kehilangan/duplikasi data | 0 kejadian setelah go-live |
| Kepuasan pengguna (survei internal) | Skor kepuasan ≥ 4/5 |
| Waktu pencarian data historis | Berkurang dari hitungan hari menjadi hitungan menit |

---

## 12. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Format file ekspor FTIR bervariasi antar instrumen | Kegagalan parsing data | Standarisasi format upload (CSV/JCAMP-DX) & validasi awal, sediakan template panduan ekspor |
| Resistensi pengguna terhadap sistem baru | Adopsi lambat | Pelatihan (training) dan pendampingan (super user) di awal implementasi |
| Kehilangan data akibat kegagalan server | Data pengujian hilang | Backup otomatis harian & rencana disaster recovery |
| Kesalahan hak akses (role) | Data diubah oleh pihak tidak berwenang | Implementasi RBAC ketat & audit log |
| Volume data besar dalam jangka panjang | Penurunan performa sistem | Desain database dengan indexing yang tepat & rencana arsip data lama |

---

## 13. Asumsi & Batasan

**Asumsi:**
- Tim QC memiliki akses jaringan internal perusahaan yang stabil.
- Instrumen FTIR yang digunakan dapat mengekspor data ke format CSV/JCAMP-DX/TXT.
- Perusahaan menyediakan server internal/private untuk hosting aplikasi.

**Batasan:**
- Fase 1 tidak mencakup integrasi langsung (real-time) dengan software instrumen FTIR.
- Fase 1 fokus pada satu jenis pengujian (FTIR), belum mencakup pengujian QC lainnya.

---

## 14. Rencana Implementasi (High-Level Timeline)

| Fase | Aktivitas | Estimasi Durasi |
|---|---|---|
| 1. Discovery & Requirement Finalization | Wawancara mendalam tim QC, finalisasi kebutuhan | 2 minggu |
| 2. UI/UX Design | Wireframe & desain antarmuka (Tailwind CSS) | 2 minggu |
| 3. Development Sprint 1 | Modul autentikasi, manajemen sampel, input data | 3 minggu |
| 4. Development Sprint 2 | Modul visualisasi grafik & pembandingan spektrum | 3 minggu |
| 5. Development Sprint 3 | Modul pelaporan, dashboard, audit trail | 3 minggu |
| 6. Testing (QA/UAT) | Pengujian internal & User Acceptance Test bersama tim QC | 2 minggu |
| 7. Training & Deployment | Pelatihan pengguna & go-live | 1–2 minggu |

**Total estimasi: ± 4–5 bulan** (dapat menyesuaikan kapasitas tim pengembang).

---

## 15. Lampiran

### 15.1 Glosarium
- **FTIR**: Fourier Transform Infrared Spectroscopy, teknik analisis untuk identifikasi senyawa berdasarkan spektrum inframerah.
- **Wavenumber**: Satuan sumbu-X pada spektrum FTIR (cm⁻¹).
- **Absorbansi/Transmitansi**: Satuan sumbu-Y pada spektrum FTIR.
- **JCAMP-DX**: Format file standar untuk pertukaran data spektroskopi.
- **RBAC**: Role-Based Access Control.

### 15.2 Dokumen Terkait (untuk disusun setelah PRD disetujui)
- Technical Design Document (ERD detail, API/route list)
- UI/UX Wireframe & Mockup
- Test Plan / UAT Checklist

---

*Dokumen ini merupakan draf awal PRD dan terbuka untuk direview serta disesuaikan bersama tim QC, IT, dan stakeholder terkait di PT International Paint Indonesia sebelum masuk ke tahap desain teknis.*
