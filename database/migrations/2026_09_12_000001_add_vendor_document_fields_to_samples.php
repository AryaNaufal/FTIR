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
            if (! isset($columns['coa_part_a'])) {
                $table->string('coa_part_a')->nullable()->after('coa');
            }
            if (! isset($columns['batch_part_a'])) {
                $table->string('batch_part_a')->nullable()->after('coa_part_a');
            }
            if (! isset($columns['document_part_a_path'])) {
                $table->string('document_part_a_path')->nullable()->after('batch_part_a');
            }
            if (! isset($columns['document_part_a_name'])) {
                $table->string('document_part_a_name')->nullable()->after('document_part_a_path');
            }
            if (! isset($columns['coa_part_b'])) {
                $table->string('coa_part_b')->nullable()->after('document_part_a_name');
            }
            if (! isset($columns['batch_part_b'])) {
                $table->string('batch_part_b')->nullable()->after('coa_part_b');
            }
            if (! isset($columns['document_part_b_path'])) {
                $table->string('document_part_b_path')->nullable()->after('batch_part_b');
            }
            if (! isset($columns['document_part_b_name'])) {
                $table->string('document_part_b_name')->nullable()->after('document_part_b_path');
            }
            if (! isset($columns['vendor_result_path'])) {
                $table->string('vendor_result_path')->nullable()->after('document_part_b_name');
            }
            if (! isset($columns['vendor_result_name'])) {
                $table->string('vendor_result_name')->nullable()->after('vendor_result_path');
            }
            if (! isset($columns['vendor_result_uploaded_at'])) {
                $table->timestamp('vendor_result_uploaded_at')->nullable()->after('vendor_result_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn([
                'coa_part_a',
                'batch_part_a',
                'document_part_a_path',
                'document_part_a_name',
                'coa_part_b',
                'batch_part_b',
                'document_part_b_path',
                'document_part_b_name',
                'vendor_result_path',
                'vendor_result_name',
                'vendor_result_uploaded_at',
            ]);
        });
    }
};
