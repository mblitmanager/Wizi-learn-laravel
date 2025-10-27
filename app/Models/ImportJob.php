<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $table = 'import_jobs';

    protected $fillable = [
        'status', 'report_filename', 'details', 'started_at', 'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
