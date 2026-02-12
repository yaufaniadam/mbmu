<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'wablas_message_id',
        'phone',
        'message',
        'status',
        'attachment_url',
        'related_type',
        'related_id',
    ];

    /**
     * Get the parent related model (user, instruction, etc.).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
