<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    paginationItemsPerPage: 10
)]
class Stagiaire extends Model
{
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
    ];




    public function catalogue_formations()
    {
        return $this->belongsToMany(CatalogueFormation::class, 'stagiaire_catalogue_formations', 'stagiaire_id', 'catalogue_formation_id')
            ->withPivot('date_debut', 'date_inscription', 'date_fin', 'formateur_id')
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
}
