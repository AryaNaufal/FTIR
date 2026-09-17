<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_flip(Schema::getColumnListing('samples'));

        Schema::table('samples', function (Blueprint $table) use ($columns) {
            if (! isset($columns['coa_part'])) {
                $table->string('coa_part')->nullable()->after('coa');
            }
            if (! isset($columns['batch_part'])) {
                $table->string('batch_part')->nullable()->after('coa_part');
            }
            if (! isset($columns['document_part_path'])) {
                $table->string('document_part_path')->nullable()->after('batch_part');
            }
            if (! isset($columns['document_part_name'])) {
                $table->string('document_part_name')->nullable()->after('document_part_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn(['coa_part', 'batch_part', 'document_part_path', 'document_part_name']);
        });
    }
};
