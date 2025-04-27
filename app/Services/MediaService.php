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

    public function getTutoriels()
    {
        return $this->mediaRepository->getTutoriels();
    }

    public function getAstuces()
    {
        return $this->mediaRepository->getAstuces();
    }

    public function getTutorielsByFormation($formationId)
    {
        return $this->mediaRepository->getTutorielsByFormation($formationId);
    }

    public function getAstucesByFormation($formationId)
    {
        return $this->mediaRepository->getAstucesByFormation($formationId);
    }

    public function getInteractiveFormations()
    {
        $formations = $this->formationRepository->all();
        return $formations->map(function($formation) {
            return [
                'id' => $formation->id,
                'title' => $formation->title,
                'description' => $formation->description,
                'duration' => $formation->duration,
                'tutoriels' => $this->mediaRepository->getTutorielsByFormation($formation->id),
                'astuces' => $this->mediaRepository->getAstucesByFormation($formation->id)
            ];
        });
    }
}
