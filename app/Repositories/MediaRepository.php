<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\Interfaces\MediaRepositoryInterface;

class MediaRepository implements MediaRepositoryInterface
{
    public function getTutorials()
    {
        return Media::where('type', 'tutorial')
            ->where('duration', '<=', 30) // 30 secondes max
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLanguageSessions()
    {
        return Media::where('type', 'language_session')
            ->where('duration', '<=', 5) // 5 minutes max
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getInteractiveContent($formationId)
    {
        return Media::where('type', 'interactive')
            ->where('formation_id', $formationId)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getMediaByType($type)
    {
        return Media::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update($id, array $data): bool
    {
        $media = Media::find($id);
        return $media ? $media->update($data) : false;
    }

    public function delete($id): bool
    {
        $media = Media::find($id);
        return $media ? $media->delete() : false;
    }
}
