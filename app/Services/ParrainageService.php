<?php

namespace App\Services;

use App\Repositories\ParrainageRepository;
use Illuminate\Support\Str;

class ParrainageService
{
    protected $parrainageRepository;

    public function __construct(ParrainageRepository $parrainageRepository)
    {
        $this->parrainageRepository = $parrainageRepository;
    }

    public function getParrainageLink($stagiaireId)
    {
        $parrainage = $this->parrainageRepository->findByStagiaireId($stagiaireId);

        if (!$parrainage) {
            $parrainage = $this->parrainageRepository->create([
                'parrain_id' => $stagiaireId,
                'filleul_id' => null,
                'code' => Str::random(8),
                'link' => config('app.url') . '/register?ref=' . Str::random(8)
            ]);
        }

        return $parrainage->link;
    }

    public function getFilleuls($stagiaireId)
    {
        return $this->parrainageRepository->getFilleuls($stagiaireId);
    }

    public function getParrainageStats($stagiaireId)
    {
        $filleuls = $this->getFilleuls($stagiaireId);
        $rewards = collect($this->parrainageRepository->getRewards($stagiaireId));

        return [
            'total_filleuls' => count($filleuls),
            'total_points' => $rewards->sum('points') ?? 0,
            'total_rewards' => $rewards->count() ?? 0,
            'filleuls' => $filleuls
        ];
    }

    /**
     * Génère un lien de parrainage unique pour un stagiaire
     *
     * @param int $stagiaireId
     * @return string
     */
    public function generateParrainageLink($stagiaireId)
    {
        // Générer un token unique
        $token = bin2hex(random_bytes(16));

        // Sauvegarder le token dans la base de données
        $this->parrainageRepository->create([
            'parrain_id' => $stagiaireId,
            'filleul_id' => null,
            'token' => $token,
            'created_at' => now()
        ]);

        // Construire et retourner le lien de parrainage
        return config('app.url') . '/parrainage/' . $token;
    }

    /**
     * Accepter un parrainage
     *
     * @param int $stagiaireId
     * @param string $token
     * @return array
     */
    public function acceptParrainage($stagiaireId, $token)
    {
        // Vérifier si le token existe et n'a pas été utilisé
        $parrainage = $this->parrainageRepository->findByToken($token);

        if (!$parrainage) {
            throw new \Exception('Lien de parrainage invalide');
        }

        if ($parrainage->filleul_id) {
            throw new \Exception('Ce lien de parrainage a déjà été utilisé');
        }

        // Mettre à jour le parrainage avec l'ID du filleul
        $this->parrainageRepository->update($parrainage->id, [
            'filleul_id' => $stagiaireId,
            'accepted_at' => now()
        ]);

        // Attribuer les récompenses
        $this->attribuerRecompenses($parrainage->parrain_id, $stagiaireId);

        return [
            'success' => true,
            'message' => 'Parrainage accepté avec succès'
        ];
    }

    /**
     * Obtenir les récompenses de parrainage
     *
     * @param int $stagiaireId
     * @return array
     */
    public function getParrainageRewards($stagiaireId)
    {
        $rewards = collect($this->parrainageRepository->getRewards($stagiaireId));

        return [
            'total_points' => $rewards->sum('points'),
            'total_filleuls' => $rewards->count(),
            'rewards' => $rewards
        ];
    }

    /**
     * Obtenir l'historique des parrainages
     *
     * @param int $stagiaireId
     * @return array
     */
    public function getParrainageHistory($stagiaireId)
    {
        $history = collect($this->parrainageRepository->getHistory($stagiaireId));

        return [
            'parrainages' => $history->map(function ($item) {
                return [
                    'id' => $item->id,
                    'filleul' => [
                        'id' => $item->filleul->id,
                        'name' => $item->filleul->user->name,
                        'email' => $item->filleul->user->email
                    ],
                    'points' => $item->points,
                    'created_at' => $item->created_at,
                    'accepted_at' => $item->accepted_at
                ];
            })
        ];
    }

    /**
     * Attribuer les récompenses pour un parrainage
     *
     * @param int $parrainId
     * @param int $filleulId
     * @return void
     */
    private function attribuerRecompenses($parrainId, $filleulId)
    {
        // Points pour le parrain
        $this->parrainageRepository->addPoints($parrainId, 100);

        // Points pour le filleul
        $this->parrainageRepository->addPoints($filleulId, 50);
    }
}
