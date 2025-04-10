<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Agenda extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'evenement',
        'commentaire',
        'stagiaire_id',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }
}
