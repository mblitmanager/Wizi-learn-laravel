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
    ];

    /**
     * Relation avec le modèle Formation (Many-to-One).
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_catalogue_formations', 'catalogue_formation_id', 'stagiaire_id');
    }
}
