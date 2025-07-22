<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Media extends Model
{
    use HasFactory;
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'url',
        'type',
        'categorie',
        'titre',
        'description',
        'formation_id',
        'duree',
        'ordre',

    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class)
            ->withPivot('is_watched', 'watched_at')
            ->withTimestamps();
    }
}
