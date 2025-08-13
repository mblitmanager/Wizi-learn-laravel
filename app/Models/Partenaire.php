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
        'logo',
        'contacts',
        'actif',
    ];

    protected $casts = [
        'contacts' => 'array',
        'actif' => 'boolean',
    ];

    /**
     * Normalise les contacts pour préserver les zéros initiaux des téléphones.
     */
    public function setContactsAttribute($value): void
    {
        $contacts = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($contacts)) {
            $this->attributes['contacts'] = json_encode([]);
            return;
        }

        $normalized = [];
        foreach ($contacts as $contact) {
            if (!is_array($contact)) {
                continue;
            }
            if (array_key_exists('tel', $contact) && $contact['tel'] !== null) {
                // Force en chaîne pour conserver d'éventuels zéros initiaux
                $contact['tel'] = (string) $contact['tel'];
            }
            $normalized[] = $contact;
        }

        $this->attributes['contacts'] = json_encode($normalized);
    }

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'partenaire_stagiaire', 'partenaire_id', 'stagiaire_id');
    }
}
