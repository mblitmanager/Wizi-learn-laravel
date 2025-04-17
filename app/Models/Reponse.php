<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
class Reponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'position',
        'match_pair',
        'bank_group',
        'flashcard_back'
    ];
    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }
    public function getCorrectAnswers()
    {
        return $this->where('is_correct', true)->get();
    }
    public function getIncorrectAnswers()
    {
        return $this->where('is_correct', false)->get();
    }
}
