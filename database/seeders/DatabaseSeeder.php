<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ekspedisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ← jangan lupa import

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder yang sudah ada
        $this->call(AturanChatbotSeeder::class);

        // User & Ekspedisi awal (tetap)
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin NJK', 'password' => bcrypt('password'), 'peran' => 'admin']
        );
        User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            ['name' => 'Kasir NJK', 'password' => bcrypt('password'), 'peran' => 'kasir']
        );
        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'Pelanggan', 'password' => bcrypt('password'), 'peran' => 'pelanggan']
        );
        User::firstOrCreate(
            ['email' => 'teknisi@gmail.com'],
            ['name' => 'Teknisi NJK', 'password' => bcrypt('password'), 'peran' => 'teknisi']
        );

        // 9 pelanggan tambahan (untuk laporan)
        $pelangganBaru = [
            ['name' => 'Andi Pratama', 'email' => 'andi@mail.com'],
            ['name' => 'Bunga Lestari', 'email' => 'bunga@mail.com'],
            ['name' => 'Citra Dewi', 'email' => 'citra@mail.com'],
            ['name' => 'Dimas Ardian', 'email' => 'dimas@mail.com'],
            ['name' => 'Eka Safitri', 'email' => 'eka@mail.com'],
            ['name' => 'Fahri Rizky', 'email' => 'fahri@mail.com'],
            ['name' => 'Gina Amelia', 'email' => 'gina@mail.com'],
            ['name' => 'Hadi Saputra', 'email' => 'hadi@mail.com'],
            ['name' => 'Intan Permata', 'email' => 'intan@mail.com'],
        ];

        foreach ($pelangganBaru as $p) {
            User::firstOrCreate(
                ['email' => $p['email']],
                ['name' => $p['name'], 'password' => bcrypt('password'), 'peran' => 'pelanggan']
            );
        }

        Ekspedisi::firstOrCreate(['nama_ekspedisi' => 'JNE'], ['ongkir_per_km' => 2000]);
        Ekspedisi::firstOrCreate(['nama_ekspedisi' => 'TIKI'], ['ongkir_per_km' => 2500]);
        Ekspedisi::firstOrCreate(['nama_ekspedisi' => 'SiCepat'], ['ongkir_per_km' => 1500]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Seeder data master & transaksi (15 produk, 10 jasa, 20 transaksi)
        $this->call([
            KategoriSeeder::class,
            ProdukSeeder::class,
            JasaServisSeeder::class,
            TransaksiSeeder::class,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}