<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Media;
use App\Models\Stagiaire;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\FormationRepositoryInterface;
use Illuminate\Support\Facades\DB;

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






    public function markAsWatched($stagiaireId, $mediaId)
    {
        DB::table('media_stagiaire')->updateOrInsert(
            [
                'media_id' => $mediaId,
                'stagiaire_id' => $stagiaireId,
            ],
            [
                'is_watched' => true,
                'watched_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function getFormationsWithWatchedStatus($stagiaireId)
    {
        return Formation::with(['medias' => function ($query) use ($stagiaireId) {
            $query->with(['stagiaires' => function ($q) use ($stagiaireId) {
                $q->where('stagiaire_id', $stagiaireId);
            }]);
        }])->get();
    }

    public function getMediaWatchedStatus($stagiaireId, $mediaId)
    {
        return DB::table('media_stagiaire')
            ->where('media_id', $mediaId)
            ->where('stagiaire_id', $stagiaireId)
            ->first(['is_watched', 'watched_at']);
    }
}
