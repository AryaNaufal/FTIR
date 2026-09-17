<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Data demo hanya boleh dipasang pada local/testing.');
        }
        $this->call(DemoSeeder::class);
        $this->call(BulkDummySeeder::class);
        $this->call(ValidationFeatureSeeder::class);
    }
}
