<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
class Classement extends Model
{
    //

    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'rang',
        'stagiaire_id',
        'quiz_id',
        'points',
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
