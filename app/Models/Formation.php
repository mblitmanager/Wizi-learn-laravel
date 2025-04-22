<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Formation extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'titre',
        'slug',
        'description',
        'categorie',
        'icon',
        'image',
        'statut',
        'duree',
    ];

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_formation');
    }

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'stagiaire_formations');
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_formations');
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }


    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function catalogueFormation()
    {
        return $this->hasOne(CatalogueFormation::class, 'formation_id');
    }
}
