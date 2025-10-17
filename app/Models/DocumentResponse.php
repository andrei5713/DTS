<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentResponse extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'message',
        'type',
        'parent_response_id',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentResponse(): BelongsTo
    {
        return $this->belongsTo(DocumentResponse::class, 'parent_response_id');
    }

    public function replies()
    {
        return $this->hasMany(DocumentResponse::class, 'parent_response_id');
    }
}
