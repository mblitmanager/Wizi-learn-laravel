<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistories extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'city',
        'device',
        'browser',
        'platform',
        'login_at',
        'logout_at'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime'
    ];
}
