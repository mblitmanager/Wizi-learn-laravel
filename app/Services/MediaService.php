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

    public function getTutorials()
    {
        return $this->mediaRepository->getTutorials();
    }

    public function getLanguageSessions()
    {
        return $this->mediaRepository->getLanguageSessions();
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
                'interactive_content' => $this->mediaRepository->getInteractiveContent($formation->id)
            ];
        });
    }
}
