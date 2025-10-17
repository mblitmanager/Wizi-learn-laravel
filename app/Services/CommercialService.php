<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\CommercialInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommercialService
{
    protected $commercialInterface;

    public function __construct(CommercialInterface $commercialInterface)
    {
        $this->commercialInterface = $commercialInterface;
    }

    public function list()
    {
        return $this->commercialInterface->all();
    }

    public function show($id)
    {
        return $this->commercialInterface->find($id);
    }
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Gestion de l'image
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];

                if (!$image->isValid()) {
                    throw new \Exception("Le fichier image n'est pas valide");
                }

                $imageName = 'commercial_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $imageName);
                $imagePath = 'uploads/users/' . $imageName;
            }

            // CORRECTION : Déterminer le rôle en fonction de la civilité
            $role = 'commercial'; // Par défaut
            if (isset($data['civilite'])) {
                if ($data['civilite'] == 'Mme.' || $data['civilite'] == 'Mlle.') {
                    $role = 'commerciale';
                } elseif ($data['civilite'] == 'M.') {
                    $role = 'commercial';
                }
            }

            // CORRECTION : Gestion du mot de passe
            $password = $data['password'] ?? 'commercial@123'; // Mot de passe par défaut si null

            // Création de l'utilisateur
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => $role,
                'image' => $imagePath,
            ]);

            // Création du commercial avec prenom, civilite et telephone
            $commercialData = [
                'user_id' => $user->id,
                'prenom' => $data['prenom'] ?? null,
                'civilite' => $data['civilite'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'role' => $role,
            ];

            $commercial = $this->commercialInterface->create($commercialData);

            // Gestion des relations
            $stagiaireIds = $data['stagiaire_id'] ?? [];
            $commercial->stagiaires()->sync($stagiaireIds);

            DB::commit();
            return $commercial;
        } catch (\Exception $e) {
            DB::rollBack();

            // Nettoyage de l'image en cas d'erreur
            if (isset($imagePath) && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            Log::error('Erreur création commercial: ' . $e->getMessage());
            throw $e;
        }
    }
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $commercial = $this->commercialInterface->find($id);
            if (!$commercial) {
                throw new \Exception('Commercial non trouvé');
            }

            // CORRECTION : Déterminer le rôle en fonction de la civilité
            if (isset($data['civilite'])) {
                $role = 'commercial'; // Par défaut
                if ($data['civilite'] == 'Mme.' || $data['civilite'] == 'Mlle.') {
                    $role = 'commerciale';
                } elseif ($data['civilite'] == 'M.') {
                    $role = 'commercial';
                }
                $data['role'] = $role;
            }

            // Préparation des données utilisateur
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Ajouter le rôle aux données utilisateur si défini
            if (isset($data['role'])) {
                $userData['role'] = $data['role'];
            }

            // CORRECTION : Gestion du mot de passe - seulement si fourni
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            // Gestion de l'image
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];

                if (!$image->isValid()) {
                    throw new \Exception("Le fichier image n'est pas valide");
                }

                // Suppression de l'ancienne image
                if ($commercial->user->image && file_exists(public_path($commercial->user->image))) {
                    unlink(public_path($commercial->user->image));
                }

                // Upload de la nouvelle image
                $imageName = 'commercial_' . $commercial->user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $imageName);
                $userData['image'] = 'uploads/users/' . $imageName;
            }

            // Mise à jour de l'utilisateur
            $commercial->user->update($userData);

            // Mise à jour du commercial
            $stagiaireIds = $data['stagiaire_id'] ?? [];
            unset($data['name'], $data['email'], $data['password'], $data['image'], $data['stagiaire_id']);

            $this->commercialInterface->update($id, $data);

            // Synchronisation des relations
            $commercial->stagiaires()->sync($stagiaireIds);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour commercial: ' . $e->getMessage());
            throw $e;
        }
    }
    public function delete($id)
    {
        return $this->commercialInterface->delete($id);
    }
}
