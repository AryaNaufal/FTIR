<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('supplier')->nullable();
            $table->string('category')->nullable();
            $table->string('reference_graph_path')->nullable();
            $table->string('reference_graph_name')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::table('samples', function (Blueprint $table) {
            $table->foreignId('raw_material_id')->nullable()->after('user_id')->constrained();
            $table->string('validation_status')->default('menunggu_validasi')->after('result_status')->index();
        });
        Schema::create('ftir_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained();
            $table->foreignId('raw_material_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('status')->index();
            $table->text('notes');
            $table->timestamp('validated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ftir_validations');
        Schema::table('samples', function (Blueprint $table) {
            $table->dropConstrainedForeignId('raw_material_id');
            $table->dropColumn('validation_status');
        });
        Schema::dropIfExists('raw_materials');
    }
};
