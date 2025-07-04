<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class CatalogueFormation extends Model
{
    use HasFactory;

    protected $table = 'catalogue_formations';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'titre',
        'description',
        'statut',
        'certification',
        'prerequis',
        'duree',
        'image_url',
        'tarif',
        'formation_id',
        'cursus_pdf',
    ];

    /**
     * Relation avec le modèle Formation (Many-to-One).
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Relation avec le modèle Formation (One-to-Many, accès via formation_id sur Formation)
     */
    public function formations()
    {
        return $this->hasMany(Formation::class, 'formation_id', 'id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_catalogue_formation');
    }
    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_catalogue_formations', 'catalogue_formation_id', 'stagiaire_id')
            ->withPivot('date_debut', 'date_inscription', 'date_fin', 'formateur_id')
            ->withTimestamps();
    }

    /**
     * Accesseur pour obtenir l'URL complète du PDF du cursus
     */
    public function getCursusPdfUrlAttribute()
    {
        return $this->cursus_pdf ? asset('storage/' . $this->cursus_pdf) : null;
    }

}
