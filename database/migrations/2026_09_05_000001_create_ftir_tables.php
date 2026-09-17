<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->nullable()->unique();
            $t->string('role')->default('viewer');
            $t->boolean('active')->default(true);
        });
        Schema::create('samples', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->string('type')->index();
            $t->string('batch');
            $t->string('supplier')->nullable();
            $t->date('received_at')->index();
            $t->date('tested_at')->nullable();
            $t->string('status')->default('baru')->index();
            $t->foreignId('user_id')->constrained();
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('instruments', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('serial')->unique();
            $t->date('calibrated_at');
            $t->date('calibration_due')->index();
            $t->timestamps();
        });
        Schema::create('instrument_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('instrument_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->string('type');
            $t->date('performed_at');
            $t->text('notes');
            $t->timestamps();
        });
        Schema::create('ftir_measurements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sample_id')->nullable()->constrained();
            $t->foreignId('instrument_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->string('name');
            $t->string('mode');
            $t->dateTime('measured_at');
            $t->unsignedInteger('scans');
            $t->decimal('resolution', 8, 3);
            $t->string('raw_path');
            $t->string('original_name');
            $t->string('sha256', 64);
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('spectrum_data', function (Blueprint $t) {
            $t->id();
            $t->foreignId('measurement_id')->unique()->constrained('ftir_measurements');
            $t->json('points');
        });
        Schema::create('spectral_library', function (Blueprint $t) {
            $t->id();
            $t->foreignId('measurement_id')->unique()->constrained('ftir_measurements');
            $t->string('category')->index();
            $t->timestamps();
        });
        Schema::create('annotations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('measurement_id')->constrained('ftir_measurements');
            $t->foreignId('user_id')->constrained();
            $t->double('x');
            $t->string('label');
            $t->timestamps();
        });
        Schema::create('comparisons', function (Blueprint $t) {
            $t->id();
            $t->foreignId('measurement_id')->constrained('ftir_measurements');
            $t->foreignId('reference_id')->constrained('ftir_measurements');
            $t->foreignId('user_id')->constrained();
            $t->double('correlation');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sample_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->timestamps();
        });
        Schema::create('report_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained();
            $t->unsignedInteger('version');
            $t->string('status')->default('draft')->index();
            $t->text('conclusion');
            $t->json('snapshot');
            $t->foreignId('reviewer_id')->nullable()->constrained('users');
            $t->text('review_note')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
            $t->unique(['report_id', 'version']);
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained();
            $t->string('action')->index();
            $t->string('entity');
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->json('before')->nullable();
            $t->json('after')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'report_versions', 'reports', 'comparisons', 'annotations', 'spectral_library', 'spectrum_data', 'ftir_measurements', 'instrument_events', 'instruments', 'samples'] as $table) {
            Schema::dropIfExists($table);
        } Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['username', 'role', 'active']));
    }
};
