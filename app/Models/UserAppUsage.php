<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'first_used_at',
        'last_used_at',
        'app_version',
        'device_model',
        'os_version',
    ];

    protected $casts = [
        'first_used_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
