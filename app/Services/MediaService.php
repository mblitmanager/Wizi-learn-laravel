<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Media;
use App\Models\Stagiaire;
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

    // App/Services/MediaService.php
    public function getFormationsWithWatchedStatus(int $stagiaireId)
    {
        return Formation::with([
            'medias' => function ($query) use ($stagiaireId) {
                $query->with([
                    'stagiaires' => function ($q) use ($stagiaireId) {
                        $q->where('stagiaire_id', $stagiaireId);
                    }
                ]);
            }
        ])
            ->get()
            ->map(function ($formation) {
                $formation->medias->each(function ($media) {
                    $media->is_watched = $media->stagiaires->isNotEmpty()
                        ? $media->stagiaires->first()->pivot->is_watched
                        : false;
                    unset($media->stagiaires);
                });
                return $formation;
            });
    }


    public function markAsWatched(int $stagiaireId, int $mediaId)
    {
        $stagiaire = Stagiaire::findOrFail($stagiaireId);
        $stagiaire->medias()->syncWithoutDetaching([
            $mediaId => ['is_watched' => true, 'watched_at' => now()]
        ]);
    }

    public function getWatchedStatus(int $stagiaireId, array $mediaIds)
    {
        return Media::whereHas('stagiaires', function ($query) use ($stagiaireId) {
            $query->where('stagiaire_id', $stagiaireId)
                ->where('is_watched', true);
        })
            ->whereIn('id', $mediaIds)
            ->pluck('id')
            ->toArray();
    }
}
