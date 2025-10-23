<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\CatalogueFormation;
use App\Services\CatalogueFormationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Stagiaire;
use App\Repositories\Interfaces\CatalogueFormationInterface;


class CatalogueFormationController extends Controller
{
    protected $catalogueFormationService;
    public function __construct(CatalogueFormationService $catalogueFormationService)
    {
        $this->catalogueFormationService = $catalogueFormationService;
    }

    public function getAllCatalogueFormations()
    {
        $catalogueFormations = $this->catalogueFormationService->list();
        return response()->json($catalogueFormations);
    }

    public function getCatalogueFormationById($id)
    {
        $catalogueFormation = $this->catalogueFormationService->show($id);

        if (!$catalogueFormation) {
            return response()->json(['error' => 'Catalogue de formation introuvable'], 404);
        }

        // Charger relations pour s'assurer que front a tout ce dont il a besoin
        $catalogueFormation->loadMissing(['formation', 'formateurs', 'stagiaires']);

        $response = [
            'catalogueFormation' => $catalogueFormation,
            'formationId' => $catalogueFormation->formation_id,
            'cursusPdfUrl' => $catalogueFormation->cursus_pdf ? asset('storage/' . $catalogueFormation->cursus_pdf) : null,
        ];

        return response()->json($response);
    }
    public function getFormationsAndCatalogues()
    {
        $user = JWTAuth::parseToken()->authenticate();
            // Charger la relation stagiaire si elle n'est pas déjà chargée
            if (!isset($user->relations['stagiaire'])) {
                $user->load('stagiaire');
            }

            // Vérifier si l'utilisateur est bien le stagiaire demandé ou a les droits d'accès
            if ($user->role != 'formateur' && $user->role != 'admin') {
                // Vérifier si l'utilisateur est associé à ce stagiaire
                $userStagiaire = $user->stagiaire;
                if (!$userStagiaire) {
                    return response()->json(['error' => 'non autorisé'], 403);
                }
            }

        $stagiaire = $this->catalogueFormationService->getFormationsAndCatalogues($userStagiaire->id);
        return response()->json($stagiaire);
    }

    /**
     * Télécharger le PDF du cursus
     */
    public function getCataloguePdf($id)
    {
        try {
            $catalogueFormation = $this->catalogueFormationService->show($id);

            if (!$catalogueFormation || !$catalogueFormation->cursus_pdf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'existe pas.'
                ], 404);
            }

            // Vérifier si le fichier existe dans le dossier public
            $filePath = base_path('public/' . $catalogueFormation->cursus_pdf);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier PDF n\'a pas été trouvé sur le serveur.'
                ], 404);
            }

            return response()->json([
                asset($catalogueFormation->cursus_pdf)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du téléchargement du PDF.'
            ], 500);
        }
    }

    /**
     * Récupérer tous les catalogues avec leurs formations associées
     */
    public function getCataloguesWithFormations()
    {
        try {
            $catalogues = CatalogueFormation::with(['formation', 'formateurs', 'stagiaires'])->get();

            if ($catalogues->isEmpty()) {
                return response()->json([
                    '@context' => '/api/contexts/CatalogueFormation',
                    '@id' => '/api/catalogue_formations',
                    '@type' => 'Collection',
                    'totalItems' => 0,
                    'member' => []
                ]);
            }

            $formattedCatalogues = $catalogues->map(function ($catalogue) {
                return [
                    '@id' => "/api/catalogue_formations/{$catalogue->id}",
                    '@type' => 'CatalogueFormation',
                    'id' => $catalogue->id,
                    'titre' => $catalogue->titre,
                    'description' => $catalogue->description,
                    'prerequis' => $catalogue->prerequis,
                    'imageUrl' => $catalogue->image_url,
                    'cursusPdf' => $catalogue->cursus_pdf,
                    'tarif' => $catalogue->tarif,
                    'certification' => $catalogue->certification,
                    'statut' => $catalogue->statut,
                    'duree' => $catalogue->duree,
                    'createdAt' => $catalogue->created_at,
                    'updatedAt' => $catalogue->updated_at,
                    'cursusPdfUrl' => $catalogue->cursus_pdf ? asset('storage/' . $catalogue->cursus_pdf) : null,
                    'formation' => $catalogue->formation ? [
                        '@id' => "/api/formations/{$catalogue->formation->id}",
                        '@type' => 'Formation',
                        'id' => $catalogue->formation->id,
                        'titre' => $catalogue->formation->titre,
                        'description' => $catalogue->formation->description,
                        'categorie' => $catalogue->formation->categorie,
                        'duree' => $catalogue->formation->duree,
                        'image_url' => $catalogue->formation->image_url,
                        'video_url' => $catalogue->formation->video_url,
                        'statut' => $catalogue->formation->statut
                    ] : null,
                    'formateurs' => $catalogue->formateurs->map(fn($formateur) => "/api/formateurs/{$formateur->id}")->toArray(),
                    'stagiaires' => $catalogue->stagiaires->map(fn($stagiaire) => "/api/stagiaires/{$stagiaire->id}")->toArray()
                ];
            });

            return response()->json([
                '@context' => '/api/contexts/CatalogueFormation',
                '@id' => '/api/catalogue_formations',
                '@type' => 'Collection',
                'totalItems' => $catalogues->count(),
                'member' => $formattedCatalogues
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des catalogues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des catalogues.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
