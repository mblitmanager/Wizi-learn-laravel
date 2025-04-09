<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QustionReponse extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'quiz_id',
        'question',
        'reponse',
        'type',
        'reponse_correct',
        'immage_illustration',
        'explication',
        'points',
        'astuce',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
