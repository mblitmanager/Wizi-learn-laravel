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
        'time_spent'
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

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
