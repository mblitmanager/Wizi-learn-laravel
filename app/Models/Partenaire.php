<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifiant',
        'adresse',
        'ville',
        'departement',
        'code_postal',
        'type',
        'logo'
    ];

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'partenaire_stagiaire', 'partenaire_id', 'stagiaire_id');
    }
}
