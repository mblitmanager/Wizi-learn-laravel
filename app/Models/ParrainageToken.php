<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParrainageToken extends Model
{
    protected $table = 'parrainage_tokens';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
