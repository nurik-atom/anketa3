<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationWork extends Model
{
    protected $fillable = [
        'candidate_id',
        'school',
        'universities',
        'language_skills',
        'computer_skills',
        'work_experience',
        'total_experience_years',
        'job_satisfaction',
        'desired_position',
        'expected_salary',
        'employer_requirements',
    ];

    protected $casts = [
        'universities' => 'array',
        'language_skills' => 'array',
        'computer_skills' => 'string',
        'work_experience' => 'array',
        'expected_salary' => 'decimal:2',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
} 