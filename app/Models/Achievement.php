<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     * - level: palier du succès (bronze, silver, gold, etc.)
     * - quiz_id: id du quiz associé pour les succès de type quiz
     */
    protected $fillable = [
        'name',
        'type',
        'condition',
        'description',
        'icon',
        'level', // bronze, silver, gold, etc.
        'quiz_id', // id du quiz associé
        'code', // code unique pour identifier l'achievement
    ];

    /**
     * Si un succès est lié à un quiz spécifique
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_achievements', 'achievement_id', 'stagiaire_id')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
    // Pour compatibilité avec withCount('users')
    public function users()
    {
        return $this->belongsToMany(Stagiaire::class, 'stagiaire_achievements', 'achievement_id', 'stagiaire_id');
    }
}
