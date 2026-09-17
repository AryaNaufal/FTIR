<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['kepala_lab', 'viewer'])
            ->update(['role' => 'analis', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Peran lama tidak dapat dipulihkan secara andal setelah disederhanakan.
    }
};
