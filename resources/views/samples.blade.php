@extends('layout')

@section('title', 'Pemantauan FTIR')

@section('content')
    <div x-data="{ inputOpen: @js($errors->any()) }" @keydown.escape.window="inputOpen = false">
        <div class="page-heading">
            <div>
                <div class="eyebrow">ARSIP DOKUMEN FTIR</div>
                <h1>Pemantauan FTIR</h1>
                <p class="muted">Simpan dan cari dokumen PDF berdasarkan project, Part A/B, COA, dan batch number.</p>
            </div>
            <div class="flex gap-3">
                <a class="btn secondary" href="/samples/export?{{ http_build_query(request()->query()) }}"><x-ui-icon
                        name="arrow-down-tray" />Ekspor CSV</a>
                <button class="btn" type="button" @click="inputOpen = true"><x-ui-icon name="plus" />Input dokumen
                    Part</button>
            </div>
        </div>

        <section class="panel">
            <form class="filters">
                <label class="grow">Cari
                    <input name="q" placeholder="Project, COA Part, atau batch number" value="{{ request('q') }}">
                </label>
                <label>Part
                    <select name="part">
                        <option value="">Semua Part</option>
                        <option value="A" @selected(request('part') === 'A')>Part A</option>
                        <option value="B" @selected(request('part') === 'B')>Part B</option>
                    </select>
                </label>
                <button class="btn">Cari</button>
                <a href="{{ route('samples.index') }}">Reset</a>
            </form>
            @include('partials.sample-table')
            <div class="mt-5">{{ $samples->links() }}</div>
        </section>

        <div x-cloak x-show="inputOpen" class="modal-backdrop" @click.self="inputOpen = false">
            <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="input-ftir-title">
                <div class="section-heading">
                    <div>
                        <div class="eyebrow">INPUT BARU</div>
                        <h2 id="input-ftir-title">Input dokumen Part</h2>
                        <p class="muted text-sm">Satu input digunakan untuk satu dokumen Part A atau Part B.</p>
                    </div>
                    <button class="icon-button" type="button" @click="inputOpen = false"
                        aria-label="Tutup form input"><x-ui-icon name="x-mark" /></button>
                </div>
                <form method="post" action="{{ route('samples.store') }}" enctype="multipart/form-data">
                    @include('partials.sample-form')
                </form>
            </section>
        </div>
    </div>
@endsection
