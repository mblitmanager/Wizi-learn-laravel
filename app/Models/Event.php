<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Event extends Model
{
    protected $fillable = [
        'title',
        'message',
        'topic',
        'data',
        'status'
    ];

    protected $casts = [
        'data' => 'array',
        'processed_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending',
        'topic' => 'general'
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    // Accessors
    protected function isProcessed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'processed'
        );
    }
}
