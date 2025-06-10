<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalInformation extends Model
{
    protected $fillable = [
        'candidate_id',
        'religion',
        'is_practicing',
        'family_members',
        'hobbies',
        'interests',
        'visited_countries',
        'books_per_year',
        'favorite_sports',
        'favorite_entertainment',
        'entertainment_hours_weekly',
        'educational_hours_weekly',
        'social_media_hours_weekly',
        'driving_license_type',
    ];

    protected $casts = [
        'is_practicing' => 'boolean',
        'family_members' => 'array',
        'visited_countries' => 'array',
        'favorite_sports' => 'array',
        'favorite_entertainment' => 'array',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
} 