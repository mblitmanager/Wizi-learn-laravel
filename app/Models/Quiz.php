<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;


#[ApiResource]
class Quiz extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'titre',
        'description',
        'duree',
        'niveau',
        'nb_points_total',
    ];

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function qustionReponses()
    {
        return $this->hasMany(QustionReponse::class);
    }
}
