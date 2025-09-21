<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'phone',
        'company',
        'is_read',
        'is_replied',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_replied' => 'boolean',
        'replied_at' => 'datetime',
    ];

    /**
     * Scope para mensagens não lidas
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope para mensagens não respondidas
     */
    public function scopeUnanswered($query)
    {
        return $query->where('is_replied', false);
    }

    /**
     * Marcar mensagem como lida
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Marcar mensagem como respondida
     */
    public function markAsReplied($reply)
    {
        $this->update([
            'is_replied' => true,
            'admin_reply' => $reply,
            'replied_at' => now(),
        ]);
    }
}
