<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;

#[ApiResource(
    paginationItemsPerPage: 10
    )]
class Commercial extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'role',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function stagiaires()
    {
        return $this->hasMany(Stagiaire::class);
    }

}
