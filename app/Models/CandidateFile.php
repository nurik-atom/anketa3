<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'size'
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
} 