# Pedoman tampilan IPI System

Antarmuka menerapkan Contrast, Repetition, Alignment, dan Proximity (CRAP).

| Prinsip | Penerapan |
|---|---|
| Contrast | Teks slate gelap pada permukaan putih, tombol utama biru muda dengan teks navy, judul lebih tegas, status peringatan tetap memiliki label dan warna tersendiri. |
| Repetition | Komponen `.btn`, `.panel`, `.badge`, `.icon`, dan `.form-grid` digunakan berulang. Ikon outline berukuran dasar 20 px, ikon tombol 18 px, serta tombol minimal 44 px. |
| Alignment | Sidebar 256 px, konten dan header memiliki garis tepi sejajar, label dan input tersusun vertikal, filter sejajar pada bagian bawah, kartu dan form memakai grid responsif. |
| Proximity | Label berjarak 8 px dari input, field dalam grid berjarak 20 px, panel 24 px, dan judul halaman terpisah 32 px dari konten. Judul, penjelasan, serta aksi terkait dikelompokkan dalam satu bagian. |

Palet utama ada di `tailwind.config.js`: primary `#38BDF8`, accent `#BAE6FD`, teks navy `#082F49`. Komponen dan aturan responsif berada di `resources/css/app.css`.

Ikon menggunakan [Blade Heroicons](https://github.com/driesvints/blade-heroicons), dipasang lewat Composer dan dirender sebagai SVG lokal. Tidak ada CDN ikon. Gunakan komponen bersama:

```blade
<x-ui-icon name="beaker" />
<button class="btn"><x-ui-icon name="plus" />Registrasi sampel</button>
```

`x-app-brand` menggunakan ikon beaker dan wordmark IPI System sebagai identitas aplikasi, bukan pengganti logo resmi perusahaan. PDF menggunakan ikon dari library yang sama.

Ikon dekoratif memakai `aria-hidden`, tombol yang hanya berisi ikon memiliki label, dan menu aktif memiliki `aria-current`. Navigasi mobile menyediakan backdrop, tombol tutup, Escape, serta indikator expanded. Tautan lewati navigasi dan indikator fokus tersedia untuk keyboard. Tabel lebar dapat digeser dalam panel tanpa membuat seluruh halaman melebar.
