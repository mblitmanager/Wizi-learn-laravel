<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

#[ApiResource(
    paginationItemsPerPage: 10
)]
class Stagiaire extends Model
{
    use HasFactory;

    public function partenaire()
    {
        return $this->belongsTo(Partenaire::class, 'partenaire_id');
    }
    use HasFactory;
    protected $fillable = [
        'civilite',
        'prenom',
        'telephone',
        'adresse',
        'date_naissance',
        'ville',
        'code_postal',
        'date_debut_formation',
        'date_inscription',
        'role',
        'statut',
        'user_id',
        'date_fin_formation',
        'onboarding_seen',
        'partenaire_id',
    ];




    public function catalogue_formations()
    {
        return $this->belongsToMany(CatalogueFormation::class, 'stagiaire_catalogue_formations', 'stagiaire_id', 'catalogue_formation_id')
            ->withPivot(['date_debut', 'date_inscription', 'date_fin', 'formateur_id'])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_stagiaire');
    }


    public function commercials()
    {
        return $this->belongsToMany(Commercial::class, 'commercial_stagiaire');
    }


    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function poleRelationClients()
    {
        return $this->belongsToMany(PoleRelationClient::class, 'pole_relation_client_stagiaire');
    }



    public function commercial()
    {
        return $this->belongsToMany(Commercial::class, 'commercial_stagiaire');
    }

    public function formateur()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_stagiaire');
    }

    public function poleRelationClient()
    {
        return $this->belongsToMany(PoleRelationClient::class, 'pole_relation_client_stagiaire');
    }

    public function medias()
    {
        return $this->belongsToMany(Media::class)
            ->withPivot('is_watched', 'watched_at')
            ->withTimestamps();
    }
    // Ajout des relations pour les succès
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'stagiaire_achievements')->withTimestamps();
    }

    public function classements()
    {
        return $this->hasMany(Classement::class);
    }

    public function progressions()
    {
        return $this->hasMany(Progression::class);
    }
    use SoftDeletes;

    public function referrals()
    {
        return $this->hasMany(Parrainage::class, 'parrain_id');
    }

    public function watchedVideos()
    {
        return $this->medias()->where('is_watched', true);
    }

    /**
     * Relation avec les participations aux quiz
     */
    public function quizParticipations()
    {
        return $this->hasMany(QuizParticipation::class, 'user_id', 'user_id');
    }

    /**
     * Relation avec les participations aux quiz via le stagiaire_id
     * (si vous avez un champ stagiaire_id dans quiz_participations)
     */
    public function participationsQuiz()
    {
        return $this->hasMany(QuizParticipation::class, 'stagiaire_id');
    }

    /**
     * Relation pour les quiz complétés
     */
    public function quizCompletes()
    {
        return $this->quizParticipations()->where('status', 'completed');
    }

    /**
     * Relation pour les dernières activités
     */
    public function dernieresActivites()
    {
        return $this->hasMany(QuizParticipation::class, 'user_id', 'user_id')
            ->orderBy('completed_at', 'desc')
            ->take(10);
    }

    // Dans App\Models\Stagiaire.php
    /**
     * Obtenir la dernière activité du stagiaire
     */
    public function getDerniereActiviteAttribute(): ?Carbon
    {
        $dates = [];

        // Dernière participation à un quiz
        if ($this->quizParticipations->isNotEmpty()) {
            $derniereParticipation = $this->quizParticipations
                ->whereNotNull('completed_at')
                ->max('completed_at');
            if ($derniereParticipation) {
                $dates[] = Carbon::parse($derniereParticipation);
            }
        }

        // Dernière progression
        if ($this->progressions->isNotEmpty()) {
            $derniereProgression = $this->progressions
                ->whereNotNull('updated_at')
                ->max('updated_at');
            if ($derniereProgression) {
                $dates[] = Carbon::parse($derniereProgression);
            }
        }

        // Dernière vidéo regardée
        if ($this->watchedVideos->isNotEmpty()) {
            $derniereVideo = $this->watchedVideos
                ->whereNotNull('watched_at')
                ->max('watched_at');
            if ($derniereVideo) {
                $dates[] = Carbon::parse($derniereVideo);
            }
        }

        return count($dates) > 0 ? max($dates) : null;
    }

    /**
     * Vérifier si le stagiaire a utilisé l'application
     */
    public function getAUtiliseApplicationAttribute(): bool
    {
        // Vérifier les participations aux quiz
        if ($this->quizParticipations->count() > 0) {
            return true;
        }

        // Vérifier les progressions
        if ($this->progressions->where('termine', true)->count() > 0) {
            return true;
        }

        // Vérifier les vidéos regardées
        if ($this->watchedVideos->count() > 0) {
            return true;
        }

        // Vérifier les classements
        if ($this->classements->count() > 0) {
            return true;
        }

        return false;
    }
}
