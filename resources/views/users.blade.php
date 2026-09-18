@extends('layout')

@section('title', 'Pengguna')

@section('content')
    <div x-data="{ detail: null, edit: null, remove: null, create: false }" @keydown.escape.window="detail = null; edit = null; remove = null; create = false">
        <div class="page-heading">
            <div>
                <div class="eyebrow">ADMINISTRASI</div>
                <h1>Manajemen pengguna</h1>
                <p class="muted">Lihat detail, ubah, atau hapus akun pengguna.</p>
            </div>
            <button class="btn" type="button" @click="create = true"><x-ui-icon name="plus" />Tambah pengguna</button>
        </div>

        <section class="panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="status-heading">Status</th>
                            <th class="action-heading">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td><b>{{ $user->name }}</b></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td class="status-cell"><span
                                        class="badge {{ $user->active ? '' : 'warning' }}">{{ $user->active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="action-cell">
                                    <div class="flex gap-2">
                                        <button class="text-button table-action" type="button"
                                            @click='detail = @json($user)'><x-ui-icon
                                                name="eye" />Detail</button>
                                        <button class="text-button table-action" type="button"
                                            @click='edit = @json($user)'><x-ui-icon
                                                name="pencil-square" />Edit</button>
                                        @if ($user->id !== auth()->id())
                                            <button class="text-button table-action danger" type="button"
                                                @click='remove = @json($user)'><x-ui-icon
                                                    name="trash" />Hapus</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div x-cloak x-show="detail" class="modal-backdrop" @click.self="detail = null">
            <section class="modal-panel max-w-lg" role="dialog" aria-modal="true" aria-labelledby="detail-user-title">
                <div class="section-heading">
                    <h2 id="detail-user-title">Detail pengguna</h2><button class="icon-button" type="button"
                        @click="detail = null" aria-label="Tutup detail pengguna"><x-ui-icon name="x-mark" /></button>
                </div>
                <div class="form-grid mt-5">
                    <label>Nama
                        <input disabled :value="detail?.name ?? ''">
                    </label>
                    <label>Email
                        <input disabled :value="detail?.email ?? ''">
                    </label>
                    <label>Username
                        <input disabled :value="detail?.username ?? ''">
                    </label>
                    <label>Role
                        <select disabled :value="detail?.role ?? ''">
                            <option value="admin">Admin</option>
                            <option value="analis">Analis</option>
                        </select>
                    </label>
                    <label>Status
                        <select disabled :value="detail?.active ? '1' : '0'">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </label>
                </div>
            </section>
        </div>

        <div x-cloak x-show="edit" class="modal-backdrop" @click.self="edit = null">
            <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="edit-user-title">
                <div class="section-heading">
                    <h2 id="edit-user-title">Edit pengguna</h2><button class="icon-button" type="button"
                        @click="edit = null" aria-label="Tutup edit pengguna"><x-ui-icon name="x-mark" /></button>
                </div>
                <form method="post" :action="'/users/' + (edit?.id ?? '')">
                    @csrf
                    @include('partials.user-form', ['editing' => true])
                </form>
            </section>
        </div>

        <div x-cloak x-show="create" class="modal-backdrop" @click.self="create = false">
            <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="create-user-title">
                <div class="section-heading">
                    <h2 id="create-user-title">Tambah pengguna</h2><button class="icon-button" type="button"
                        @click="create = false" aria-label="Tutup tambah pengguna"><x-ui-icon name="x-mark" /></button>
                </div>
                <form method="post" action="{{ route('users.store') }}">
                    @csrf
                    @include('partials.user-form', ['editing' => false])
                </form>
            </section>
        </div>

        <div x-cloak x-show="remove" class="modal-backdrop" @click.self="remove = null">
            <section class="modal-panel max-w-lg" role="dialog" aria-modal="true" aria-labelledby="delete-user-title">
                <div class="section-heading">
                    <h2 id="delete-user-title">Hapus pengguna</h2><button class="icon-button" type="button"
                        @click="remove = null" aria-label="Tutup hapus pengguna"><x-ui-icon name="x-mark" /></button>
                </div>
                <p>Hapus akun <b x-text="remove?.name"></b>? Tindakan ini tidak dapat dibatalkan.</p>
                <form class="mt-5 flex gap-3" method="post" :action="'/users/' + (remove?.id ?? '') + '/delete'">
                    @csrf
                    <button class="btn danger" type="submit"><x-ui-icon name="trash" />Hapus pengguna</button>
                    <button class="btn secondary" type="button" @click="remove = null">Batal</button>
                </form>
            </section>
        </div>
    </div>
@endsection
