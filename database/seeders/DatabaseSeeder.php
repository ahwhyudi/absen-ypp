<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bikin 10 user acak (karena Factory udah diupdate, ini bakal jalan normal)
        // User::factory(10)->create();

        // // Bikin akun Test User bawaan Laravel (tambahin username-nya)
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'username' => 'testuser', // <-- Tambahin baris ini
        //     'email' => 'test@example.com',
        // ]);

        // Panggil RoleSeeder yang udah kita buat khusus untuk Absensi
        $this->call([
            RoleSeeder::class,
        ]);
    }
}
