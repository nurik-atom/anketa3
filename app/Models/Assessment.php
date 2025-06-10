<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'candidate_id',
        'gallup_pdf_path',
        'mbti_type',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
} 