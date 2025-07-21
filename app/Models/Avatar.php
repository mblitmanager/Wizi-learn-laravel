<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'image', 'unlock_condition', 'price_points'
    ];
    public function users() {
        return $this->belongsToMany(User::class, 'avatar_user', 'avatar_id', 'user_id')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
} 