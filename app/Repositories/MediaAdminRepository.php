<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\Interfaces\MediaInterface;
use Illuminate\Support\Collection;

class MediaAdminRepository implements MediaInterface
{

    public function all(): Collection
    {
        return Media::with('formation')->get();
    }

    public function find(int $id): ?Media
    {
        return Media::with('formation')->where('id', $id)->first();
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $media = Media::findOrFail($id);
        return $media->update($data);
    }

    public function delete(int $id): bool
    {
        return Media::destroy($id) > 0;
    }
}
