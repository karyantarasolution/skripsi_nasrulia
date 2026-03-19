<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    // Beri izin mass-assignment
    protected $fillable = [
        'kode_transaksi', 
        'user_id', 
        'nama_pelanggan', 
        'tipe', 
        'total_bayar', 
        'status'
    ];

    // Relasi ke Detail Transaksi (1 Transaksi punya banyak Detail)
    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }
}