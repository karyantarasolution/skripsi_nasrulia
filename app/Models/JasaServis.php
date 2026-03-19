<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JasaServis extends Model
{
    protected $table = 'jasa_servis';
protected $fillable = ['nama_jasa', 'biaya_jasa'];
}
