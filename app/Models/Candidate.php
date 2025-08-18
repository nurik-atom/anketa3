<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        // Basic Information
        'user_id',
        'step',
        'full_name',
        'patronymic',
        'email',
        'phone',
        'gender',
        'marital_status',
        'birth_date',
        'birth_place',
        'current_city',
        'photo',

        // Additional Information
        'religion',
        'is_practicing',
        'family_members',
        'hobbies',
        'interests',
        'visited_countries',
        'books_per_year',
        'favorite_sports',
        'entertainment_hours_weekly',
        'educational_hours_weekly',
        'social_media_hours_weekly',
        'driving_license_type',

        // Education and Work
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

        // Assessments
        'gallup_pdf',
        'anketa_pdf',
        'mbti_type',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_practicing' => 'boolean',
        'family_members' => 'array',
        'visited_countries' => 'array',
        'favorite_sports' => 'array',
        'universities' => 'array',
        'language_skills' => 'array',
        'computer_skills' => 'string',
        'work_experience' => 'array',
        'books_per_year' => 'integer',
        'entertainment_hours_weekly' => 'integer',
        'educational_hours_weekly' => 'integer',
        'social_media_hours_weekly' => 'integer',
        'total_experience_years' => 'integer',
        'job_satisfaction' => 'integer',
        'expected_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function additionalInformation(): HasOne
    {
        return $this->hasOne(AdditionalInformation::class);
    }

    public function educationWork(): HasOne
    {
        return $this->hasOne(EducationWork::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CandidateFile::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(CandidateHistory::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(CandidateStatus::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CandidateComment::class);
    }

    public function getLatestStatusAttribute()
    {
        return $this->statuses()->latest()->first();
    }

    public function getCurrentStatusAttribute()
    {
        return $this->latest_status?->status ?? 'draft';
    }

    public function gallupTalents()
    {
        return $this->hasMany(GallupTalent::class);
    }

    public function gallupReports()
    {
        return $this->hasMany(GallupReport::class);
    }

    /**
     * Получает структурированные данные о семье
     */
    public function getFamilyStructured()
    {
        $familyData = $this->family_members ?? [];
        
        // Если это новая структура
        if (is_array($familyData) && isset($familyData['parents'])) {
            return [
                'parents' => $familyData['parents'] ?? [],
                'siblings' => $familyData['siblings'] ?? [],
                'children' => $familyData['children'] ?? [],
                'is_new_structure' => true
            ];
        }
        
        // Если это старая структура - преобразуем
        $parents = [];
        $siblings = [];
        $children = [];
        
        if (is_array($familyData)) {
            foreach ($familyData as $member) {
                if (!is_array($member)) continue;
                
                $type = $member['type'] ?? '';
                switch ($type) {
                    case 'Отец':
                    case 'Мать':
                        $parents[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                            'profession' => $member['profession'] ?? ''
                        ];
                        break;
                    case 'Брат':
                    case 'Сестра':
                        $siblings[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? ''
                        ];
                        break;
                    case 'Сын':
                    case 'Дочь':
                        $children[] = [
                            'name' => $member['profession'] ?? '', // В старой структуре имя было в поле profession
                            'birth_year' => $member['birth_year'] ?? ''
                        ];
                        break;
                }
            }
        }
        
        return [
            'parents' => $parents,
            'siblings' => $siblings,
            'children' => $children,
            'is_new_structure' => false
        ];
    }

    /**
     * Получает форматированный список родителей
     */
    public function getFormattedParents()
    {
        $family = $this->getFamilyStructured();
        $formatted = [];
        
        foreach ($family['parents'] as $parent) {
            $line = $parent['relation'] ?? 'Не указано';
            $line .= ' - ' . ($parent['birth_year'] ?? 'Не указано') . ' г.р.';
            if (!empty($parent['profession'])) {
                $line .= ' - ' . $parent['profession'];
            }
            $formatted[] = $line;
        }
        
        return $formatted;
    }

    /**
     * Получает форматированный список братьев и сестер
     */
    public function getFormattedSiblings()
    {
        $family = $this->getFamilyStructured();
        $formatted = [];
        
        foreach ($family['siblings'] as $sibling) {
            $line = $sibling['relation'] ?? 'Не указано';
            $line .= ' - ' . ($sibling['birth_year'] ?? 'Не указано') . ' г.р.';
            $formatted[] = $line;
        }
        
        return $formatted;
    }

    /**
     * Получает форматированный список детей
     */
    public function getFormattedChildren()
    {
        $family = $this->getFamilyStructured();
        $formatted = [];
        
        foreach ($family['children'] as $child) {
            $line = $child['name'] ?? 'Не указано';
            $line .= ' - ' . ($child['birth_year'] ?? 'Не указано') . ' г.р.';
            $formatted[] = $line;
        }
        
        return $formatted;
    }

    public function gallupReportByType(string $type): ?GallupReport
    {
        return $this->gallupReports()->where('type', $type)->latest()->first();
    }

    public function gardnerTestResult()
    {
        return $this->hasOneThrough(GardnerTestResult::class, User::class);
    }
}
