<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JasaServisSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jasa_servis')->truncate();

        $jasa = [
            ['nama_jasa' => 'Instal Ulang Windows + Driver', 'biaya_jasa' => 100000],
            ['nama_jasa' => 'Ganti LCD Laptop (jasa saja)', 'biaya_jasa' => 250000],
            ['nama_jasa' => 'Cleaning Overheat & Ganti Thermal Paste', 'biaya_jasa' => 85000],
            ['nama_jasa' => 'Upgrade SSD / RAM (jasa pasang)', 'biaya_jasa' => 75000],
            ['nama_jasa' => 'Recovery Data', 'biaya_jasa' => 150000],
            ['nama_jasa' => 'Perbaikan Engsel Laptop', 'biaya_jasa' => 200000],
            ['nama_jasa' => 'Instal Software / Aplikasi', 'biaya_jasa' => 50000],
            // 3 jasa tambahan
            ['nama_jasa' => 'Servis Motherboard Laptop', 'biaya_jasa' => 300000],
            ['nama_jasa' => 'Bongkar Total & Cleaning', 'biaya_jasa' => 120000],
            ['nama_jasa' => 'Reset BIOS / Firmware', 'biaya_jasa' => 95000],
        ];

        foreach ($jasa as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('jasa_servis')->insert($item);
        }
    }
}