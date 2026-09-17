<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class Audit
{
    public static function record(string $action, string $entity, ?int $id = null, mixed $before = null, mixed $after = null): void
    {
        DB::table('audit_logs')->insert(['user_id' => auth()->id(), 'action' => $action, 'entity' => $entity, 'entity_id' => $id, 'before' => $before === null ? null : json_encode($before), 'after' => $after === null ? null : json_encode($after), 'ip' => request()->ip(), 'created_at' => now()]);
    }
}
