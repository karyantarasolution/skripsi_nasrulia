<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    protected $table = 'chatbot_logs';

    protected $fillable = [
        'user_id',
        'pesan',
        'jawaban',
        'kategori',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
