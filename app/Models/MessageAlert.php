<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_id',
        'message_body',
        'message_created_at',
        'detected_rules',
        'status', // PENDING, APPROVED, REJECTED, ARCHIVED
    ];

    protected $casts = [
        'message_created_at' => 'datetime',
        'detected_rules' => 'array',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, ['APPROVED', 'REJECTED','ARCHIVED']);
    }
}
