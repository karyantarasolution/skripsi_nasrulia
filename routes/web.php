<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\JasaServisController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\AturanChatbotController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\TeknisiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 1. AKSES KHUSUS ADMIN (Master Data & Laporan)
    Route::middleware(['peran:admin'])->group(function () {
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('jasa', JasaServisController::class);
        Route::resource('ekspedisi', EkspedisiController::class);
        Route::resource('aturan-chatbot', AturanChatbotController::class);

        // Laporan PDF
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [App\Http\Controllers\LaporanController::class, 'index'])->name('index');
            Route::get('/cetak/{tipe}', [App\Http\Controllers\LaporanController::class, 'cetakPDF'])->name('cetak');
        });
    });

    // 2. AKSES ADMIN, KASIR & TEKNISI (Transaksi & Servis)
    Route::middleware(['peran:admin,kasir'])->group(function () {
        Route::get('/transaksi', [App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi/konfirmasi/{id}', [App\Http\Controllers\TransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
        Route::get('/transaksi/invoice/{id}', [App\Http\Controllers\TransaksiController::class, 'invoice'])->name('transaksi.invoice');
    });

    // 3. TEKNISI: Manajemen Servis
    Route::middleware(['peran:teknisi'])->group(function () {
        Route::get('/teknisi/servis', [TeknisiController::class, 'index'])->name('teknisi.servis');
        Route::get('/teknisi/semua-servis', [TeknisiController::class, 'daftarServis'])->name('teknisi.semua-servis');
        Route::post('/teknisi/ambil/{id}', [TeknisiController::class, 'ambilServis'])->name('teknisi.ambil');
        Route::post('/teknisi/update-status/{id}', [TeknisiController::class, 'updateStatus'])->name('teknisi.update-status');
    });

    // 4. AKSES SEMUA ROLE
    Route::middleware(['peran:admin,kasir,pelanggan,teknisi'])->group(function () {

        // Fitur Chatbot
        Route::get('/konsultasi', function () { return view('chatbot.index'); })->name('konsultasi');
        Route::post('/api/chat', [ChatbotController::class, 'getResponse'])->name('api.chat');

        // Fitur Katalog & Keranjang Checkout (hanya pelanggan)
        Route::get('/katalog', [KatalogController::class, 'index'])->name('pelanggan.katalog');
        Route::post('/keranjang/tambah/{id}', [KatalogController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
        Route::get('/keranjang', [KatalogController::class, 'tampilkanKeranjang'])->name('keranjang.index');
        Route::delete('/keranjang/hapus/{id}', [KatalogController::class, 'hapusItem'])->name('keranjang.hapus');
        Route::post('/checkout', [KatalogController::class, 'checkout'])->name('checkout');
        Route::get('/pesanan-saya', [App\Http\Controllers\TransaksiController::class, 'pesananSaya'])->name('pesanan.saya');

        // Pembayaran
        Route::get('/pembayaran/{id}', [KatalogController::class, 'formPembayaran'])->name('pembayaran.form');
        Route::post('/pembayaran/upload/{id}', [KatalogController::class, 'uploadPembayaran'])->name('pembayaran.upload');

        // Invoice untuk pelanggan
        Route::get('/pesanan-saya/invoice/{id}', [App\Http\Controllers\TransaksiController::class, 'invoice'])->name('pesanan.invoice');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
