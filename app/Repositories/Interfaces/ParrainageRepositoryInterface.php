<?php

namespace App\Repositories\Interfaces;

interface ParrainageRepositoryInterface
{
    /**
     * Get all filleuls (sponsored users) for a given parrain (sponsor)
     *
     * @param int $parrainId
     * @return array
     */
    public function getFilleuls(int $parrainId): array;

    /**
     * Get parrain (sponsor) information for a given filleul (sponsored user)
     *
     * @param int $filleulId
     * @return array|null
     */
    public function getParrain(int $filleulId): ?array;

    /**
     * Create a new sponsorship relationship
     *
     * @param int $parrainId
     * @param int $filleulId
     * @return bool
     */
    public function createParrainage(int $parrainId, int $filleulId): bool;

    /**
     * Find parrainage by stagiaire ID
     *
     * @param int $stagiaireId
     * @return mixed
     */
    public function findByStagiaireId(int $stagiaireId);

    /**
     * Create a new parrainage
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Trouver un parrainage par son token
     *
     * @param string $token
     * @return mixed
     */
    public function findByToken(string $token);

    /**
     * Mettre à jour un parrainage
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Obtenir les récompenses de parrainage
     *
     * @param int $stagiaireId
     * @return mixed
     */
    public function getRewards(int $stagiaireId);

    /**
     * Obtenir l'historique des parrainages
     *
     * @param int $stagiaireId
     * @return mixed
     */
    public function getHistory(int $stagiaireId);

    /**
     * Ajouter des points à un stagiaire
     *
     * @param int $stagiaireId
     * @param int $points
     * @return mixed
     */
    public function addPoints(int $stagiaireId, int $points);
}
