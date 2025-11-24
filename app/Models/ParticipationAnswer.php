<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'participation_id',
        'question_id',
        'answer_data', // JSON
    ];

    protected $casts = [
        'answer_data' => 'array',
    ];

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }
}
