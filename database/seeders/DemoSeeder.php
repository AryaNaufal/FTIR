<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Seeder demo hanya untuk lingkungan lokal.');
        }
        if (DB::table('users')->exists()) {
            $this->command?->warn('Database sudah berisi pengguna; demo tidak ditambahkan.');

            return;
        }
        $password = Hash::make('password');
        foreach (['admin' => 'Admin Laboratorium', 'analis' => 'Analis QC'] as $role => $name) {
            DB::table('users')->insert(['name' => $name, 'email' => $role.'@ipi.local', 'username' => $role, 'role' => $role, 'active' => true, 'password' => $password, 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->command?->info('Akun demo: admin dan analis (atau email @ipi.local).');
        $this->command?->info('Kata sandi demo untuk semua akun: password');
    }
}
