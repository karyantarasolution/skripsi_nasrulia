<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $fillable = ['nama_kategori'];

    // Relasi: 1 Kategori memiliki banyak Produk
    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}