<?php

return [
    // Aktif jika API Key sudah diisi di .env
    'api_key' => env('RAJAONGKIR_API_KEY', ''),

    // Base URL API RajaOngkir (versi Komerce)
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    // ID destinasi asal pengiriman (kecamatan/kota toko).
    // Cari ID-nya lewat endpoint: /api/ongkir/destinasi?search=nama+kecamatan
    'origin_id' => env('RAJAONGKIR_ORIGIN_ID', ''),

    // Label asal pengiriman (untuk ditampilkan ke pelanggan)
    'origin_label' => env('RAJAONGKIR_ORIGIN_LABEL', 'Makassar'),

    // Kode kurir yang ditampilkan, pisahkan dengan titik dua (:)
    'couriers' => env('RAJAONGKIR_COURIERS', 'jne:jnt:sicepat:tiki:pos'),

    // Berat default per unit produk (gram) karena produk belum punya kolom berat
    'default_weight' => env('RAJAONGKIR_DEFAULT_WEIGHT', 1000),
];
