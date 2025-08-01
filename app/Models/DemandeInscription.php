<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeInscription extends Model
{
    protected $fillable = [
        'parrain_id',
        'filleul_id',
        'formation_id',
        'statut',
        'donnees_formulaire',
        'lien_parrainage',
        'motif',
        'date_demande',
        'date_inscription'
    ];

    protected $casts = [
        'donnees_formulaire' => 'array',
        'date_demande' => 'datetime',
        'date_inscription' => 'datetime',
    ];

    public function parrain()
    {
        return $this->belongsTo(User::class, 'parrain_id');
    }

    public function filleul()
    {
        return $this->belongsTo(User::class, 'filleul_id');
    }

    public function formation()
    {
        return $this->belongsTo(CatalogueFormation::class, 'formation_id');
    }

    // Accesseur pour formater les données
    public function getFormattedDonneesAttribute()
    {
        if (empty($this->donnees_formulaire)) {
            return [];
        }

        $donnees = is_array($this->donnees_formulaire)
            ? $this->donnees_formulaire
            : json_decode($this->donnees_formulaire, true);

        $formatted = [];
        foreach ($donnees as $key => $value) {
            $formatted[ucfirst(str_replace('_', ' ', $key))] = is_array($value)
                ? json_encode($value, JSON_PRETTY_PRINT)
                : $value;
        }

        return $formatted;
    }
}
