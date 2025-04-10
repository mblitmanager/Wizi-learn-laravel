<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Media extends Model
{
    use HasFactory;
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'url',
        'type',
        'titre',
        'description',
    ];
}
