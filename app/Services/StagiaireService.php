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

    public function create(array $data, array $selectedFormations, array $poleRelationClientIds = [], array $formateurIds = [])
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

        // 3. Récupérer et retirer les champs non nécessaires
        $commercialIds = $data['commercial_id'] ?? [];
        unset($data['formateur_id'], $data['commercial_id'], $data['pole_relation_client_id']);

        // 4. Créer le stagiaire
        $stagiaire = $this->stagiaireRepository->create($data);

        // 5. Associer les formations via la table pivot avec date_debut et formateur_id
        $stagiaire->catalogue_formations()->sync($selectedFormations);
        // 6. Associer les formateurs via la table pivot
        $stagiaire->formateurs()->sync($formateurIds);
        // 7. Associer les commerciaux via la table pivot
        $stagiaire->commercials()->sync($commercialIds);
        // 8. Associer les pôles relation client via la table pivot
        $stagiaire->poleRelationClient()->sync($poleRelationClientIds);

        return $stagiaire;
    }

    public function update(int $id, array $data, array $selectedFormations, array $poleRelationClientIds = [], array $formateurIds = [])
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

        $commercialIds = $data['commercial_id'] ?? [];
        $formationIds = $data['catalogue_formation_id'] ?? [];

        unset($data['name'], $data['email'], $data['password'], $data['catalogue_formation_id'], $data['formateur_id'], $data['commercial_id']);

        // 3. Mise à jour des champs du stagiaire
        $this->stagiaireRepository->update($id, $data);

        // 4. Synchronisation des formations avec date_debut et formateur_id
        $stagiaire->catalogue_formations()->sync($selectedFormations);
        // 5. Synchronisation des formateurs
        if (!empty($formateurIds)) {
            $stagiaire->formateurs()->sync($formateurIds);
        }
        // 6. Synchronisation des commerciaux
        if (!empty($commercialIds)) {
            $stagiaire->commercials()->sync($commercialIds);
        }
        // 7. Synchronisation des pôles relation client
        $stagiaire->poleRelationClient()->sync($poleRelationClientIds);

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
