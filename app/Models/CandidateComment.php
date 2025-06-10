<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'comment',
        'commented_by',
        'is_private'
    ];

    protected $casts = [
        'is_private' => 'boolean'
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
} 