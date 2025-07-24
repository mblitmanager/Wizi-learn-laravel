<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\FormateurInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class FormateurService
{
    protected $formateurInterface;

    public function __construct(FormateurInterface $formateurInterface)
    {
        $this->formateurInterface = $formateurInterface;
    }

    public function list()
    {
        return $this->formateurInterface->all();
    }

    public function show($id)
    {
        return $this->formateurInterface->find($id);
    }


    public function create(array $data)
    {
        // Créer l'utilisateur associé
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'formateur', // Forcer le rôle à 'formateur'
        ];
        if (isset($data['image'])) {
            $userData['image'] = $data['image'];
        }
        $user = User::create($userData);

        // Associer l'utilisateur au formateur
        $data['user_id'] = $user->id;

        // Vérifier si les stagiaires et formations sont définis
        $stagiaireIds = $data['stagiaire_id'] ?? [];
        $formationIds = $data['catalogue_formation_id'] ?? [];
        unset($data['stagiaire_id'], $data['catalogue_formation_id']);

        // Créer le formateur
        $formateur = $this->formateurInterface->create($data);

        // Synchroniser les relations avec les stagiaires et formations
        $formateur->stagiaires()->sync($stagiaireIds);
        $formateur->catalogue_formations()->sync($formationIds);

        return $formateur;
    }

    public function update(int $id, array $data)
    {
        // Trouver le formateur
        $formateur = $this->formateurInterface->find($id);

        if (!$formateur) {
            throw new \Exception("Formateur not found");
        }

        // Mettre à jour l'utilisateur associé
        $formateur->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) && $data['password'] !== null
                ? Hash::make($data['password'])
                : $formateur->user->password,
        ]);

           // Ajout de la gestion de l'image
        if (isset($data['image'])) {
            $userUpdate['image'] = $data['image'];
        }
        $formateur->user->update($userUpdate);

        // Vérifier si les stagiaires et formations sont définis
        $stagiaireIds = $data['stagiaire_id'] ?? [];
        $formationIds = $data['catalogue_formation_id'] ?? [];
        unset($data['name'], $data['email'], $data['password'], $data['stagiaire_id'], $data['catalogue_formation_id']);

        // Synchroniser les relations avec les stagiaires et formations
        $formateur->stagiaires()->sync($stagiaireIds);
        $formateur->catalogue_formations()->sync($formationIds);

        // Mettre à jour les autres données du formateur
        return $this->formateurInterface->update($id, $data);
    }

    public function delete($id)
    {
        return $this->formateurInterface->delete($id);
    }
}
