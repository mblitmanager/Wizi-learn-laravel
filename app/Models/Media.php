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
        if ($this->video_platform === 'server' && $this->video_file_path) {
            return asset('storage/videos/' . $this->video_file_path);
        }
        return $this->url;
    }

    /**
     * Get the video thumbnail URL based on platform.
     */
    public function getThumbnailUrlAttribute()
    {
        switch ($this->video_platform) {
            case 'youtube':
                $videoId = $this->extractYoutubeId($this->url);
                return $videoId ? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg" : null;

            case 'dailymotion':
                // Dailymotion thumbnail requires video ID extraction
                $videoId = $this->extractDailymotionId($this->url);
                return $videoId ? "https://www.dailymotion.com/thumbnail/video/{$videoId}" : null;

            case 'server':
                // For server videos, we could generate thumbnails or use a default
                return null; // Or implement video thumbnail generation

            default:
                return null;
        }
    }

    private function extractYoutubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function extractDailymotionId($url)
    {
        preg_match('/(?:dailymotion\.com\/(?:video|hub)\/|dai\.ly\/)([a-zA-Z0-9]+)/', $url, $matches);
        return $matches[1] ?? null;
    }
}
