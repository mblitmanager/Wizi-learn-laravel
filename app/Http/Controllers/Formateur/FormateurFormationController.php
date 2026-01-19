<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use App\Models\CatalogueFormation;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormateurFormationController extends Controller
{
    /**
     * Vérifier que l'utilisateur est un formateur
     */
    private function checkFormateur()
    {
        $user = Auth::user();
        if ($user->role !== 'formateur' && $user->role !== 'formatrice') {
            abort(403, 'Accès réservé aux formateurs.');
        }

        if (!$user->formateur) {
            abort(404, 'Profil formateur non trouvé.');
        }
    }

    /**
     * API: Get available formations for assignment
     * GET /formateur/formations/available
     */
    public function getAvailable()
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Get all formations with stagiaire count
        $formations = CatalogueFormation::withCount(['stagiaires' => function ($query) use ($formateur) {
            // Count only stagiaires belonging to this formateur
            $query->whereHas('formateurs', function ($q) use ($formateur) {
                $q->where('formateur_id', $formateur->id);
            });
        }])
            ->with(['medias' => function ($query) {
                $query->where('type', 'video');
            }])
            ->orderBy('titre')
            ->get();

        $formationsData = $formations->map(function ($formation) {
            return [
                'id' => $formation->id,
                'titre' => $formation->titre,
                'categorie' => $formation->categorie ?? 'Général',
                'description' => $formation->description,
                'image' => $formation->image,
                'nb_stagiaires' => $formation->stagiaires_count,
                'nb_videos' => $formation->medias->count(),
                'duree_estimee' => $formation->duree ?? 0,
            ];
        });

        return response()->json([
            'formations' => $formationsData,
        ]);
    }

    /**
     * API: Get stagiaires assigned to a specific formation
     * GET /formateur/formations/{id}/stagiaires
     */
    public function getStagiairesByFormation($formationId)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $formation = CatalogueFormation::findOrFail($formationId);

        // Get stagiaires for this formateur enrolled in this formation
        $stagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->whereHas('catalogue_formations', function ($query) use ($formationId) {
                $query->where('catalogue_formation_id', $formationId);
            })
            ->with(['user', 'watchedVideos'])
            ->get();

        $stagiairesData = $stagiaires->map(function ($stagiaire) use ($formation) {
            // Calculate progress
            $totalVideos = $formation->medias->where('type', 'video')->count();
            $watchedCount = $stagiaire->watchedVideos
                ->whereIn('media_id', $formation->medias->pluck('id'))
                ->count();

            $progress = $totalVideos > 0 ? round(($watchedCount / $totalVideos) * 100) : 0;

            return [
                'id' => $stagiaire->id,
                'prenom' => $stagiaire->user->prenom ?? '',
                'nom' => $stagiaire->user->nom ?? '',
                'email' => $stagiaire->user->email ?? '',
                'date_debut' => $stagiaire->date_debut_formation,
                'date_fin' => $stagiaire->date_fin_formation,
                'progress' => $progress,
                'status' => $stagiaire->statut ? 'active' : 'inactive',
            ];
        });

        return response()->json([
            'formation' => [
                'id' => $formation->id,
                'titre' => $formation->titre,
                'categorie' => $formation->categorie,
            ],
            'stagiaires' => $stagiairesData,
        ]);
    }

    /**
     * API: Assign formation to multiple stagiaires
     * POST /formateur/formations/{id}/assign
     */
    public function assignStagiaires(Request $request, $formationId)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $request->validate([
            'stagiaire_ids' => 'required|array|min:1',
            'stagiaire_ids.*' => 'required|integer|exists:stagiaires,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $formation = CatalogueFormation::findOrFail($formationId);
        $stagiaireIds = $request->stagiaire_ids;
        $dateDebut = $request->date_debut ?? now();
        $dateFin = $request->date_fin;

        // Verify stagiaires belong to this formateur
        $stagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->whereIn('id', $stagiaireIds)
            ->get();

        if ($stagiaires->count() !== count($stagiaireIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Certains stagiaires n\'appartiennent pas à ce formateur.',
            ], 403);
        }

        // Assign formation to each stagiaire
        $assigned = 0;
        foreach ($stagiaires as $stagiaire) {
            // Check if already assigned
            if (!$stagiaire->catalogue_formations->contains($formation->id)) {
                $stagiaire->catalogue_formations()->attach($formation->id);
                $assigned++;
            }

            // Update dates if provided
            if ($dateDebut) {
                $stagiaire->date_debut_formation = $dateDebut;
            }
            if ($dateFin) {
                $stagiaire->date_fin_formation = $dateFin;
            }
            $stagiaire->save();
        }

        return response()->json([
            'success' => true,
            'message' => "$assigned stagiaire(s) assigné(s) à la formation {$formation->titre}",
            'assigned_count' => $assigned,
        ]);
    }

    /**
     * API: Get formateur's stagiaires (not yet assigned to a formation)
     * GET /formateur/stagiaires/unassigned/{formationId}
     */
    public function getUnassignedStagiaires($formationId)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        // Get stagiaires belonging to formateur but NOT assigned to this formation
        $stagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->whereDoesntHave('catalogue_formations', function ($query) use ($formationId) {
                $query->where('catalogue_formation_id', $formationId);
            })
            ->with('user')
            ->get();

        $stagiairesData = $stagiaires->map(function ($stagiaire) {
            return [
                'id' => $stagiaire->id,
                'prenom' => $stagiaire->user->prenom ?? '',
                'nom' => $stagiaire->user->nom ?? '',
                'email' => $stagiaire->user->email ?? '',
            ];
        });

        return response()->json([
            'stagiaires' => $stagiairesData,
        ]);
    }

    /**
     * API: Update formation schedule for stagiaires
     * PUT /formateur/formations/{id}/schedule
     */
    public function updateSchedule(Request $request, $formationId)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $request->validate([
            'stagiaire_ids' => 'required|array|min:1',
            'stagiaire_ids.*' => 'required|integer|exists:stagiaires,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $formation = CatalogueFormation::findOrFail($formationId);

        // Update dates for selected stagiaires
        $updated = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->whereIn('id', $request->stagiaire_ids)
            ->update([
                'date_debut_formation' => $request->date_debut,
                'date_fin_formation' => $request->date_fin,
            ]);

        return response()->json([
            'success' => true,
            'message' => "$updated stagiaire(s) mis à jour",
            'updated_count' => $updated,
        ]);
    }

    /**
     * API: Get formation statistics
     * GET /formateur/formations/{id}/stats
     */
    public function getFormationStats($formationId)
    {
        $this->checkFormateur();
        $formateur = Auth::user()->formateur;

        $formation = CatalogueFormation::with('medias')->findOrFail($formationId);

        // Get stagiaires assigned to this formation (belonging to formateur)
        $stagiaires = Stagiaire::whereHas('formateurs', function ($query) use ($formateur) {
            $query->where('formateur_id', $formateur->id);
        })
            ->whereHas('catalogue_formations', function ($query) use ($formationId) {
                $query->where('catalogue_formation_id', $formationId);
            })
            ->with('watchedVideos')
            ->get();

        $totalVideos = $formation->medias->where('type', 'video')->count();
        $totalStagiaires = $stagiaires->count();
        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;

        foreach ($stagiaires as $stagiaire) {
            $watchedCount = $stagiaire->watchedVideos
                ->whereIn('media_id', $formation->medias->pluck('id'))
                ->count();

            $progress = $totalVideos > 0 ? round(($watchedCount / $totalVideos) * 100) : 0;

            if ($progress === 100) {
                $completed++;
            } elseif ($progress > 0) {
                $inProgress++;
            } else {
                $notStarted++;
            }
        }

        return response()->json([
            'formation' => [
                'id' => $formation->id,
                'titre' => $formation->titre,
                'nb_videos' => $totalVideos,
            ],
            'stats' => [
                'total_stagiaires' => $totalStagiaires,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'not_started' => $notStarted,
                'completion_rate' => $totalStagiaires > 0
                    ? round(($completed / $totalStagiaires) * 100, 1)
                    : 0,
            ],
        ]);
    }
}
