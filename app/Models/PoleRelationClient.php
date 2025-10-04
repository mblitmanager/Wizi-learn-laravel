<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;

#[ApiResource(
    paginationItemsPerPage: 10
)]
class PoleRelationClient extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'role',
        'stagiaire_id',
        'user_id',
        'prenom',
        'telephone',
    ];

    public function stagiaires()
    {
        return $this->belongsToMany(Stagiaire::class, 'pole_relation_client_stagiaire');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
