<?php

namespace App\Services;

use App\Models\PoleRelationClient;
use App\Models\User;
use App\Repositories\Interfaces\PRCInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        DB::beginTransaction();

        try {
            // Gestion de l'image
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $image = $data['image'];

                if (!$image->isValid()) {
                    throw new \Exception("Le fichier image n'est pas valide");
                }

                $imageName = 'prc_'.time().'_'.Str::random(8).'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $imageName);
                $imagePath = 'uploads/users/'.$imageName;
            }

            // Création de l'utilisateur
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'pole relation client',
                'image' => $imagePath,
            ]);

            // Création du PRC
            $prcData = [
                'user_id' => $user->id,
                // Ajoutez ici les autres champs spécifiques au PRC
            ];

            $prc = $this->repository->create($prcData);

            // Gestion des relations
            $stagiaireIds = $data['stagiaire_id'] ?? [];
            $prc->stagiaires()->sync($stagiaireIds);

            DB::commit();
            return $prc;

        } catch (\Exception $e) {
            DB::rollBack();

            // Nettoyage de l'image en cas d'erreur
            if (isset($imagePath) && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            \Log::error('Erreur création PRC: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        DB::beginTransaction();

        try {
            $prc = $this->repository->find($id);
            if (!$prc) {
                throw new \Exception('PoleRelationClient not found');
            }

            // Préparation des données utilisateur
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Gestion du mot de passe
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
                if ($prc->user->image && file_exists(public_path($prc->user->image))) {
                    unlink(public_path($prc->user->image));
                }

                // Upload de la nouvelle image
                $imageName = 'prc_' . $prc->user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $imageName);
                $userData['image'] = 'uploads/users/' . $imageName;
            }

            // Mise à jour de l'utilisateur
            $prc->user->update($userData);

            // Mise à jour du PRC
            $stagiaireIds = $data['stagiaire_id'] ?? [];
            unset($data['name'], $data['email'], $data['password'], $data['image'], $data['stagiaire_id']);

            $this->repository->update($id, $data);

            // Synchronisation des relations
            $prc->stagiaires()->sync($stagiaireIds);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour PRC: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
