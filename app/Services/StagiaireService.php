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
        // 1. Créer l'utilisateur associé
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'stagiaire',
        ]);

        // 2. Associer l'utilisateur
        $data['user_id'] = $user->id;

        // 3. Récupérer et retirer les formations du tableau avant création du stagiaire
        $formationIds = $data['formation_id']; // Tableau d'IDs
        $formateurIds = $data['formateur_id'] ?? [];
        $commercialIds = $data['commercial_id'] ?? [];
        unset($data['formation_id']);
        unset($data['formateur_id']);
        unset($data['commercial_id']);

        // 4. Créer le stagiaire
        $stagiaire = $this->stagiaireRepository->create($data);

        // 5. Associer les formations via la table pivot
        $stagiaire->formations()->sync($formationIds);
        // 6. Associer les formateurs via la table pivot
        $stagiaire->formateurs()->sync($formateurIds);
        // 7. Associer les commerciaux via la table pivot
        $stagiaire->commercials()->sync($commercialIds);

        return $stagiaire;
    }
    public function update(int $id, array $data)
    {
        $stagiaire = $this->stagiaireRepository->find($id);

        if (!$stagiaire) {
            throw new \Exception("Stagiaire not found");
        }

        // 1. Mise à jour de l'utilisateur lié
        $stagiaire->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) && $data['password'] !== null
                ? Hash::make($data['password'])
                : $stagiaire->user->password,
        ]);


        $formationIds = $data['formation_id'] ?? [];
        $formateurIds = $data['formateur_id'] ?? [];
        $commercialIds = $data['commercial_id'] ?? [];

        unset($data['name'], $data['email'], $data['password'], $data['formation_id']);

        // 3. Mise à jour des champs du stagiaire
        $this->stagiaireRepository->update($id, $data);

        // 4. Synchronisation des formations
        $stagiaire->formations()->sync($formationIds);

        // 5. Synchronisation des formateurs
        $stagiaire->formateurs()->sync($formateurIds);
        // 6. Synchronisation des commerciaux
        $stagiaire->commercials()->sync($commercialIds);

        return true;
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
