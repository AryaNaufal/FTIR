<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dailyVolumes = [1, 3, 7, 12, 16, 20, 5, 10, 14, 12];
        $dayIndex = 0;
        $documentsOnDay = 0;
        $samples = DB::table('samples')
            ->whereBetween('batch_part', ['3265118001', '3265118100'])
            ->orderBy('id')
            ->get();

        foreach ($samples as $sample) {
            if ($documentsOnDay === $dailyVolumes[$dayIndex]) {
                $dayIndex++;
                $documentsOnDay = 0;
            }
            $date = now()->startOfDay()->subDays(count($dailyVolumes) - 1 - $dayIndex);
            $documentsOnDay++;
            DB::table('samples')->where('id', $sample->id)->update([
                'received_at' => $date->toDateString(),
                'tested_at' => $date->toDateString(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    public function down(): void
    {
        // Jadwal penerimaan dokumen tidak dikembalikan ke pola sebelumnya.
    }
};
