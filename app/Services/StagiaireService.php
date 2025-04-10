<?php
namespace App\Services;

use App\Models\User;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;

class StagiaireService
{
    protected $stagiaireRepository;

    public function __construct(StagiaireRepositoryInterface $stagiaireRepository)
    {
        $this->stagiaireRepository = $stagiaireRepository;
    }

    public function list()
    {
        return $this->stagiaireRepository->all();
    }

    public function show($id)
    {
        return $this->stagiaireRepository->find($id);
    }

    public function create(array $data)
    {
        // Créer l'utilisateur associé
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'stagiaire',
        ]);

        $data['user_id'] = $user->id;

        return $this->stagiaireRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $stagiaire = $this->stagiaireRepository->find($id);

        if (!$stagiaire) {
            throw new \Exception("Stagiaire not found");
        }

        // Mise à jour de l'utilisateur lié
        $stagiaire->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $stagiaire->user->password,
        ]);

        unset($data['name'], $data['email'], $data['password']);

        return $this->stagiaireRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->stagiaireRepository->delete($id);
    }

    public function desactive($id)
    {
        return $this->stagiaireRepository->desactive($id);
    }
    public function active($id)
    {
        return $this->stagiaireRepository->active($id);
    }
}
