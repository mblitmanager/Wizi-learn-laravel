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



    // public function stagiaires()
    // {
    //     return $this->belongsToMany(Stagiaire::class, 'stagiaire_formations');
    // }

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
        return $this->hasMany(CatalogueFormation::class, 'formation_id');
    }

    public function tutoriels()
    {
        return $this->hasMany(Media::class)->where('type', 'tutoriel')->orderBy('ordre');
    }

    public function astuces()
    {
        return $this->hasMany(Media::class)->where('type', 'astuce')->orderBy('ordre');
    }
}
