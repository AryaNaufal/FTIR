<?php

namespace App\Http\Controllers;

use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LabController extends Controller
{
    private function allow(array $roles = ['admin', 'analis']): void
    {
        abort_unless(in_array(auth()->user()->role, $roles, true), 403);
    }

    private function row(string $table, int $id): object
    {
        return DB::table($table)->find($id) ?? abort(404);
    }

    private function monitoringQuery(Request $request)
    {
        $query = DB::table('samples')
            ->join('users', 'users.id', '=', 'samples.user_id')
            ->select('samples.*', 'users.name as analyst');

        if ($request->filled('q')) {
            $query->where(fn ($builder) => $builder
                ->where('samples.project', 'like', '%'.$request->q.'%')
                ->orWhere('samples.coa_part', 'like', '%'.$request->q.'%')
                ->orWhere('samples.batch_part', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('part')) {
            $query->where('samples.part_type', $request->part);
        }

        return $query;
    }

    public function dashboard(Request $request)
    {
        $request->validate(['from' => 'nullable|date', 'to' => 'nullable|date|after_or_equal:from']);
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $base = DB::table('samples')->whereBetween('received_at', [$from, $to]);
        $series = (clone $base)
            ->whereNotNull('received_at')
            ->selectRaw("DATE(received_at) as date, COUNT(*) as documents, SUM(CASE WHEN validation_status = 'menunggu_validasi' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN validation_status = 'valid' THEN 1 ELSE 0 END) as valid, SUM(CASE WHEN validation_status = 'tidak_sesuai' THEN 1 ELSE 0 END) as invalid, SUM(CASE WHEN validation_status = 'uji_ulang' THEN 1 ELSE 0 END) as retest")
            ->groupByRaw('DATE(received_at)')
            ->havingRaw('COUNT(*) > 0')
            ->orderBy('date')
            ->get();

        return view('dashboard', [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('validation_status', 'menunggu_validasi')->count(),
            'valid' => (clone $base)->where('validation_status', 'valid')->count(),
            'attention' => (clone $base)->whereIn('validation_status', ['tidak_sesuai', 'uji_ulang'])->count(),
            'recent' => DB::table('samples')->latest()->limit(6)->get(),
            'series' => $series,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function samples(Request $request)
    {
        return view('samples', [
            'samples' => $this->monitoringQuery($request)->orderByDesc('samples.id')->paginate(15)->withQueryString(),
            'materials' => DB::table('raw_materials')->where('active', true)->orderBy('code')->get(),
        ]);
    }

    public function export(Request $request)
    {
        Audit::record('export', 'samples');

        return response()->streamDownload(function () use ($request) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Project', 'Part', 'COA Part', 'Batch Number', 'Nama Dokumen', 'Pembuat']);
            foreach ($this->monitoringQuery($request)->orderBy('samples.id')->cursor() as $sample) {
                fputcsv($file, [$sample->project, $sample->part_type, $sample->coa_part, $sample->batch_part, $sample->document_part_name, $sample->analyst]);
            }
            fclose($file);
        }, 'pemantauan-dokumen-ftir.csv');
    }

    public function saveSample(Request $request, ?int $id = null)
    {
        $this->allow();
        $data = $request->validate([
            'project' => 'required|string|max:180',
            'part_type' => 'required|in:A,B',
            'coa_part' => 'required|string|max:100',
            'batch_part' => 'required|string|max:100',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'document_part' => [$id ? 'nullable' : 'required', 'file', 'max:10240', 'extensions:pdf'],
            'notes' => 'nullable|string|max:5000',
        ]);
        $duplicate = DB::table('samples')
            ->where('part_type', $data['part_type'])
            ->where('coa_part', $data['coa_part'])
            ->where('batch_part', $data['batch_part'])
            ->when($id, fn ($query) => $query->where('id', '!=', $id))
            ->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['batch_part' => 'Dokumen dengan COA part dan batch number yang sama sudah terdaftar.']);
        }

        $existing = $id ? $this->row('samples', $id) : null;
        $file = $request->file('document_part');
        $path = $file?->store('part-documents/part-'.strtolower($data['part_type']), 'local');
        try {
            $save = [
                'project' => $data['project'],
                'part_type' => $data['part_type'],
                'coa_part' => $data['coa_part'],
                'batch_part' => $data['batch_part'],
                'raw_material_id' => $data['raw_material_id'],
                'document_part_path' => $path ?: $existing?->document_part_path,
                'document_part_name' => $file?->getClientOriginalName() ?: $existing?->document_part_name,
                'notes' => $data['notes'] ?? null,
                'code' => $existing?->code ?: 'DOC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'name' => $data['coa_part'].' Part '.$data['part_type'],
                'type' => 'Dokumen FTIR',
                'batch' => $data['batch_part'],
                'coa' => $data['coa_part'],
                'received_at' => $existing?->received_at ?: now()->toDateString(),
                'status' => 'selesai',
                'ftir_status' => 'sudah_dibuat',
                'result_status' => 'sudah_ada',
            ];

            DB::transaction(function () use ($id, $existing, $save) {
                if ($id) {
                    DB::table('samples')->where('id', $id)->update($save + ['updated_at' => now()]);
                    Audit::record('update', 'samples', $id, $existing, $save);
                } else {
                    $id = DB::table('samples')->insertGetId($save + ['user_id' => auth()->id(), 'created_at' => now(), 'updated_at' => now()]);
                    Audit::record('create', 'samples', $id, null, $save);
                }
            });
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }
        if ($path && $existing?->document_part_path) {
            Storage::disk('local')->delete($existing->document_part_path);
        }

        return redirect()->route('samples.index')->with('success', 'Dokumen Part '.$data['part_type'].' tersimpan.');
    }

    public function sample(int $id)
    {
        $sample = $this->row('samples', $id);

        return view('sample', [
            'sample' => $sample,
            'material' => $sample->raw_material_id ? $this->row('raw_materials', $sample->raw_material_id) : null,
            'materials' => DB::table('raw_materials')->where('active', true)->orderBy('code')->get(),
            'validationHistory' => DB::table('ftir_validations')
                ->join('users', 'users.id', '=', 'ftir_validations.user_id')
                ->where('ftir_validations.sample_id', $id)
                ->select('ftir_validations.*', 'users.name as analyst')
                ->orderByDesc('ftir_validations.validated_at')
                ->get(),
            'activityHistory' => DB::table('audit_logs')
                ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
                ->where('audit_logs.entity', 'samples')
                ->where('audit_logs.entity_id', $id)
                ->select('audit_logs.*', 'users.name as user_name')
                ->orderByDesc('audit_logs.created_at')
                ->get(),
        ]);
    }

    public function downloadDocument(int $id)
    {
        $sample = $this->row('samples', $id);
        abort_unless($sample->document_part_path && Storage::disk('local')->exists($sample->document_part_path), 404);
        Audit::record('download_document', 'samples', $id);

        return Storage::disk('local')->download($sample->document_part_path, $sample->document_part_name, ['Content-Type' => 'application/pdf']);
    }

    public function materials()
    {
        $this->allow(['admin']);

        return view('materials', ['materials' => DB::table('raw_materials')->orderBy('code')->get()]);
    }

    public function saveMaterial(Request $request, ?int $id = null)
    {
        $this->allow(['admin']);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:60', Rule::unique('raw_materials')->ignore($id)],
            'name' => 'required|string|max:180',
            'supplier' => 'nullable|string|max:180',
            'category' => 'nullable|string|max:120',
            'active' => 'required|boolean',
            'reference_graph' => ['nullable', 'file', 'max:10240', 'extensions:pdf'],
        ]);
        $existing = $id ? $this->row('raw_materials', $id) : null;
        $file = $request->file('reference_graph');
        $path = $file?->store('reference-graphs', 'local');
        try {
            $save = [
                'code' => $data['code'], 'name' => $data['name'], 'supplier' => $data['supplier'] ?? null,
                'category' => $data['category'] ?? null, 'active' => $data['active'],
                'reference_graph_path' => $path ?: $existing?->reference_graph_path,
                'reference_graph_name' => $file?->getClientOriginalName() ?: $existing?->reference_graph_name,
            ];
            DB::transaction(function () use ($id, $existing, $save) {
                if ($id) {
                    DB::table('raw_materials')->where('id', $id)->update($save + ['updated_at' => now()]);
                    Audit::record('update', 'raw_materials', $id, $existing, $save);
                } else {
                    $id = DB::table('raw_materials')->insertGetId($save + ['created_at' => now(), 'updated_at' => now()]);
                    Audit::record('create', 'raw_materials', $id, null, $save);
                }
            });
        } catch (\Throwable $exception) {
            if ($path) Storage::disk('local')->delete($path);
            throw $exception;
        }
        if ($path && $existing?->reference_graph_path) Storage::disk('local')->delete($existing->reference_graph_path);

        return back()->with('success', 'Master bahan baku tersimpan.');
    }

    public function downloadReference(int $id)
    {
        $material = $this->row('raw_materials', $id);
        abort_unless($material->reference_graph_path && Storage::disk('local')->exists($material->reference_graph_path), 404);
        Audit::record('download_reference', 'raw_materials', $id);

        return Storage::disk('local')->download($material->reference_graph_path, $material->reference_graph_name, ['Content-Type' => 'application/pdf']);
    }

    public function validations()
    {
        return view('validations', [
            'samples' => DB::table('samples')
                ->leftJoin('raw_materials', 'raw_materials.id', '=', 'samples.raw_material_id')
                ->select('samples.*', 'raw_materials.code as material_code', 'raw_materials.name as material_name', 'raw_materials.reference_graph_path', 'raw_materials.reference_graph_name')
                ->orderByDesc('samples.id')->paginate(15),
            'materials' => DB::table('raw_materials')->where('active', true)->orderBy('code')->get(),
        ]);
    }

    public function tracking(Request $request)
    {
        $query = DB::table('samples')
            ->leftJoin('raw_materials', 'raw_materials.id', '=', 'samples.raw_material_id')
            ->leftJoinSub(
                DB::table('ftir_validations')
                    ->select('sample_id', DB::raw('MAX(validated_at) as last_validated_at'))
                    ->groupBy('sample_id'),
                'latest_validation',
                fn ($join) => $join->on('latest_validation.sample_id', '=', 'samples.id')
            )
            ->select(
                'samples.*',
                'raw_materials.code as material_code',
                'raw_materials.name as material_name',
                'raw_materials.reference_graph_path',
                'latest_validation.last_validated_at'
            );

        if ($request->filled('q')) {
            $query->where(fn ($builder) => $builder
                ->where('samples.project', 'like', '%'.$request->q.'%')
                ->orWhere('samples.coa_part', 'like', '%'.$request->q.'%')
                ->orWhere('samples.batch_part', 'like', '%'.$request->q.'%')
                ->orWhere('raw_materials.code', 'like', '%'.$request->q.'%')
                ->orWhere('raw_materials.name', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('status')) {
            $query->where('samples.validation_status', $request->status);
        }
        if ($request->filled('reference')) {
            $query->whereNotNull('raw_materials.reference_graph_path');
        }

        return view('tracking', [
            'samples' => $query->orderByDesc('samples.updated_at')->paginate(15)->withQueryString(),
        ]);
    }

    public function saveValidation(Request $request)
    {
        $this->allow();
        $data = $request->validate([
            'sample_id' => 'required|exists:samples,id',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'status' => 'required|in:valid,tidak_sesuai,uji_ulang',
            'notes' => 'required|string|max:5000',
        ]);
        $sample = $this->row('samples', $data['sample_id']);
        DB::transaction(function () use ($data, $sample) {
            DB::table('ftir_validations')->insert($data + ['user_id' => auth()->id(), 'validated_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('samples')->where('id', $sample->id)->update([
                'raw_material_id' => $data['raw_material_id'], 'validation_status' => $data['status'], 'updated_at' => now(),
            ]);
            Audit::record('manual_validation', 'samples', $sample->id, ['validation_status' => $sample->validation_status], $data);
        });

        return back()->with('success', 'Hasil validasi manual tersimpan.');
    }

    public function users()
    {
        $this->allow(['admin']);

        return view('users', ['users' => DB::table('users')->select('id', 'name', 'email', 'username', 'role', 'active')->orderBy('name')->get()]);
    }

    public function saveUser(Request $request, ?int $id = null)
    {
        $this->allow(['admin']);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'username' => ['required', 'alpha_dash', 'max:60', Rule::unique('users')->ignore($id)],
            'role' => 'required|in:admin,analis',
            'active' => 'required|boolean',
            'password' => [$id ? 'nullable' : 'required', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);
        if ($id === auth()->id()) {
            abort_unless($data['active'] && $data['role'] === 'admin', 422, 'Akun admin sendiri harus tetap aktif.');
        }
        $password = $data['password'] ?? null;
        unset($data['password']);

        DB::transaction(function () use ($id, $data, $password) {
            $before = $id ? (array) $this->row('users', $id) : null;
            $save = $data;
            if ($password) {
                $save['password'] = bcrypt($password);
            }
            if ($id) {
                DB::table('users')->where('id', $id)->update($save + ['updated_at' => now()]);
            } else {
                $id = DB::table('users')->insertGetId($save + ['created_at' => now(), 'updated_at' => now()]);
            }
            if ($before) {
                unset($before['password'], $before['remember_token']);
            }
            Audit::record('save_user', 'users', $id, $before, $data + ['password_reset' => (bool) $password]);
        });

        return back()->with('success', 'Pengguna tersimpan.');
    }

    public function deleteUser(int $id)
    {
        $this->allow(['admin']);
        $user = $this->row('users', $id);
        abort_if($id === auth()->id(), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        if ($user->role === 'admin' && $user->active && DB::table('users')->where('role', 'admin')->where('active', true)->count() <= 1) {
            abort(422, 'Admin aktif terakhir tidak dapat dihapus.');
        }
        if (DB::table('samples')->where('user_id', $id)->exists() || DB::table('audit_logs')->where('user_id', $id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Pengguna memiliki riwayat data. Nonaktifkan akun melalui Edit agar riwayat tetap tersimpan.']);
        }

        DB::transaction(function () use ($id, $user) {
            DB::table('users')->where('id', $id)->delete();
            Audit::record('delete_user', 'users', $id, $user);
        });

        return back()->with('success', 'Pengguna dihapus.');
    }
}
