<?php

namespace App\Repositories\Interfaces;

use App\Models\Media;

interface MediaRepositoryInterface
{
    public function getTutorials();
    public function getLanguageSessions();
    public function getInteractiveContent($formationId);
    public function getMediaByType($type);
    public function create(array $data): Media;
    public function update($id, array $data): bool;
    public function delete($id): bool;
} 