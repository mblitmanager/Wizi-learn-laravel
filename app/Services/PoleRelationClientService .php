<?php 
namespace App\Services;

use App\Repositories\Contracts\PoleRelationClientRepositoryInterface;
use App\Models\PoleRelationClient;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class PoleRelationClientService implements PoleRelationClientRepositoryInterface
{
    public function __construct(protected PoleRelationClientRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function all(): Collection
    {
        return $this->repository->all();
    }
    public function find(int $id): ?PoleRelationClient
    {
        return $this->repository->find($id);
    }
    public function create(array $data): PoleRelationClient
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pole relation client',
        ]);

        // 2. Associer l'utilisateur
        $data['user_id'] = $user->id;

        return $this->repository->create($data);
    }
    public function update(int $id, array $data): bool
    {
        $prc = $this->repository->find($id);
        if (!$prc) {
            throw new \Exception("PoleRelationClient not found");
        }

        $prc->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $prc->user->password,
        ]);

        unset($data['name'], $data['email'], $data['password']);
        return $this->repository->update($id, $data);
    }
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
