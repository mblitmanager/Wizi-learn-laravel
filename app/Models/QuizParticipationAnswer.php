<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizParticipationAnswer extends Model
{
    protected $fillable = [
        'participation_id',
        'question_id',
        'answer_ids',
    ];

    protected $casts = [
        'answer_ids' => 'array',
    ];

    public function participation()
    {
        return $this->belongsTo(QuizParticipation::class, 'participation_id');
    }

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }
} 