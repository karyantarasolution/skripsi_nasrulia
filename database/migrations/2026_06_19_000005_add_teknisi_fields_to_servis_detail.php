<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete()->after('jasa_servis_id');
            $table->text('catatan_teknisi')->nullable()->after('status');
            $table->timestamp('tanggal_selesai')->nullable()->after('catatan_teknisi');
        });
    }

    public function down(): void
    {
        Schema::table('servis_detail', function (Blueprint $table) {
            $table->dropColumn(['teknisi_id', 'catatan_teknisi', 'tanggal_selesai']);
        });
    }
};
