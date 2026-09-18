<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LabTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'analis'): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role, 'active' => true, 'username' => 'user'.$user->id])->save();

        return $user;
    }

    private function documentData(array $overrides = []): array
    {
        $materialId = DB::table('raw_materials')->insertGetId([
            'code' => 'RM-'.str_pad((string) (DB::table('raw_materials')->count() + 1), 3, '0', STR_PAD_LEFT),
            'name' => 'Pigmen Uji',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return array_merge([
            'project' => 'Marine Coating',
            'part_type' => 'A',
            'coa_part' => 'EAA485',
            'batch_part' => '3265118021',
            'raw_material_id' => $materialId,
            'document_part' => UploadedFile::fake()->create('part-a.pdf', 100, 'application/pdf'),
            'notes' => 'Dokumen Part A.',
        ], $overrides);
    }

    public function test_document_archive_routes_are_available_and_vendor_routes_are_not_exposed(): void
    {
        $this->withoutVite();
        $this->actingAs($this->user());

        foreach (['/', '/samples', '/validations', '/tracking'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (['/spectra/1', '/library', '/reports', '/instruments', '/audit', '/samples/1/vendor-result'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_analyst_can_store_and_download_a_part_document(): void
    {
        $this->withoutVite();
        Storage::fake('local');
        $this->actingAs($this->user());
        $data = $this->documentData();

        $this->post('/samples', $data)->assertRedirect('/samples');
        $sample = DB::table('samples')->first();
        $this->assertDatabaseHas('samples', ['id' => $sample->id, 'coa_part' => 'EAA485', 'part_type' => 'A']);
        Storage::disk('local')->assertExists($sample->document_part_path);
        $this->get('/samples/'.$sample->id)->assertOk()->assertSee('Dokumen Part A');
        $this->get('/tracking?q=Marine')->assertOk()->assertSee('Tracking Grafik FTIR')->assertSee('Marine Coating');
        $this->get('/samples/'.$sample->id.'/document')->assertOk();
        $this->get('/')->assertOk()->assertSee('document-monitoring-payload');
    }

    public function test_analyst_can_record_a_manual_validation(): void
    {
        Storage::fake('local');
        $this->actingAs($this->user());
        $this->post('/samples', $this->documentData())->assertRedirect();
        $sample = DB::table('samples')->first();

        $this->post('/validations', [
            'sample_id' => $sample->id,
            'raw_material_id' => $sample->raw_material_id,
            'status' => 'valid',
            'notes' => 'Dokumen sesuai dengan grafik referensi.',
        ])->assertRedirect();

        $this->assertDatabaseHas('samples', ['id' => $sample->id, 'validation_status' => 'valid']);
        $this->assertDatabaseHas('ftir_validations', ['sample_id' => $sample->id, 'status' => 'valid']);
    }

    public function test_only_admin_can_manage_raw_materials(): void
    {
        $this->actingAs($this->user())->get('/materials')->assertForbidden();
        $this->actingAs($this->user('admin'));

        $this->post('/materials', [
            'code' => 'RM-001', 'name' => 'Resin Uji', 'supplier' => 'Pemasok Uji', 'category' => 'Resin', 'active' => 1,
            'reference_graph' => UploadedFile::fake()->create('referensi.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $this->assertDatabaseHas('raw_materials', ['code' => 'RM-001']);
        $this->get('/materials')->assertOk()->assertSee('Resin Uji');
    }

    public function test_duplicate_part_document_and_invalid_file_are_rejected(): void
    {
        Storage::fake('local');
        $this->actingAs($this->user());
        $this->post('/samples', $this->documentData())->assertRedirect();
        $this->post('/samples', $this->documentData())->assertSessionHasErrors('batch_part');
        $this->assertDatabaseCount('samples', 1);

        $invalid = $this->documentData([
            'coa_part' => 'EAA500',
            'batch_part' => '3265119000',
            'document_part' => UploadedFile::fake()->create('part-a.csv', 10, 'text/csv'),
        ]);
        $this->post('/samples', $invalid)->assertSessionHasErrors('document_part');
        $this->assertDatabaseCount('samples', 1);
    }

    public function test_only_admin_can_manage_two_user_roles(): void
    {
        $analyst = $this->user();
        $this->actingAs($analyst)->get('/users')->assertForbidden();
        $this->actingAs($this->user('admin'));

        $this->post('/users', ['name' => 'Analis Baru', 'email' => 'baru@example.com', 'username' => 'analisbaru', 'role' => 'analis', 'active' => 1, 'password' => 'ValidPassword!789'])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'baru@example.com', 'role' => 'analis']);
        $newUser = DB::table('users')->where('email', 'baru@example.com')->first();
        $this->get('/users')->assertOk()->assertSee('Analis Baru')->assertSee('Detail')->assertSee('Hapus');
        $this->post('/users/'.$newUser->id.'/delete')->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
        $this->post('/users', ['name' => 'Role Lama', 'email' => 'lama@example.com', 'username' => 'rolelama', 'role' => 'viewer', 'active' => 1, 'password' => 'ValidPassword!789'])->assertSessionHasErrors('role');
    }
}
