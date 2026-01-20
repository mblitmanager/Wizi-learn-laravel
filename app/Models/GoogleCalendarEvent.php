<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarEvent extends Model
{
    protected $fillable = [
        'google_calendar_id',
        'google_id',
        'summary',
        'description',
        'location',
        'start',
        'end',
        'html_link',
        'hangout_link',
        'organizer',
        'attendees',
        'status',
        'recurrence',
        'event_type',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'organizer' => 'array',
        'attendees' => 'array',
        'recurrence' => 'array',
    ];

    public function googleCalendar(): BelongsTo
    {
        return $this->belongsTo(GoogleCalendar::class);
    }
}
