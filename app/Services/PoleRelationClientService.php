<?php
namespace App\Services;

use App\Models\PoleRelationClient;
use App\Models\User;
use App\Repositories\Interfaces\PRCInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class PoleRelationClientService implements PRCInterface
{
    protected $repository;
    public function __construct(PRCInterface $repository)
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

        $stagiaireId = $data['stagiaire_id'];
        unset($data['stagiaire_id']);


        // 4. Créer le stagiaire
        $prc = $this->repository->create($data);

        // 5. Associer les formations via la table pivot
        $prc->stagiaires()->sync($stagiaireId);

        return $prc;

    }
    public function update(int $id, array $data): bool
    {
        $prc = $this->repository->find($id);
        if (!$prc) {
            throw new \Exception("PoleRelationClient not found");
        }

        // Mise à jour de l'utilisateur lié
        $prc->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) && $data['password'] !== null
                ? Hash::make($data['password'])
                : $prc->user->password,
        ]);

        // Récupération et suppression du tableau de stagiaires pour éviter l'erreur SQL
        $stagiaireIds = $data['stagiaire_id'] ?? [];
        unset($data['name'], $data['email'], $data['password'], $data['stagiaire_id']);

        // Mise à jour des autres champs de PRC
        $this->repository->update($id, $data);

        // Synchronisation des stagiaires
        $prc->stagiaires()->sync($stagiaireIds);

        return true;
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
