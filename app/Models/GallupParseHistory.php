<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GallupParseHistory extends Model
{
    use HasFactory;

    protected $table = 'gallup_parse_history';

    protected $fillable = [
        'candidate_id',
        'step',
        'details',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Связь с кандидатом
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Создать запись в истории
     */
    public static function createHistory(int $candidateId, string $step, string $status = 'in_progress', ?string $details = null): self
    {
        return self::create([
            'candidate_id' => $candidateId,
            'step' => $step,
            'status' => $status,
            'details' => $details,
        ]);
    }

    /**
     * Получить последний статус для кандидата
     */
    public static function getLastStatus(int $candidateId): ?self
    {
        return self::where('candidate_id', $candidateId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Получить историю для кандидата
     */
    public static function getHistoryForCandidate(int $candidateId, int $limit = 50)
    {
        return self::where('candidate_id', $candidateId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Очистить старые записи (старше указанного количества дней)
     */
    public static function cleanOldRecords(int $days = 7): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }
}
