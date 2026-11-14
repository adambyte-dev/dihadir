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
        // Production Ready: Seeder hanya memanggil seeder inisialisasi resmi (tanpa data demo).
        $this->call([
            SuperAdminSeeder::class,
        ]);
    }
}
