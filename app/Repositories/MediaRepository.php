<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\Interfaces\MediaRepositoryInterface;

class MediaRepository implements MediaRepositoryInterface
{
    public function getTutoriels()
    {
        return Media::where('categorie', 'tutoriel')
            ->orderBy('ordre', 'asc')
            ->get();
    }

    public function getAstuces()
    {
        return Media::where('categorie', 'astuce')
            ->orderBy('ordre', 'asc')
            ->get();
    }

    public function getTutorielsByFormation($formationId)
    {
        return Media::where('categorie', 'tutoriel')
            ->where('formation_id', $formationId)
            ->orderBy('ordre', 'asc')
            ->get();
    }

    public function getAstucesByFormation($formationId)
    {
        return Media::where('categorie', 'astuce')
            ->where('formation_id', $formationId)
            ->orderBy('ordre', 'asc')
            ->get();
    }

    public function getMediaByType($type)
    {
        return Media::where('type', $type)
            ->orderBy('ordre', 'asc')
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
