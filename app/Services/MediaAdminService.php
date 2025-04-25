<?php

namespace App\Services;

use App\Repositories\Interfaces\MediaInterface;

class MediaAdminService
{
    protected $mediaRepositoryInterface;

    public function __construct(MediaInterface $mediaRepositoryInterface)
    {
        $this->mediaRepositoryInterface = $mediaRepositoryInterface;
    }

    public function list()
    {
        return $this->mediaRepositoryInterface->all();
    }

    public function show($id)
    {
        return $this->mediaRepositoryInterface->find($id);
    }
    public function create(array $data)
    {
        // Créer le quiz
        return $this->mediaRepositoryInterface->create($data);
    }

    public function update(int $id, array $data)
    {
        $media = $this->mediaRepositoryInterface->find($id);

        if (!$media) {
            throw new \Exception("Media not found");
        }

        // Mettre à jour le media
        return $this->mediaRepositoryInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->mediaRepositoryInterface->delete($id);
    }
}
