<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produk')->truncate();
        $kategori = DB::table('kategori')->pluck('id', 'nama_kategori');

        $produk = [
            // 9 produk awal (stok disesuaikan agar ada yang di bawah 5)
            ['kategori_id' => $kategori['Laptop'], 'merk' => 'ASUS', 'nama_produk' => 'ASUS Vivobook 15 A516', 'stok' => 3, 'harga_beli' => 6500000, 'harga_jual' => 7200000],
            ['kategori_id' => $kategori['Laptop'], 'merk' => 'Lenovo', 'nama_produk' => 'Lenovo IdeaPad Slim 3', 'stok' => 2, 'harga_beli' => 5800000, 'harga_jual' => 6500000],
            ['kategori_id' => $kategori['Komponen'], 'merk' => 'Kingston', 'nama_produk' => 'SSD Kingston A400 480GB', 'stok' => 10, 'harga_beli' => 420000, 'harga_jual' => 500000],
            ['kategori_id' => $kategori['Komponen'], 'merk' => 'Team Elite', 'nama_produk' => 'RAM DDR4 8GB 3200MHz', 'stok' => 4, 'harga_beli' => 280000, 'harga_jual' => 350000],
            ['kategori_id' => $kategori['Printer'], 'merk' => 'Canon', 'nama_produk' => 'Canon Pixma G1010', 'stok' => 1, 'harga_beli' => 1100000, 'harga_jual' => 1350000],
            ['kategori_id' => $kategori['Aksesoris'], 'merk' => 'Logitech', 'nama_produk' => 'Keyboard Mechanical K845', 'stok' => 3, 'harga_beli' => 450000, 'harga_jual' => 550000],
            ['kategori_id' => $kategori['Aksesoris'], 'merk' => 'Votre', 'nama_produk' => 'Mouse Wireless Silent', 'stok' => 2, 'harga_beli' => 75000, 'harga_jual' => 95000],
            ['kategori_id' => $kategori['Monitor'], 'merk' => 'LG', 'nama_produk' => 'LG 22MN430H-B', 'stok' => 4, 'harga_beli' => 1500000, 'harga_jual' => 1750000],
            ['kategori_id' => $kategori['Networking'], 'merk' => 'TP-Link', 'nama_produk' => 'TP-Link Archer C54', 'stok' => 1, 'harga_beli' => 230000, 'harga_jual' => 290000],

            // 6 produk tambahan (stok sengaja dibuat kecil untuk laporan stok menipis)
            ['kategori_id' => $kategori['Laptop'], 'merk' => 'HP', 'nama_produk' => 'HP 14s-dq2518TU', 'stok' => 2, 'harga_beli' => 6200000, 'harga_jual' => 6900000],
            ['kategori_id' => $kategori['Komponen'], 'merk' => 'Seagate', 'nama_produk' => 'HDD 1TB Barracuda', 'stok' => 3, 'harga_beli' => 550000, 'harga_jual' => 650000],
            ['kategori_id' => $kategori['Printer'], 'merk' => 'Epson', 'nama_produk' => 'Epson L3210', 'stok' => 1, 'harga_beli' => 1450000, 'harga_jual' => 1700000],
            ['kategori_id' => $kategori['Aksesoris'], 'merk' => 'Sades', 'nama_produk' => 'Headset Gaming Sades SA-903', 'stok' => 4, 'harga_beli' => 185000, 'harga_jual' => 230000],
            ['kategori_id' => $kategori['Monitor'], 'merk' => 'Samsung', 'nama_produk' => 'Samsung 24" FHD IPS', 'stok' => 2, 'harga_beli' => 1800000, 'harga_jual' => 2100000],
            ['kategori_id' => $kategori['Networking'], 'merk' => 'D-Link', 'nama_produk' => 'D-Link DIR-842', 'stok' => 3, 'harga_beli' => 275000, 'harga_jual' => 340000],
        ];

        foreach ($produk as $p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
            DB::table('produk')->insert($p);
        }
    }
}