<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaStagiaire extends Model
{
    use HasFactory;

    protected $table = 'media_stagiaire';

    protected $fillable = [
        'media_id',
        'stagiaire_id',
        'is_watched',
        'watched_at'
    ];

    protected $casts = [
        'is_watched' => 'boolean',
        'watched_at' => 'datetime'
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }
}
