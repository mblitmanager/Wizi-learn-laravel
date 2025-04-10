<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Participation extends Model
{
    use HasFactory;
    protected $fillable = [
        'stagiaire_id',
        'quiz_id',
        'date',
        'heure',
        'score',
        'deja_jouer',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
