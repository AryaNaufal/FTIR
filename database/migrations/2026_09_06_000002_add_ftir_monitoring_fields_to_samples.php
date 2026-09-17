<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->string('coa')->nullable()->after('batch');
            $table->string('part_type', 1)->nullable()->after('coa');
            $table->string('project')->nullable()->after('part_type');
            $table->string('ftir_status')->default('belum_dibuat')->after('status');
            $table->string('result_status')->default('belum_ada')->after('ftir_status');
            $table->index(['batch', 'coa', 'part_type', 'project'], 'samples_monitoring_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropIndex('samples_monitoring_lookup');
            $table->dropColumn(['coa', 'part_type', 'project', 'ftir_status', 'result_status']);
        });
    }
};
