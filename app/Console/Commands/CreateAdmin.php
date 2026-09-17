<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'ftir:admin';

    protected $description = 'Buat admin awal secara interaktif tanpa menyimpan password pada command history';

    public function handle(): int
    {
        $data = ['name' => $this->ask('Nama admin'), 'email' => $this->ask('Email'), 'username' => $this->ask('Username'), 'password' => $this->secret('Password (12+ karakter, besar/kecil, angka, simbol)')];
        $v = Validator::make($data, ['name' => 'required|max:100', 'email' => 'required|email|unique:users', 'username' => 'required|alpha_dash|unique:users', 'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()]]);
        if ($v->fails()) {
            foreach ($v->errors()->all() as $e) {
                $this->error($e);
            }

            return self::FAILURE;
        }
        $data['password'] = bcrypt($data['password']);
        DB::transaction(function () use ($data) {
            $id = DB::table('users')->insertGetId($data + ['role' => 'admin', 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('audit_logs')->insert(['user_id' => $id, 'action' => 'bootstrap_admin', 'entity' => 'users', 'entity_id' => $id, 'created_at' => now()]);
        });
        $this->info('Admin berhasil dibuat.');

        return self::SUCCESS;
    }
}
