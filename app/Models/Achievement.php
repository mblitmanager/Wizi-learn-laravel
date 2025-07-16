<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'condition',
        'description',
        'icon',
        'level', // bronze, silver, gold, etc.
    ];

    public function stagiaires()
    {
        return $this->belongsToMany(User::class, 'stagiaire_achievements')->withTimestamps();
    }
}
