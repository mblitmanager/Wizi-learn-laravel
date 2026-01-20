<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleCalendar extends Model
{
    protected $fillable = [
        'user_id',
        'google_id',
        'summary',
        'description',
        'background_color',
        'foreground_color',
        'access_role',
        'time_zone',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(GoogleCalendarEvent::class);
    }
}
