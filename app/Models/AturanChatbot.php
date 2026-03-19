<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AturanChatbot extends Model
{
    protected $table = 'aturan_chatbot';
protected $fillable = ['kata_kunci', 'jawaban'];
}
