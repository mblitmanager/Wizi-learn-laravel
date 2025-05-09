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
        'role',
        'statut',
        'user_id',
    ];



    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'stagiaire_formations');
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


    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_formations');
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
}
