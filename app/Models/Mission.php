<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'type', 'goal', 'reward', 'start_date', 'end_date'
    ];
    public function stagiaires() {
        return $this->belongsToMany(Stagiaire::class, 'mission_user', 'mission_id', 'stagiaire_id')
            ->withPivot('progress', 'completed', 'completed_at')
            ->withTimestamps();
    }
} 