<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\FormateurInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Reverb\Loggers\Log;
use Symfony\Component\HttpFoundation\Request;

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
        DB::beginTransaction();

        try {
            // Initialisation du chemin de l'image
            $imagePath = null;

            // Gestion de l'image
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];

                if (!$image->isValid()) {
                    throw new \Exception("Le fichier image n'est pas valide");
                }

                $imageName = 'user_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $imageName);
                $imagePath = 'uploads/users/' . $imageName;
            }

            // Déterminer le rôle en fonction de la civilité
            $role = 'formateur'; // Par défaut
            if (isset($data['civilite'])) {
                if ($data['civilite'] == 'Mme' || $data['civilite'] == 'Mlle') {
                    $role = 'formatrice';
                } elseif ($data['civilite'] == 'M') {
                    $role = 'formateur';
                }
            }

            // Créer l'utilisateur
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $role, // Utiliser le rôle déterminé
                'image' => $imagePath,
            ]);

            // Créer le formateur/formatrice
            $formateurData = [
                'user_id' => $user->id,
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'] ?? null,
                'role' => $role,
                'civilite' => $data['civilite'] ?? null, // Ajouter la civilité
            ];

            $formateur = $this->formateurInterface->create($formateurData);

            // Gérer les relations
            $this->syncRelations($formateur, $data);

            DB::commit();
            return $formateur;
        } catch (\Exception $e) {
            DB::rollBack();

            // Supprimer l'image si elle a été uploadée mais qu'une erreur est survenue
            if (isset($imagePath) && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            \Log::error('Erreur création formateur: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Trouver le formateur
            $formateur = $this->formateurInterface->find($id);
            if (!$formateur) {
                throw new \Exception('Formateur non trouvé');
            }

            // Préparer les données utilisateur
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Déterminer le rôle en fonction de la civilité (pour la mise à jour aussi)
            if (isset($data['civilite'])) {
                if ($data['civilite'] == 'Mme' || $data['civilite'] == 'Mlle') {
                    $userData['role'] = 'formatrice';
                } elseif ($data['civilite'] == 'M') {
                    $userData['role'] = 'formateur';
                }
            }

            // Gérer le mot de passe
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            // Gestion de l'image
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];

                // Valider que c'est bien une image
                if (!$image->isValid()) {
                    throw new \Exception("Le fichier image n'est pas valide");
                }

                // Supprimer l'ancienne image
                if ($formateur->user->image) {
                    $oldImagePath = public_path($formateur->user->image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                // Générer un nom unique
                $imageName = 'user_' . $formateur->user->id . '_' . time() . '.' . $image->getClientOriginalExtension();

                // Déplacer le fichier
                $image->move(public_path('uploads/users'), $imageName);

                // Enregistrer le chemin
                $userData['image'] = 'uploads/users/' . $imageName;
            }

            // Mettre à jour l'utilisateur
            $formateur->user->update($userData);

            // Synchroniser les relations
            $this->syncRelations($formateur, $data);

            // Nettoyer les données avant mise à jour
            unset(
                $data['name'],
                $data['email'],
                $data['password'],
                $data['image'],
                $data['stagiaire_id'],
                $data['catalogue_formation_id']
            );

            // Mettre à jour le formateur
            $this->formateurInterface->update($id, $data);

            DB::commit();

            return $formateur;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour formateur: ' . $e->getMessage());
            throw $e;
        }
    }
    // Méthodes helper
    protected function shouldHandleImageUpdate(array $data, $formateur): bool
    {
        return isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile;
    }

    protected function deleteOldImage(?string $imagePath): void
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
    }

    protected function syncRelations($formateur, $data): void
    {
        $formateur->stagiaires()->sync($data['stagiaire_id'] ?? []);
        $formateur->catalogue_formations()->sync($data['catalogue_formation_id'] ?? []);
    }

    public function delete($id)
    {
        return $this->formateurInterface->delete($id);
    }
}
