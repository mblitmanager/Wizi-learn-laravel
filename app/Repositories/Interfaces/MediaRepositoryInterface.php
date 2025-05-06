<?php

namespace App\Repositories\Interfaces;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;

interface MediaRepositoryInterface
{
    // Anciennes méthodes (collections)
    public function getTutoriels();
    public function getAstuces();
    public function getTutorielsByFormation($formationId);
    public function getAstucesByFormation($formationId);

    // Nouvelles méthodes (query builders pour pagination performante)
    public function getTutorielsQuery(): Builder;
    public function getAstucesQuery(): Builder;
    public function getTutorielsByFormationQuery($formationId): Builder;
    public function getAstucesByFormationQuery($formationId): Builder;

    // Méthode utilitaire
    public function getMediaByType($type);

    // CRUD
    public function create(array $data): Media;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}
