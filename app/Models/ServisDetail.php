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
        'keluhan',
        'status',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function jasaServis(): BelongsTo
    {
        return $this->belongsTo(JasaServis::class, 'jasa_servis_id');
    }
}
