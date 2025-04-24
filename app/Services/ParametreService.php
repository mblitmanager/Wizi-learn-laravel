<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\ParametreRepositoryInterface;
use Illuminate\Support\Collection;

class ParametreService
{
    protected $parametreInterface;

    public function __construct(ParametreRepositoryInterface $parametreInterface)
    {
        $this->parametreInterface = $parametreInterface;
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function list(): Collection
    {
        return $this->parametreInterface->all();
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function find(int $id): ?User
    {
        return $this->parametreInterface->find($id);
    }

    /**
     * Créer un utilisateur
     */
    public function create(array $data): User
    {
        return $this->parametreInterface->create($data);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(int $id, array $data): User
    {
        return $this->parametreInterface->update($id, $data);
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete(int $id): bool
    {
        return $this->parametreInterface->delete($id);
    }
}
