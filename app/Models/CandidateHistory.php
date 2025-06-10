<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateHistory extends Model
{
    use HasFactory;

    protected $table = 'candidate_history';

    protected $fillable = [
        'candidate_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
        'ip_address'
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
} 