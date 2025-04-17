<?php
namespace App\Repositories;

use App\Models\PoleRelationClient;
use Illuminate\Support\Collection;

class PoleRelationClientRepository implements \App\Repositories\Interfaces\PRCInterface
{

    public function all(): Collection
    {
        return PoleRelationClient::with('stagiaires', 'user')->get();
    }

    public function find(int $id): ?PoleRelationClient
    {
        return PoleRelationClient::with('stagiaires', 'user')->where('id', $id)->first();
    }

    public function create(array $data): PoleRelationClient
    {
        return PoleRelationClient::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $media = PoleRelationClient::findOrFail($id);
        return $media->update($data);
    }

    public function delete(int $id): bool
    {
        return PoleRelationClient::destroy($id) > 0;
    }
}
