<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->enum('metode_pengambilan', ['diantar', 'diambil'])->default('diambil')->after('status');
            $table->foreignId('ekspedisi_id')->nullable()->constrained('ekspedisi')->nullOnDelete()->after('metode_pengambilan');
            $table->decimal('jarak_km', 10, 2)->nullable()->after('ekspedisi_id');
            $table->decimal('ongkir', 15, 2)->default(0)->after('jarak_km');
            $table->text('alamat_pengiriman')->nullable()->after('ongkir');
            $table->string('bukti_bayar')->nullable()->after('alamat_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['metode_pengambilan', 'ekspedisi_id', 'jarak_km', 'ongkir', 'alamat_pengiriman', 'bukti_bayar']);
        });
    }
};
