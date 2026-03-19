<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\JasaServisController;
use App\Http\Controllers\AturanChatbotController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\KatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 1. AKSES KHUSUS ADMIN (Master Data & Aturan AI)
    Route::middleware(['peran:admin'])->group(function () {
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('jasa', JasaServisController::class);
        Route::resource('aturan-chatbot', AturanChatbotController::class);
        Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/cetak/{tipe}', [App\Http\Controllers\LaporanController::class, 'cetakPDF'])->name('laporan.cetak');
        
        Route::get('/pengguna', function () { return view('pengguna.index'); })->name('pengguna.index');
        Route::get('/laporan', function () { return view('laporan.index'); })->name('laporan.index');
    });

    // 2. AKSES ADMIN & KASIR (Kelola Transaksi)
   Route::middleware(['peran:admin,kasir'])->group(function () {
        Route::get('/transaksi', [App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi/konfirmasi/{id}', [App\Http\Controllers\TransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
    });

    // 3. AKSES SEMUA ROLE (Biar Admin bisa ngetes Chatbot & Katalog Pelanggan)
    Route::middleware(['peran:admin,kasir,pelanggan'])->group(function () {
        
        // Fitur Chatbot
        Route::get('/konsultasi', function () { return view('chatbot.index'); })->name('konsultasi');
        Route::post('/api/chat', [ChatbotController::class, 'getResponse'])->name('api.chat');

        // Fitur Katalog & Keranjang Checkout
        Route::get('/katalog', [KatalogController::class, 'index'])->name('pelanggan.katalog');
        Route::post('/keranjang/tambah/{id}', [KatalogController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
        Route::get('/keranjang', [KatalogController::class, 'tampilkanKeranjang'])->name('keranjang.index');
        Route::delete('/keranjang/hapus/{id}', [KatalogController::class, 'hapusItem'])->name('keranjang.hapus');
        Route::post('/checkout', [KatalogController::class, 'checkout'])->name('checkout');
        Route::get('/pesanan-saya', [App\Http\Controllers\TransaksiController::class, 'pesananSaya'])->name('pesanan.saya');
        
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/api/chat', [ChatbotController::class, 'getResponse'])->name('api.chat');
require __DIR__.'/auth.php';