<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GardnerTestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'answers',
        'results',
    ];

    protected $casts = [
        'answers' => 'array',
        'results' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 