<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DINONAKTIFKAN.
 *
 * DIHADIR menerapkan aturan ZERO DEMO DATA: sistem tidak boleh membuat
 * pertemuan, peserta, atau absensi tiruan. Seeder ini sengaja tidak membuat
 * data apa pun. Gunakan antarmuka aplikasi untuk mengisi data riil.
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn(
            'SampleDataSeeder tidak melakukan apa pun: pembuatan data contoh/dummy dilarang pada DIHADIR.'
        );
    }
}
