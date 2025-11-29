<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Media extends Model
{
    use HasFactory;
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'url',
        'type',
        'categorie',
        'titre',
        'description',
        'formation_id',
        'duree',
        'ordre',
        'video_platform',
        'video_file_path',
        'subtitle_file_path',
        'subtitle_language',
        'size',
        'mime',
        'uploaded_by',

    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class)
            ->withPivot('is_watched', 'watched_at')
            ->withTimestamps();
    }

    public function getIsUrlAttribute()
    {
        return filter_var($this->url, FILTER_VALIDATE_URL);
    }

    /**
     * Get the full URL for server-hosted videos.
     */
    public function getVideoUrlAttribute()
    {
        // For server-hosted videos, return streaming URL
        if ($this->type === 'video' && $this->video_file_path) {
            // Return relative API URL for streaming
            return '/api/media/stream/' . $this->video_file_path;
        }
        return $this->url;
    }

    /**
     * Get the subtitle URL for server-hosted subtitle files.
     */
    public function getSubtitleUrlAttribute()
    {
        if ($this->subtitle_file_path) {
            return '/api/media/subtitle/' . $this->subtitle_file_path;
        }
        return null;
    }

    /**
     * Get the video thumbnail URL.
     * For server videos, this would require thumbnail generation.
     */
    public function getThumbnailUrlAttribute()
    {
        // For server videos, you could implement thumbnail generation
        // or return a default placeholder
        return null;
    }

    protected static function booted()
    {
        static::creating(function ($media) {
            // Always set video platform to 'server' for video type
            if ($media->type === 'video') {
                $media->video_platform = 'server';
            }
        });

        static::updating(function ($media) {
            // Ensure video platform stays 'server' for video type
            if ($media->type === 'video') {
                $media->video_platform = 'server';
            }
        });
    }
}
