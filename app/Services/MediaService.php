<?php

namespace App\Services;

use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\FormationRepositoryInterface;

class MediaService
{
    protected $mediaRepository;
    protected $formationRepository;

    public function __construct(
        MediaRepositoryInterface $mediaRepository,
        FormationRepositoryInterface $formationRepository
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->formationRepository = $formationRepository;
    }

    public function getTutoriels($perPage = 10)
    {
        return $this->mediaRepository->getTutorielsQuery()->paginate($perPage);
    }

    public function getAstuces($perPage = 10)
    {
        return $this->mediaRepository->getAstucesQuery()->paginate($perPage);
    }

    public function getTutorielsByFormation($formationId, $perPage = 10)
    {
        return $this->mediaRepository->getTutorielsByFormationQuery($formationId)->paginate($perPage);
    }

    public function getAstucesByFormation($formationId, $perPage = 10)
    {
        return $this->mediaRepository->getAstucesByFormationQuery($formationId)->paginate($perPage);
    }

    public function getInteractiveFormations()
    {
        $formations = $this->formationRepository->all();

        return $formations->map(function ($formation) {
            return [
                'id' => $formation->id,
                'title' => $formation->title,
                'description' => $formation->description,
                'duration' => $formation->duration,
                // Attention : ici, on retourne toujours tout sans pagination
                'tutoriels' => $this->mediaRepository->getTutorielsByFormationQuery($formation->id)->get(),
                'astuces' => $this->mediaRepository->getAstucesByFormationQuery($formation->id)->get(),
            ];
        });
    }
}
