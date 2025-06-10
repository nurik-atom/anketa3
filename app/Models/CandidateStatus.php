<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'status',
        'comment',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_SUBMITTED => 'На рассмотрении',
            self::STATUS_APPROVED => 'Одобрено',
            self::STATUS_REJECTED => 'Отклонено'
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
} 