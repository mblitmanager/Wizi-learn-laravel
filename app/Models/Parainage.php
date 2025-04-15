<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ApiResource]
class Parainage extends Model
{
    use HasFactory;

    protected $table = 'parainages';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'parrain_id',
        'filleul_id',
    ];

    /**
     * Get the parrain (sponsor) that owns the parainage.
     */
    public function parrain(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class, 'parrain_id');
    }

    /**
     * Get the filleul (sponsored user) that owns the parainage.
     */
    public function filleul(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class, 'filleul_id');
    }
}
