<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Komponen', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Printer', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Aksesoris', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Monitor', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Networking', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('kategori')->insert($kategori);
    }
}