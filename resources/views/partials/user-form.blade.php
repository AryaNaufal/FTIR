<div class="form-grid">
    <label>Nama
        <input name="name" required :value="{{ $editing ? 'edit?.name' : "''" }}">
    </label>
    <label>Email
        <input type="email" name="email" required :value="{{ $editing ? 'edit?.email' : "''" }}">
    </label>
    <label>Username
        <input name="username" required :value="{{ $editing ? 'edit?.username' : "''" }}">
    </label>
    <label>Role
        <select name="role" :value="{{ $editing ? 'edit?.role' : "'analis'" }}">
            <option value="admin">Admin</option>
            <option value="analis">Analis</option>
        </select>
    </label>
    <label>Status
        <select name="active" :value="{{ $editing ? "edit?.active ? '1' : '0'" : "'1'" }}">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </label>
    <label>{{ $editing ? 'Kata sandi baru (opsional)' : 'Kata sandi' }}
        <input type="password" name="password" minlength="12" @required(!$editing) autocomplete="new-password">
    </label>
</div>
<p class="muted text-sm mb-3">Kata sandi minimal 12 karakter, huruf besar/kecil, angka, dan simbol.</p>
<button class="btn">Simpan pengguna</button>
