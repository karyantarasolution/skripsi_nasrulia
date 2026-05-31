<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE servis_detail MODIFY COLUMN status ENUM('proses', 'selesai', 'diambil', 'garansi', 'batal') DEFAULT 'proses'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE servis_detail MODIFY COLUMN status ENUM('proses', 'selesai', 'diambil') DEFAULT 'proses'");
    }
};
