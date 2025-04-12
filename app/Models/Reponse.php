<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
class Reponse extends Model
{
      /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'id',
        'text',
        'is_correct',
        'position',
        'match_pair',
        'bank_group',
        'flashcard_back',
        'question_id',

    ];


     /**
     * Relation avec le modèle Questions (Many-to-One).
     */
    public function questions()
    {
        return $this->belongsTo(Questions::class);
    }
}
