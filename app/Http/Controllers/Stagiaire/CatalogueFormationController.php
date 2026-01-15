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


use Illuminate\Support\Str;

class CatalogueFormationController extends Controller
{
    protected $catalogueFormationService;
    public function __construct(CatalogueFormationService $catalogueFormationService)
    {
        $this->catalogueFormationService = $catalogueFormationService;
    }

    public function getAllCatalogueFormations(Request $request)
    {
        // No caching for filtered/paginated results to ensure freshness and correct filtering
        // $cacheKey = 'catalogue_formations_list_' . md5(json_encode($request->all()));

        try {
            $perPage = $request->input('per_page', 10);
            $category = $request->input('category');
            $search = $request->input('search');
            $sort = $request->input('sort'); // nameAsc, priceDesc, etc.

            $query = CatalogueFormation::where('statut', 1)
                ->select(['id', 'formation_id', 'titre', 'description', 'image_url', 'duree', 'tarif', 'cursus_pdf', 'statut', 'updated_at'])
                ->with([
                    'formation' => function ($q) {
                        $q->where('statut', 1)
                            ->select(['id', 'titre', 'categorie', 'image', 'duree']);
                    }
                ]);

             // Filter by category
            if ($category && $category !== 'Tous') {
                $query->whereHas('formation', function ($q) use ($category) {
                    $q->where('categorie', $category);
                });
            }

            // Filter by search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sorting
            if ($sort) {
                switch ($sort) {
                    case 'nameAsc':
                        $query->orderBy('titre', 'asc');
                        break;
                    case 'nameDesc':
                        $query->orderBy('titre', 'desc');
                        break;
                    case 'priceAsc':
                        $query->orderBy('tarif', 'asc');
                        break;
                    case 'priceDesc':
                        $query->orderBy('tarif', 'desc');
                        break;
                    case 'durationAsc':
                        $query->orderBy('duree', 'asc');
                        break;
                    case 'durationDesc':
                        $query->orderBy('duree', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                 $query->orderBy('created_at', 'desc');
            }

            if ($request->has('per_page')) {
                $catalogueFormations = $query->paginate($perPage);

                $data = $catalogueFormations->getCollection()->map(function ($item) {
                    $item->description = Str::limit((string)$item->description, 250);
                    return $item;
                });

                return response()->json([
                    'data' => $data,
                    'current_page' => $catalogueFormations->currentPage(),
                    'last_page' => $catalogueFormations->lastPage(),
                    'total' => $catalogueFormations->total(),
                    'per_page' => $catalogueFormations->perPage(),
                    'next_page_url' => $catalogueFormations->nextPageUrl(),
                    'prev_page_url' => $catalogueFormations->previousPageUrl(),
                ]);
            } else {
                // Return all items if no pagination requested
                $catalogueFormations = $query->get();

                $data = $catalogueFormations->map(function ($item) {
                    $item->description = Str::limit((string)$item->description, 250);
                    return $item;
                });

                return response()->json($data);
            }


        } catch (\Exception $e) {
            Log::error('Erreur getAllCatalogueFormations: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
    
    // private function fetchCatalogueFormations() ... (removed as it's no longer used or I can keep it/ignore it)


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
    /**
     * Récupérer tous les catalogues avec leurs formations associées (Optimisé & Paginé)
     */
    public function getCataloguesWithFormations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 9);
            $category = $request->input('category');
            $search = $request->input('search');

            $query = CatalogueFormation::where('statut', 1)
                ->select(['id', 'formation_id', 'titre', 'description', 'image_url', 'duree', 'tarif', 'cursus_pdf', 'statut', 'created_at', 'updated_at'])
                ->with([
                    'formation' => function ($q) {
                        $q->select(['id', 'titre', 'categorie', 'image', 'duree', 'statut']);
                    },
                    'formateurs'
                ])
                ->withCount('stagiaires'); // Utiliser withCount au lieu de with pour éviter l'ambiguïté SQL

            // Filtrer par catégorie
            if ($category && $category !== 'Tous') {
                $query->whereHas('formation', function ($q) use ($category) {
                    $q->where('categorie', $category);
                });
            }

            // Filtrer par recherche (optionnel)
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Pagination
            $catalogues = $query->paginate($perPage);

            // Transformation des données
            $formattedData = $catalogues->getCollection()->map(function ($catalogue) {
                 $description = \Illuminate\Support\Str::limit((string)$catalogue->description, 150);
                
                return [
                    'id' => $catalogue->id,
                    'titre' => $catalogue->titre,
                    'description' => $description,
                    'prerequis' => $catalogue->prerequis,
                    'image_url' => $catalogue->image_url,
                    'cursus_pdf' => $catalogue->cursus_pdf,
                    'tarif' => $catalogue->tarif,
                    'certification' => $catalogue->certification,
                    'statut' => $catalogue->statut,
                    'duree' => $catalogue->duree,
                    'created_at' => $catalogue->created_at,
                    'updated_at' => $catalogue->updated_at,
                    'cursusPdfUrl' => $catalogue->cursus_pdf ? asset('storage/' . $catalogue->cursus_pdf) : null,
                    'formation' => $catalogue->formation ? [
                        'id' => $catalogue->formation->id,
                        'titre' => $catalogue->formation->titre,
                        'description' => \Illuminate\Support\Str::limit((string)$catalogue->formation->description, 100),
                        'categorie' => $catalogue->formation->categorie,
                        'duree' => $catalogue->formation->duree,
                        'image_url' => $catalogue->formation->image_url,
                        'video_url' => $catalogue->formation->video_url,
                        'statut' => $catalogue->formation->statut
                    ] : null,
                    'formateurs' => $catalogue->formateurs->map(fn($f) => $f->id)->toArray(),
                    'stagiaires_count' => $catalogue->stagiaires_count // Utiliser la valeur calculée par withCount 
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'current_page' => $catalogues->currentPage(),
                'last_page' => $catalogues->lastPage(),
                'total' => $catalogues->total(),
                'per_page' => $catalogues->perPage(),
                'next_page_url' => $catalogues->nextPageUrl(),
                'prev_page_url' => $catalogues->previousPageUrl(),
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
