<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ekspedisi;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('servis_detail')->truncate();
        DB::table('transaksi_detail')->truncate();
        DB::table('transaksi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil semua pelanggan (1 existing + 9 baru = 10)
        $pelanggan = User::where('peran', 'pelanggan')->get();
        $teknisi   = User::where('email', 'teknisi@gmail.com')->firstOrFail();
        $ekspedisi = Ekspedisi::pluck('id', 'nama_ekspedisi');
        $jasa      = DB::table('jasa_servis')->pluck('id', 'nama_jasa');
        $produk    = DB::table('produk')->pluck('id', 'nama_produk');

        // ---- 10 TRANSAKSI LUNAS/SELESAI (pendapatan) ----
        $lunas = [
            ['pelanggan_idx' => 0, 'kode' => 'TRX-20260701-001', 'tanggal' => '2026-07-01 10:00:00', 'tipe' => 'penjualan', 'total' => 8300000, 'status' => 'lunas', 'metode' => 'diambil'],
            ['pelanggan_idx' => 1, 'kode' => 'TRX-20260701-002', 'tanggal' => '2026-07-01 14:00:00', 'tipe' => 'servis', 'total' => 100000, 'status' => 'selesai', 'metode' => 'diambil'],
            ['pelanggan_idx' => 2, 'kode' => 'TRX-20260702-001', 'tanggal' => '2026-07-02 09:30:00', 'tipe' => 'penjualan', 'total' => 6900000, 'status' => 'lunas', 'metode' => 'diambil'],
            ['pelanggan_idx' => 3, 'kode' => 'TRX-20260702-002', 'tanggal' => '2026-07-02 11:15:00', 'tipe' => 'servis', 'total' => 85000, 'status' => 'selesai', 'metode' => 'diambil'],
            ['pelanggan_idx' => 4, 'kode' => 'TRX-20260702-003', 'tanggal' => '2026-07-02 15:40:00', 'tipe' => 'penjualan', 'total' => 1700000, 'status' => 'lunas', 'metode' => 'diantar', 'ekspedisi' => 'JNE', 'jarak' => 3],
            ['pelanggan_idx' => 0, 'kode' => 'TRX-20260703-001', 'tanggal' => '2026-07-03 08:20:00', 'tipe' => 'penjualan', 'total' => 500000, 'status' => 'lunas', 'metode' => 'diambil'],
            ['pelanggan_idx' => 5, 'kode' => 'TRX-20260703-002', 'tanggal' => '2026-07-03 10:00:00', 'tipe' => 'servis', 'total' => 250000, 'status' => 'selesai', 'metode' => 'diambil'],
            ['pelanggan_idx' => 6, 'kode' => 'TRX-20260703-003', 'tanggal' => '2026-07-03 13:30:00', 'tipe' => 'penjualan', 'total' => 2100000, 'status' => 'lunas', 'metode' => 'diambil'],
            ['pelanggan_idx' => 7, 'kode' => 'TRX-20260704-001', 'tanggal' => '2026-07-04 09:10:00', 'tipe' => 'servis', 'total' => 200000, 'status' => 'selesai', 'metode' => 'diambil'],
            ['pelanggan_idx' => 8, 'kode' => 'TRX-20260704-002', 'tanggal' => '2026-07-04 14:00:00', 'tipe' => 'penjualan', 'total' => 340000, 'status' => 'lunas', 'metode' => 'diantar', 'ekspedisi' => 'SiCepat', 'jarak' => 7],
        ];

        foreach ($lunas as $trx) {
            $p = $pelanggan[$trx['pelanggan_idx']];
            $ongkir = 0;
            if ($trx['metode'] == 'diantar') {
                $ongkir = $trx['jarak'] * Ekspedisi::where('nama_ekspedisi', $trx['ekspedisi'])->value('ongkir_per_km');
            }

            $trxId = DB::table('transaksi')->insertGetId([
                'kode_transaksi'    => $trx['kode'],
                'user_id'           => $p->id,
                'nama_pelanggan'    => $p->name,
                'tipe'              => $trx['tipe'],
                'total_bayar'       => $trx['total'] + $ongkir,
                'status'            => $trx['status'],
                'metode_pengambilan'=> $trx['metode'],
                'ekspedisi_id'      => $trx['metode'] == 'diantar' ? $ekspedisi[$trx['ekspedisi']] : null,
                'jarak_km'          => $trx['metode'] == 'diantar' ? $trx['jarak'] : null,
                'ongkir'            => $ongkir,
                'alamat_pengiriman' => $trx['metode'] == 'diantar' ? 'Alamat ' . $p->name : null,
                'created_at'        => Carbon::parse($trx['tanggal']),
                'updated_at'        => Carbon::parse($trx['tanggal']),
            ]);

            // Detail transaksi (dummy sederhana)
            if ($trx['tipe'] == 'penjualan') {
                DB::table('transaksi_detail')->insert([
                    'transaksi_id' => $trxId,
                    'produk_id'    => $produk['ASUS Vivobook 15 A516'] ?? 1,
                    'jumlah'       => 1,
                    'harga_satuan' => $trx['total'] - $ongkir,
                    'subtotal'     => $trx['total'] - $ongkir,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                DB::table('servis_detail')->insert([
                    'transaksi_id'  => $trxId,
                    'jasa_servis_id'=> $jasa['Instal Ulang Windows + Driver'] ?? 1,
                    'keluhan'       => 'Servis ' . $trx['kode'],
                    'status'        => 'selesai',
                    'catatan_teknisi'=> 'OK',
                    'tanggal_selesai'=> Carbon::parse($trx['tanggal'])->addHours(3),
                    'teknisi_id'    => $teknisi->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // ---- 10 TRANSAKSI PENDING ----
        $pending = [
            ['pelanggan_idx' => 9, 'kode' => 'TRX-20260704-003', 'tanggal' => '2026-07-04 16:00:00', 'tipe' => 'penjualan', 'total' => 550000, 'status' => 'pending', 'metode' => 'diambil'],
            ['pelanggan_idx' => 1, 'kode' => 'TRX-20260704-004', 'tanggal' => '2026-07-04 16:30:00', 'tipe' => 'servis', 'total' => 120000, 'status' => 'proses', 'metode' => 'diambil'],
            ['pelanggan_idx' => 2, 'kode' => 'TRX-20260704-005', 'tanggal' => '2026-07-04 17:00:00', 'tipe' => 'penjualan', 'total' => 1750000, 'status' => 'dikirim', 'metode' => 'diantar', 'ekspedisi' => 'TIKI', 'jarak' => 5],
            ['pelanggan_idx' => 3, 'kode' => 'TRX-20260704-006', 'tanggal' => '2026-07-04 17:30:00', 'tipe' => 'servis', 'total' => 75000, 'status' => 'proses', 'metode' => 'diambil'],
            ['pelanggan_idx' => 4, 'kode' => 'TRX-20260704-007', 'tanggal' => '2026-07-04 18:00:00', 'tipe' => 'penjualan', 'total' => 230000, 'status' => 'pending', 'metode' => 'diambil'],
            ['pelanggan_idx' => 5, 'kode' => 'TRX-20260704-008', 'tanggal' => '2026-07-04 18:30:00', 'tipe' => 'servis', 'total' => 300000, 'status' => 'proses', 'metode' => 'diambil'],
            ['pelanggan_idx' => 6, 'kode' => 'TRX-20260704-009', 'tanggal' => '2026-07-04 19:00:00', 'tipe' => 'penjualan', 'total' => 650000, 'status' => 'dikirim', 'metode' => 'diantar', 'ekspedisi' => 'JNE', 'jarak' => 4],
            ['pelanggan_idx' => 7, 'kode' => 'TRX-20260704-010', 'tanggal' => '2026-07-04 19:30:00', 'tipe' => 'servis', 'total' => 200000, 'status' => 'proses', 'metode' => 'diambil'],
            ['pelanggan_idx' => 8, 'kode' => 'TRX-20260704-011', 'tanggal' => '2026-07-04 20:00:00', 'tipe' => 'penjualan', 'total' => 350000, 'status' => 'pending', 'metode' => 'diambil'],
            ['pelanggan_idx' => 9, 'kode' => 'TRX-20260704-012', 'tanggal' => '2026-07-04 20:30:00', 'tipe' => 'servis', 'total' => 150000, 'status' => 'proses', 'metode' => 'diambil'],
        ];

        foreach ($pending as $trx) {
            $p = $pelanggan[$trx['pelanggan_idx']];
            $ongkir = 0;
            if ($trx['metode'] == 'diantar') {
                $ongkir = $trx['jarak'] * Ekspedisi::where('nama_ekspedisi', $trx['ekspedisi'])->value('ongkir_per_km');
            }

            $trxId = DB::table('transaksi')->insertGetId([
                'kode_transaksi'    => $trx['kode'],
                'user_id'           => $p->id,
                'nama_pelanggan'    => $p->name,
                'tipe'              => $trx['tipe'],
                'total_bayar'       => $trx['total'] + $ongkir,
                'status'            => $trx['status'],
                'metode_pengambilan'=> $trx['metode'],
                'ekspedisi_id'      => $trx['metode'] == 'diantar' ? $ekspedisi[$trx['ekspedisi']] : null,
                'jarak_km'          => $trx['metode'] == 'diantar' ? $trx['jarak'] : null,
                'ongkir'            => $ongkir,
                'alamat_pengiriman' => $trx['metode'] == 'diantar' ? 'Alamat ' . $p->name : null,
                'created_at'        => Carbon::parse($trx['tanggal']),
                'updated_at'        => Carbon::parse($trx['tanggal']),
            ]);

            if ($trx['tipe'] == 'penjualan') {
                DB::table('transaksi_detail')->insert([
                    'transaksi_id' => $trxId,
                    'produk_id'    => $produk['Keyboard Mechanical K845'] ?? 6,
                    'jumlah'       => 1,
                    'harga_satuan' => $trx['total'] - $ongkir,
                    'subtotal'     => $trx['total'] - $ongkir,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                DB::table('servis_detail')->insert([
                    'transaksi_id'  => $trxId,
                    'jasa_servis_id'=> $jasa['Servis Motherboard Laptop'] ?? 8,
                    'keluhan'       => 'Pending ' . $trx['kode'],
                    'status'        => 'proses',
                    'catatan_teknisi'=> null,
                    'tanggal_selesai'=> null,
                    'teknisi_id'    => $teknisi->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}