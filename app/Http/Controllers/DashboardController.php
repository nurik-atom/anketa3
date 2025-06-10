<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateHistory;
use App\Models\GardnerTestResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Получаем последнюю анкету пользователя
        $candidate = Candidate::where('user_id', $user->id)
            ->latest()
            ->first();

        // Получаем время последнего обновления
        $lastUpdate = null;
        if ($candidate) {
            $lastUpdate = CandidateHistory::where('candidate_id', $candidate->id)
                ->latest()
                ->first()?->created_at?->format('d.m.Y H:i');
        }

        // Получаем результаты теста Гарднера
        $gardnerTest = GardnerTestResult::where('user_id', $user->id)->first();

        return view('dashboard', [
            'candidate' => $candidate,
            'lastUpdate' => $lastUpdate,
            'gardnerTest' => $gardnerTest
        ]);
    }

    private function getFieldName($fieldName)
    {
        $fieldNames = [
            'full_name' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'gender' => 'Пол',
            'marital_status' => 'Семейное положение',
            'birth_date' => 'Дата рождения',
            'birth_place' => 'Место рождения',
            'current_city' => 'Текущий город',
            'photo' => 'Фото',
            'religion' => 'Религия',
            'is_practicing' => 'Практикующий',
            'family_members' => 'Члены семьи',
            'hobbies' => 'Хобби',
            'interests' => 'Интересы',
            'visited_countries' => 'Посещенные страны',
            'books_per_year' => 'Книг в год',
            'favorite_sports' => 'Любимые виды спорта',
            'entertainment_hours_weekly' => 'Часов развлечений в неделю',
            'educational_hours_weekly' => 'Часов обучения в неделю',
            'social_media_hours_weekly' => 'Часов в соцсетях в неделю',
            'has_driving_license' => 'Водительские права',
            'school' => 'Школа',
            'universities' => 'Университеты',
            'language_skills' => 'Языковые навыки',
            'computer_skills' => 'Компьютерные навыки',
            'work_experience' => 'Опыт работы',
            'total_experience_years' => 'Общий стаж',
            'job_satisfaction' => 'Удовлетворенность работой',
            'desired_position' => 'Желаемая должность',
            'expected_salary' => 'Ожидаемая зарплата',
            'employer_requirements' => 'Требования к работодателю',
            'gallup_pdf' => 'Тест Gallup',
            'mbti_type' => 'Тип личности MBTI',
            'step' => 'Шаг анкеты',
            'status' => 'Статус анкеты',
        ];

        return $fieldNames[$fieldName] ?? $fieldName;
    }

    private function formatValue($value)
    {
        if ($value === null) {
            return 'Не указано';
        }

        if (is_bool($value) || $value === 'true' || $value === 'false') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'Да' : 'Нет';
        }

        if ($value === 'completed') {
            return 'Завершена';
        }

        if ($value === 'in_progress') {
            return 'В процессе';
        }

        return $value;
    }
}
