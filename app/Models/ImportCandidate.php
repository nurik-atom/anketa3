<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportCandidate extends Model
{
    protected $fillable = ['status', 'json_data'];

    protected $casts = [
        'json_data' => 'array',
    ];
}
