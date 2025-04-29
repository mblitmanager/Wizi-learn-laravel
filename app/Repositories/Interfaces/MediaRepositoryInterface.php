<?php

namespace App\Repositories\Interfaces;

use App\Models\Media;

interface MediaRepositoryInterface
{
    public function getTutoriels();
    public function getAstuces();
    public function getTutorielsByFormation($formationId);
    public function getAstucesByFormation($formationId);
    public function getMediaByType($type);
    public function create(array $data): Media;
    public function update($id, array $data): bool;
    public function delete($id): bool;
} 