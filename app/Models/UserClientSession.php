<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClientSession extends Model
{
    protected $table = 'user_client_sessions';

    protected $fillable = [
        'user_id',
        'device_id',
        'platform',
        'app_version',
        'ip',
        'last_seen_at',
    ];

    public $dates = ['last_seen_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
