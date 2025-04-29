<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
class Progression extends Model
{
     //

     use HasFactory;

     /**
      * Les attributs qui peuvent être assignés en masse.
      */
     protected $fillable = [
         'termine',
         'stagiaire_id',
         'quiz_id',
         'formation_id',
         'pourcentage',
         'explication',
         'score',
         'correct_answers',
         'total_questions',
         'time_spent',
         'completion_time'
     ];
     public function stagiaire()
     {
         return $this->belongsTo(Stagiaire::class);
     }

     public function quiz()
     {
         return $this->belongsTo(Quiz::class);
     }

     public function formations()
     {
         return $this->belongsTo(Formation::class);
     }
}
