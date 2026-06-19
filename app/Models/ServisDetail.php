<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServisDetail extends Model
{
    protected $table = 'servis_detail';

    protected $fillable = [
        'transaksi_id',
        'jasa_servis_id',
        'teknisi_id',
        'keluhan',
        'status',
        'catatan_teknisi',
        'tanggal_selesai',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function jasaServis(): BelongsTo
    {
        return $this->belongsTo(JasaServis::class, 'jasa_servis_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
