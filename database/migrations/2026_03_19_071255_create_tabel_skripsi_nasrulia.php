<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->string('nama_produk');
            $table->integer('stok');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('jasa_servis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jasa');
            $table->decimal('biaya_jasa', 15, 2);
            $table->timestamps();
        });

        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->string('nama_pelanggan')->nullable();
            $table->enum('tipe', ['penjualan', 'servis']);
            $table->decimal('total_bayar', 15, 2);
            $table->timestamps();
        });

        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('produk_id')->nullable()->constrained('produk');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::create('servis_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('jasa_servis_id')->constrained('jasa_servis');
            $table->text('keluhan');
            $table->enum('status', ['proses', 'selesai', 'diambil'])->default('proses');
            $table->timestamps();
        });

        Schema::create('aturan_chatbot', function (Blueprint $table) {
            $table->id();
            $table->string('kata_kunci');
            $table->text('jawaban');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_chatbot');
        Schema::dropIfExists('servis_detail');
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('jasa_servis');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori');
    }
};