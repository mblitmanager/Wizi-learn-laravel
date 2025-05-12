<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrespondancePair extends Model
{
    protected $fillable = ['question_id', 'left_text', 'right_text'];

    public function question()
    {
        return $this->belongsTo(Questions::class);
    }
}
