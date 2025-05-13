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
        'formation_id',
        'status', // Ajout du champ status
    ];

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function questions()
    {
        return $this->hasMany(Questions::class);
    }

    protected static function booted()
    {
        static::saving(function ($quiz) {
            $quiz->nb_points_total = $quiz->questions()->sum('points');
        });
    }
}
