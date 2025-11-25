<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'status',
        'started_at',
        'completed_at',
        'score',
        'correct_answers',
        'time_spent',
        'current_question_id'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'correct_answers' => 'integer',
        'time_spent' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizParticipationAnswer::class, 'participation_id');
    }

    public function quiz()

    {
        return $this->belongsTo(Quiz::class);
    }

    // Accessor to format resume payload for frontend
    public function getResumeDataAttribute()
    {
        return [
            'participation_id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'current_question_id' => $this->current_question_id,
            'answers' => $this->answers->mapWithKeys(function ($answer) {
                return [$answer->question_id => $answer->answer_ids];
            })->toArray(),
            'score' => $this->score,
            'time_spent' => $this->time_spent,
        ];
    }
}
