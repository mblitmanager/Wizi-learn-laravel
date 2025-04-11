<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
#[ApiResource]
class Formateur extends Model
{

    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'role',
        'user_id',
        'prenom',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formateur_formation');
    }
}
