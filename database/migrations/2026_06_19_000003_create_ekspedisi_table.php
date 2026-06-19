<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekspedisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ekspedisi');
            $table->decimal('ongkir_per_km', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekspedisi');
    }
};
