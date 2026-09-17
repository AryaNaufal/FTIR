<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->rename(true);
    }

    public function down(): void
    {
        $this->rename(false);
    }

    private function rename(bool $forward): void
    {
        $separator = ' '.mb_chr(0x2014).' ';
        $names = [
            'Epoxy resin E-51'.$separator.'pengujian demo' => 'Epoxy resin E-51 (pengujian demo)',
            'Alkyd resin 70%'.$separator.'pengujian demo' => 'Alkyd resin 70% (pengujian demo)',
            'Epoxy resin'.$separator.'standar demo' => 'Epoxy resin (standar demo)',
        ];

        DB::transaction(function () use ($names, $forward) {
            foreach ($names as $old => $new) {
                $before = $forward ? $old : $new;
                $after = $forward ? $new : $old;
                $rows = DB::table('ftir_measurements')->where('name', $before)
                    ->where('notes', 'Spektrum sintetis; tidak untuk keputusan QC.')
                    ->lockForUpdate()->get();
                foreach ($rows as $row) {
                    DB::table('ftir_measurements')->where('id', $row->id)
                        ->update(['name' => $after, 'updated_at' => now()]);
                    DB::table('audit_logs')->insert([
                        'action' => 'update_wording', 'entity' => 'ftir_measurements', 'entity_id' => $row->id,
                        'before' => json_encode(['name' => $before]), 'after' => json_encode(['name' => $after]),
                        'created_at' => now(),
                    ]);
                }
            }
        });
    }
};
