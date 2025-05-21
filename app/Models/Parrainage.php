<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parrainage extends Model
{
    protected $table = 'parrainages';
    protected $fillable = ['id', 'filleul_id', 'parrain_id', 'date_parrainage', 'points'];

    public function filleul(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filleul_id');
    }

    public function parrain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parrain_id');
    }
}
