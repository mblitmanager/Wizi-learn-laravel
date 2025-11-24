<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use App\Models\Questions;
use App\Models\ParticipationAnswer;
use App\Models\Stagiaire;
use App\Models\Quiz;
use App\Models\Challenge;
use App\Models\Question;

#[ApiResource]
class Participation extends Model
{
    use HasFactory;
    protected $fillable = [
        'stagiaire_id',
        'quiz_id',
        'date',
        'heure',
        'score',
        'deja_jouer',
        'current_question_id',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(Stagiaire::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    // New relationship to the current question for resume functionality
    public function currentQuestion()
    {
        return $this->belongsTo(Questions::class, 'current_question_id');
    }

    // Accessor to format resume payload
    public function getResumeDataAttribute()
    {
        return [
            'participation_id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'current_question_id' => $this->current_question_id,
            'answered_questions' => $this->answers->pluck('question_id'),
            'answers' => $this->answers->mapWithKeys(function ($answer) {
                return [$answer->question_id => $answer->answer_data];
            }),
            'score' => $this->score,
            'time_spent' => $this->heure,
        ];
    }

    public function answers()
    {
        return $this->hasMany(ParticipationAnswer::class);
    }
}
