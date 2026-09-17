<?php

return [
    'required' => ':attribute wajib diisi.', 'required_if' => ':attribute wajib diisi ketika :other bernilai :value.', 'required_without' => ':attribute wajib diisi jika :values kosong.',
    'string' => ':attribute harus berupa teks.', 'numeric' => ':attribute harus berupa angka.', 'integer' => ':attribute harus berupa bilangan bulat.', 'email' => ':attribute harus berupa email yang valid.', 'date' => ':attribute harus berupa tanggal yang valid.',
    'unique' => ':attribute sudah digunakan.', 'exists' => ':attribute tidak ditemukan.', 'in' => ':attribute tidak valid.', 'boolean' => ':attribute harus bernilai aktif/nonaktif.', 'array' => ':attribute harus berupa daftar.', 'distinct' => ':attribute tidak boleh duplikat.',
    'after' => ':attribute harus setelah :date.', 'after_or_equal' => ':attribute harus pada atau setelah :date.', 'regex' => 'Format :attribute tidak valid.', 'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.', 'file' => ':attribute harus berupa file.', 'extensions' => 'Ekstensi :attribute harus salah satu dari: :values.', 'uploaded' => ':attribute gagal diunggah. Periksa ukuran file dan batas upload server.',
    'max' => ['string' => ':attribute maksimal :max karakter.', 'numeric' => ':attribute maksimal :max.', 'file' => ':attribute maksimal :max KB.', 'array' => ':attribute maksimal :max item.'],
    'min' => ['string' => ':attribute minimal :min karakter.', 'numeric' => ':attribute minimal :min.', 'file' => ':attribute minimal :min KB.', 'array' => ':attribute minimal :min item.'],
    'between' => ['numeric' => ':attribute harus antara :min dan :max.'],
    'password' => ['mixed' => ':attribute harus mengandung huruf besar dan kecil.', 'numbers' => ':attribute harus mengandung angka.', 'symbols' => ':attribute harus mengandung simbol.', 'letters' => ':attribute harus mengandung huruf.'],
    'attributes' => ['name' => 'Nama', 'code' => 'Kode sampel', 'batch' => 'Nomor batch', 'received_at' => 'Tanggal terima', 'tested_at' => 'Tanggal uji', 'file' => 'File spektrum', 'reference_id' => 'Pembanding', 'instrument_id' => 'Instrumen', 'sample_id' => 'Sampel', 'password' => 'Kata sandi', 'conclusion' => 'Kesimpulan', 'review_note' => 'Catatan review', 'category' => 'Kategori', 'calibration_due' => 'Kalibrasi berikutnya', 'performed_at' => 'Tanggal pelaksanaan', 'measured_at' => 'Tanggal pengukuran', 'x' => 'Wavenumber'],
];
