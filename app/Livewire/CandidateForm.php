<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\CandidateFile;
use App\Models\CandidateHistory;
use App\Models\CandidateStatus;
use App\Jobs\ProcessGallupFile;
use App\Rules\CyrillicRule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Smalot\PdfParser\Parser;

class CandidateForm extends Component
{
    use WithFileUploads;

    protected $listeners = ['removePhoto' => 'removePhoto'];

    public $currentStep = 1;
    public $totalSteps = 4;
    public $candidate = null;

    // Step 1: Basic Information
    public $full_name;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $email;
    public $phone;
    public $gender;
    public $marital_status;
    public $birth_date;
    public $birth_place;
    public $current_city;
    public $photo;
    public $photoPreview;

    // Step 2: Additional Information
    public $religion;
    public $is_practicing;
    public $family_members = [];
    // Новая структура для разделенных категорий семьи
    public $parents = [];
    public $siblings = [];
    public $children = [];
    public $hobbies;
    public $interests;
    public $visited_countries = [];
    public $newCountry = '';
    public $books_per_year;
    public $books_per_year_min = 0;
    public $books_per_year_max = 50;
    public $favorite_sports = [];
    public $entertainment_hours_weekly;
    public $educational_hours_weekly;
    public $social_media_hours_weekly;
    public $has_driving_license;

    // Step 3: Education and Work
    public $school;
    public $universities = [];
    public $language_skills = [];
    public $computer_skills;
    public $work_experience = [];
    public $total_experience_years;
    public $job_satisfaction;
    public $desired_position;
    public $expected_salary;
    public $employer_requirements;

    // Step 4: Tests
    public $gallup_pdf;
    public $mbti_type;

    // Загружаем списки
    public $countries = [];
    public $languages = [];
    public $religions = [];
    public $sports = [];

    public $familyYears = [];

    public function mount($candidateId = null)
    {
        try {
            // Устанавливаем значение по умолчанию для books_per_year
            $this->books_per_year = '0';
            $this->books_per_year_min = 0;
            $this->books_per_year_max = 0;
            $this->updateBooksPerYear();

            // Устанавливаем начальные значения для часов в неделю
            $this->entertainment_hours_weekly = 0;
            $this->educational_hours_weekly = 0;
            $this->social_media_hours_weekly = 0;

            // Загружаем списки из JSON файлов
            $jsonPath = base_path('resources/json/countries.json');
            logger()->debug('JSON path:', ['path' => $jsonPath, 'exists' => file_exists($jsonPath)]);

            if (!file_exists($jsonPath)) {
                throw new \Exception("JSON file not found at: " . $jsonPath);
            }

            $jsonContent = file_get_contents($jsonPath);
            logger()->debug('JSON content:', ['content' => substr($jsonContent, 0, 100)]);

            $countriesData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON decode error: " . json_last_error_msg());
            }

            logger()->debug('Decoded countries:', [
                'count' => count($countriesData),
                'first_country' => $countriesData[0] ?? null
            ]);

            $this->countries = collect($countriesData)->map(function($country) {
                $data = [];

                // Проверяем наличие каждого ключа перед добавлением
                if (isset($country['name_ru'])) {
                    $data['name_ru'] = $country['name_ru'];
                }

                if (isset($country['flag_url'])) {
                    // Убедимся, что URL флага начинается с http:// или https://
                    $flagUrl = $country['flag_url'];
                    if (strpos($flagUrl, '//') === 0) {
                        $flagUrl = 'https:' . $flagUrl;
                    }
                    $data['flag_url'] = $flagUrl;
                }

                if (isset($country['iso_code2'])) {
                    $data['iso_code2'] = $country['iso_code2'];
                }

                if (isset($country['iso_code3'])) {
                    $data['iso_code3'] = $country['iso_code3'];
                }

                return $data;
            })
            ->filter(function($country) {
                // Оставляем только страны, у которых есть как минимум name_ru
                return !empty($country['name_ru']);
            })
            ->values()
            ->all();

            logger()->debug('Final countries array:', [
                'count' => count($this->countries),
                'first_country' => $this->countries[0] ?? null,
                'keys' => array_keys($this->countries[0] ?? [])
            ]);

        } catch (\Exception $e) {
            logger()->error('Error loading countries:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->countries = [];
        }

        // Загружаем языки с обработкой ошибок
        try {
            $this->loadLanguages();
        } catch (\Exception $e) {
            logger()->error('Error loading languages: ' . $e->getMessage());
            // Fallback к базовым языкам
            $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский'];
        }

        $this->religions = config('lists.religions');
        $this->sports = config('lists.sports');

        // Инициализируем массивы
        $this->universities = [];
        $this->family_members = [];
        $this->parents = [];
        $this->siblings = [];
        $this->children = [];
        $this->visited_countries = [];
        $this->favorite_sports = '';
        $this->language_skills = [];
        $this->work_experience = [];
        $this->computer_skills = '';

        logger()->debug('Mount: work_experience initialized as empty array');

        // Инициализируем значения для step3 ползунков
        $this->total_experience_years = 0;
        $this->job_satisfaction = 1;
        $this->expected_salary = 0;

        // Устанавливаем email из авторизованного пользователя
        $this->email = auth()->user()->email;

        $this->familyYears = range(2025, 1920);

        if ($candidateId) {
            $this->candidate = Candidate::findOrFail($candidateId);
            
            // Дополнительная проверка прав доступа на уровне Livewire компонента
            // Пользователь может редактировать только свои анкеты, администратор - любые
            if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для редактирования этой анкеты.');
            }
            
            $this->loadCandidateData();
            // Если анкета завершена (step >= 5), показываем первый шаг для редактирования
            $this->currentStep = $this->candidate->step >= 5 ? 1 : $this->candidate->step;
        } else {
            // Проверяем, есть ли незавершенная анкета для текущего пользователя
            $userId = auth()->id();
            if ($userId) {
                $this->candidate = Candidate::where('user_id', $userId)
                    ->latest()
                    ->first();

                if ($this->candidate) {
                    $this->loadCandidateData();
                    // Если анкета завершена (step >= 5), показываем первый шаг для редактирования
                    $this->currentStep = $this->candidate->step >= 5 ? 1 : $this->candidate->step;
                } else {
                    // Инициализируем пустые массивы для нового кандидата
                    $this->family_members = [];
                    $this->parents = [];
                    $this->siblings = [];
                    $this->children = [];
                    // Убеждаемся что work_experience пустой массив
                    $this->work_experience = [];
                }
            }
        }

        // Инициализируем валидацию для текущего шага
        $this->reinitializeValidation();
    }

    protected function loadCandidateData()
    {
        // Basic Information
        $this->full_name = $this->candidate->full_name;

        // Разделяем ФИО на части
        if ($this->full_name) {
            $nameParts = explode(' ', $this->full_name);
            $this->last_name = $nameParts[0] ?? '';
            $this->first_name = $nameParts[1] ?? '';
            $this->middle_name = $nameParts[2] ?? '';
        }

        $this->email = $this->candidate->email;
        $this->phone = $this->candidate->phone;
        $this->gender = $this->candidate->gender;
        $this->marital_status = $this->candidate->marital_status;
        $this->birth_date = $this->candidate->birth_date?->format('Y-m-d');
        $this->birth_place = $this->candidate->birth_place;
        $this->current_city = $this->candidate->current_city;

        // Загружаем фото и создаем предпросмотр
        if ($this->candidate->photo) {
            $this->photo = $this->candidate->photo;
            $this->photoPreview = Storage::disk('public')->exists($this->photo)
                ? Storage::disk('public')->url($this->photo)
                : null;
        }

        // Additional Information
        $this->religion = $this->convertReligionToRussian($this->candidate->religion);
        logger()->debug('Loading candidate religion:', ['original' => $this->candidate->religion, 'converted' => $this->religion]);
        $this->is_practicing = $this->candidate->is_practicing;
        $this->family_members = $this->candidate->family_members ?? [];
        
        // Инициализируем новые категории семьи
        $this->loadFamilyCategories();
        
        $this->hobbies = $this->candidate->hobbies;
        $this->interests = $this->candidate->interests;
        $this->visited_countries = $this->candidate->visited_countries ?? [];
        $this->books_per_year = $this->candidate->books_per_year;
        // Парсим диапазон книг для двойного слайдера
        $this->parseBooksPerYear($this->candidate->books_per_year);
        // Обновляем books_per_year на основе диапазона
        $this->updateBooksPerYear();
        $this->favorite_sports = $this->candidate->favorite_sports ?? [];
        $this->entertainment_hours_weekly = $this->candidate->entertainment_hours_weekly;
        $this->educational_hours_weekly = $this->candidate->educational_hours_weekly;
        $this->social_media_hours_weekly = $this->candidate->social_media_hours_weekly;
        $this->has_driving_license = $this->candidate->has_driving_license;

        // Education and Work
        $this->school = $this->candidate->school;
        $this->universities = $this->candidate->universities ?? [];
        $this->language_skills = $this->candidate->language_skills ?? [];
        $this->computer_skills = $this->candidate->computer_skills ?? '';
        $this->work_experience = $this->convertWorkExperienceFormat($this->candidate->work_experience ?? []);
        logger()->debug('Work experience loaded:', ['original' => $this->candidate->work_experience, 'converted' => $this->work_experience]);
        $this->total_experience_years = $this->candidate->total_experience_years;
        $this->job_satisfaction = $this->candidate->job_satisfaction;
        $this->desired_position = $this->candidate->desired_position;
        $this->expected_salary = $this->candidate->expected_salary;
        $this->employer_requirements = $this->candidate->employer_requirements;

        // Tests
        if ($this->candidate->gallup_pdf) {
            $this->gallup_pdf = $this->candidate->gallup_pdf;
        }
        $this->mbti_type = $this->candidate->mbti_type;
    }

    protected function rules()
    {
        $rules = [
        // Step 1 validation rules
            'last_name' => ['required', 'string', 'max:255', new CyrillicRule()],
            'first_name' => ['required', 'string', 'max:255', new CyrillicRule()],
            'middle_name' => ['nullable', 'string', 'max:255', new CyrillicRule()],
        'email' => 'required|email|max:255',
            // Универсальный международный формат: допускает +, цифры, пробелы, дефисы и скобки; 8-15 цифр всего
            'phone' => ['required', 'string', 'regex:/^(?=(?:.*\d){8,15})\+?[\d\s\-\(\)]{7,20}$/'],
            'gender' => 'required|in:Мужской,Женский',
            'marital_status' => 'required|in:Холост/Не замужем,Женат/Замужем,Разведен(а),Вдовец/Вдова',
            'birth_date' => 'required|date|before:today',
            'birth_place' => ['required', 'string', 'max:255', new CyrillicRule()],
        'current_city' => ['required', 'string', 'max:255', new CyrillicRule()],
            'photo' => !$this->candidate?->photo ? 'required|image|max:20480' : 'nullable|image|max:20480',

            // Step 2 validation rules
            'religion' => 'required|string|in:' . implode(',', array_values(config('lists.religions'))),
            'is_practicing' => 'required|boolean',
            // Отключаем валидацию старой структуры family_members
            // 'family_members' => 'nullable|array|min:0',
            // 'family_members.*.type' => 'required|string|in:Отец,Мать,Брат,Сестра,Жена,Муж,Сын,Дочь',
            // 'family_members.*.birth_year' => 'required|integer|min:1900|max:' . date('Y'),
            // 'family_members.*.profession' => ['required', 'string', 'max:255'],
            
            // Новые правила валидации для категорий семьи - полностью убираем правила
            'parents' => 'sometimes|array|max:2',
            'siblings' => 'sometimes|array', 
            'children' => 'sometimes|array',
            'hobbies' => ['required', 'string', 'max:1000'],
            'interests' => ['required', 'string', 'max:1000'],
            'visited_countries' => 'required|array|min:1',
            'visited_countries.*' => 'required|string|in:' . implode(',', collect($this->countries)->pluck('name_ru')->all()),
            'books_per_year_min' => 'required|integer|min:0|max:100',
            'books_per_year_max' => 'required|integer|min:0|max:100|gte:books_per_year_min',
            'favorite_sports' => ['required', 'string', 'max:1000', new CyrillicRule()],
            'entertainment_hours_weekly' => 'required|integer|min:0|max:168',
            'educational_hours_weekly' => 'required|integer|min:0|max:168',
            'social_media_hours_weekly' => 'required|integer|min:0|max:168',
            'has_driving_license' => 'required|boolean',

            // Step 3 validation rules
            'school' => ['required', 'string', 'max:255'],
            'universities' => 'nullable|array|min:0',
            'universities.*.name' => 'required|string|max:255',
            'universities.*.graduation_year' => 'required|integer|min:1950',
            'universities.*.speciality' => 'required|string|max:255',
            'universities.*.gpa' => 'required|numeric|min:0|max:4',
            'language_skills' => 'required|array|min:1',
            'language_skills.*.language' => 'required|string|max:255',
            'language_skills.*.level' => 'required|in:Начальный,Средний,Выше среднего,Продвинутый,В совершенстве',
            'computer_skills' => 'required|string',
            'work_experience' => 'nullable|array|min:0',
            'work_experience.*.years' => 'required|string|max:255',
            'work_experience.*.company' => 'required|string|max:255',
            'work_experience.*.city' => 'required|string|max:255',
            'work_experience.*.position' => 'required|string|max:255',
            'total_experience_years' => 'required|integer|min:0',
            'job_satisfaction' => 'required|integer|min:1|max:5',
            'desired_position' => ['required', 'string', 'max:255'],
            'expected_salary' => 'required|numeric|min:0|max:999999999999',
            'employer_requirements' => ['required', 'string', 'max:2000', new CyrillicRule()],

            // Step 4 validation rules
            'gallup_pdf' => [
                Rule::when($this->currentStep === 4, ['required', 'file', 'mimes:pdf', 'max:10240', function ($attribute, $value, $fail) {
                    if ($value && !is_string($value) && !$this->isGallupPdf($value)) {
                        $fail('Загруженный файл не является корректным отчетом Gallup.');
                    }
                }]),
                Rule::when($this->currentStep !== 4, ['nullable']),
            ],
            'mbti_type' => [
                Rule::when($this->currentStep === 4, ['required', 'string']),
                Rule::when($this->currentStep !== 4, ['nullable']),
            ],
        ];

        // Если мы на последнем шаге и gallup_pdf уже есть в базе, делаем его необязательным
        if ($this->currentStep === 4 && $this->candidate && $this->candidate->gallup_pdf) {
            $rules['gallup_pdf'] = 'nullable|file|mimes:pdf|max:10240';
        }

        return $rules;
    }

    protected $messages = [
        'last_name.required' => 'Фамилия обязательна для заполнения',
        'last_name.max' => 'Фамилия не должна превышать 255 символов',
        'first_name.required' => 'Имя обязательно для заполнения',
        'first_name.max' => 'Имя не должно превышать 255 символов',
        'middle_name.max' => 'Отчество не должно превышать 255 символов',
        'birth_place.required' => 'Место рождения обязательно для заполнения',
        'current_city.required' => 'Введите текущий город',
        'email.required' => 'Email обязателен для заполнения',
        'email.email' => 'Введите корректный email адрес',
        'phone.required' => 'Телефон обязателен для заполнения',
        'phone.regex' => 'Введите корректный номер телефона (разрешены +, цифры, пробелы, -, ())',
        'gender.required' => 'Выберите пол',
        'marital_status.required' => 'Выберите семейное положение',
        'birth_date.required' => 'Дата рождения обязательна для заполнения',
        'birth_date.before' => 'Дата рождения должна быть раньше текущей даты',
        'photo.required' => 'Фото обязательно для загрузки',
        'photo.image' => 'Загружаемый файл должен быть изображением (jpg, jpeg, png)',
        'photo.max' => 'Размер изображения не должен превышать 20MB',
        'gallup_pdf.required' => 'Необходимо загрузить результаты теста Gallup',
        'gallup_pdf.file' => 'Необходимо загрузить файл',
        'gallup_pdf.mimes' => 'Файл должен быть в формате PDF',
        'gallup_pdf.max' => 'Размер файла не должен превышать 10MB',
        'mbti_type.required' => 'Необходимо выбрать тип личности MBTI',
        'mbti_type.in' => 'Выбран некорректный тип личности MBTI',
        'expected_salary.required' => 'Ожидаемая зарплата обязательна для заполнения',
        'expected_salary.numeric' => 'Ожидаемая зарплата должна быть числом',
        'expected_salary.min' => 'Ожидаемая зарплата должна быть больше 0',
        'expected_salary.max' => 'Ожидаемая зарплата не может превышать 999,999,999,999 тенге',
        'desired_position.required' => 'Желаемая должность обязательна для заполнения',
        'desired_position.max' => 'Желаемая должность не должна превышать 255 символов',

        // Сообщения для CyrillicRule
        'hobbies.cyrillic' => 'Поле "Хобби" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'interests.cyrillic' => 'Поле "Интересы" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'favorite_sports.cyrillic' => 'Поле "Любимые виды спорта" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'school.cyrillic' => 'Поле "Школа" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'desired_position.cyrillic' => 'Поле "Желаемая должность" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'employer_requirements.cyrillic' => 'Поле "Требования к работодателю" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',
        'family_members.*.profession.cyrillic' => 'Поле "Профессия" должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания',

        // Дополнительные сообщения для обязательных полей
        'hobbies.required' => 'Хобби обязательно для заполнения',
        'interests.required' => 'Интересы обязательны для заполнения',
        'favorite_sports.required' => 'Любимые виды спорта обязательны для заполнения',
        'books_per_year_min.required' => 'Минимальное количество книг в год обязательно для заполнения',
        'books_per_year_max.required' => 'Максимальное количество книг в год обязательно для заполнения',
        'books_per_year_max.gte' => 'Максимальное количество книг не может быть меньше минимального',
        'is_practicing.required' => 'Укажите, являетесь ли вы практикующим',
        'visited_countries.required' => 'Добавьте хотя бы одну страну',
        'visited_countries.min' => 'Добавьте хотя бы одну страну',
        'visited_countries.*.required' => 'Выберите страну из списка',
        'visited_countries.*.in' => 'Выберите страну из списка',
        'family_members.required' => 'Добавьте минимум одного члена семьи',
        'family_members.min' => 'Добавьте минимум одного члена семьи',
        'computer_skills.required' => 'Укажите компьютерные навыки',
        'universities.required' => 'Добавьте минимум один университет',
        'language_skills.required' => 'Добавьте минимум один язык',
        'work_experience.required' => 'Добавьте минимум одно место работы',
        'job_satisfaction.required' => 'Укажите уровень удовлетворенности работой',
        'employer_requirements.required' => 'Укажите требования к работодателю',
    ];

    protected $validationAttributes = [
        // Шаг 1
        'last_name' => 'Фамилия',
        'first_name' => 'Имя',
        'middle_name' => 'Отчество',
        'email' => 'Email',
        'phone' => 'Телефон',
        'gender' => 'Пол',
        'marital_status' => 'Семейное положение',
        'birth_date' => 'Дата рождения',
        'birth_place' => 'Место рождения',
        'current_city' => 'Текущий город',
        'photo' => 'Фото',

        // Шаг 2
        'has_driving_license' => 'Водительские права',
        'religion' => 'Религия',
        'is_practicing' => 'Практикующий',
        'family_members' => 'Члены семьи',
        'family_members.*.type' => 'Тип родства',
        'family_members.*.birth_year' => 'Год рождения',
        'family_members.*.profession' => 'Профессия',
        
        // Новые атрибуты для категорий семьи
        'parents' => 'Родители',
        'parents.*.relation' => 'Родство',
        'parents.*.birth_year' => 'Год рождения',
        'parents.*.profession' => 'Профессия',
        
        'siblings' => 'Братья и сестры',
        'siblings.*.relation' => 'Родство',
        'siblings.*.birth_year' => 'Год рождения',
        
        'children' => 'Дети',
        'children.*.name' => 'Имя ребенка',
        'children.*.birth_year' => 'Год рождения',
        'hobbies' => 'Хобби',
        'interests' => 'Интересы',
        'visited_countries' => 'Посещенные страны',
        'visited_countries.*' => 'Страна',
        'books_per_year_min' => 'Минимальное количество книг в год',
        'books_per_year_max' => 'Максимальное количество книг в год',
        'favorite_sports' => 'Любимые виды спорта',
        'entertainment_hours_weekly' => 'Часы развлекательных видео в неделю',
        'educational_hours_weekly' => 'Часы образовательных видео в неделю',
        'social_media_hours_weekly' => 'Часы соцсетей в неделю',

        // Шаг 3
        'school' => 'Школа',
        'universities' => 'Университеты',
        'universities.*.name' => 'Название университета',
        'universities.*.graduation_year' => 'Год окончания',
        'universities.*.speciality' => 'Специальность',
        'universities.*.gpa' => 'GPA',
        'language_skills' => 'Языковые навыки',
        'language_skills.*.language' => 'Язык',
        'language_skills.*.level' => 'Уровень',
        'computer_skills' => 'Компьютерные навыки',
        'work_experience' => 'Опыт работы',
        'work_experience.*.years' => 'Период',
        'work_experience.*.company' => 'Название компании',
        'work_experience.*.city' => 'Город',
        'work_experience.*.position' => 'Должность',
        'total_experience_years' => 'Общий стаж работы',
        'job_satisfaction' => 'Удовлетворенность работой',
        'desired_position' => 'Желаемая должность',
        'expected_salary' => 'Ожидаемая зарплата',
        'employer_requirements' => 'Требования к работодателю',

        // Шаг 4
        'gallup_pdf' => 'Gallup PDF',
        'mbti_type' => 'Тип личности MBTI',
    ];

    public function updated($propertyName)
    {
        // Проверяем, относится ли поле к текущему шагу
        if (!$this->isFieldInCurrentStep($propertyName)) {
            // Если поле не относится к текущему шагу, только очищаем его ошибки
            // но не прерываем выполнение - может быть это системное поле Livewire
            if ($this->getErrorBag()->has($propertyName)) {
                $this->resetErrorBag($propertyName);
            }

            // Если это не системное поле Livewire, прерываем выполнение
            if (!str_starts_with($propertyName, '_') && !in_array($propertyName, ['currentStep', 'totalSteps'])) {
                return;
            }
        }

        // Если обновляется поле университета
        if (strpos($propertyName, 'universities.') === 0) {
            $this->validateOnly($propertyName);
            return;
        }

        // Если обновляется поле члена семьи
        if (strpos($propertyName, 'family_members.') === 0) {
            $this->validateOnly($propertyName);
            return;
        }

        // Если обновляется поле новых категорий семьи - отключаем live валидацию
        if (strpos($propertyName, 'parents.') === 0 || 
            strpos($propertyName, 'siblings.') === 0 || 
            strpos($propertyName, 'children.') === 0) {
            // Просто сбрасываем ошибки для этого поля без валидации
            $this->resetErrorBag($propertyName);
            return;
        }

        // Если обновляется поле опыта работы
        if (strpos($propertyName, 'work_experience.') === 0) {
            // Извлекаем индекс из имени свойства
            if (preg_match('/work_experience\.(\d+)\./', $propertyName, $matches)) {
                $index = (int)$matches[1];
                // Проверяем, что элемент с таким индексом существует
                if (!isset($this->work_experience[$index])) {
                    logger()->warning('Attempted to access non-existent work experience index', [
                        'property' => $propertyName,
                        'index' => $index,
                        'work_experience_count' => count($this->work_experience)
                    ]);
                    return;
                }
                // Валидируем только если все обязательные поля заполнены
                $experience = $this->work_experience[$index];
                if (!empty($experience['years']) && !empty($experience['company']) &&
                    !empty($experience['city']) && !empty($experience['position'])) {
                    $this->validateOnly($propertyName);
                }
            }
            return;
        }

        // Если обновляется поле языка, проверяем только если оба поля заполнены
        if (strpos($propertyName, 'language_skills.') === 0) {
            // Валидируем языковые навыки без проверки списка языков
            $this->validateOnly($propertyName);
            return;
        }

        // Валидируем только поля текущего шага
        $rules = collect($this->rules())->filter(function ($rule, $field) {
            return $this->isFieldInCurrentStep($field);
        })->toArray();



        $this->validateOnly($propertyName, $rules);

        // Сохраняем изменение в историю
        if ($this->candidate) {
            $oldValue = $this->candidate->{$propertyName};
            $newValue = $this->{$propertyName};

            if ($oldValue !== $newValue) {
                CandidateHistory::create([
                    'candidate_id' => $this->candidate->id,
                    'field_name' => $propertyName,
                    'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
                    'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
                    'changed_by' => auth()->user()?->name ?? 'Guest',
                    'ip_address' => request()->ip()
                ]);
            }
        }
    }

    protected function isFieldInCurrentStep($field)
    {
        // Извлекаем основное поле из составного имени (например, "universities.0.name" -> "universities")
        $baseField = explode('.', $field)[0];

        $step1Fields = ['last_name', 'first_name', 'middle_name', 'email', 'phone', 'gender', 'marital_status', 'birth_date', 'birth_place', 'current_city', 'photo'];
        $step2Fields = ['religion', 'is_practicing', 'family_members', 'parents', 'siblings', 'children', 'hobbies', 'interests', 'visited_countries', 'books_per_year_min', 'books_per_year_max', 'favorite_sports', 'entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'has_driving_license', 'newCountry'];
        $step3Fields = ['school', 'universities', 'language_skills', 'computer_skills', 'work_experience', 'total_experience_years', 'job_satisfaction', 'desired_position', 'expected_salary', 'employer_requirements'];
        $step4Fields = ['gallup_pdf', 'mbti_type'];

        return match($this->currentStep) {
            1 => in_array($baseField, $step1Fields),
            2 => in_array($baseField, $step2Fields),
            3 => in_array($baseField, $step3Fields),
            4 => in_array($baseField, $step4Fields),
            default => false,
        };
    }

    public function nextStep()
    {
        try {
            logger()->debug('Starting nextStep method');
            logger()->debug('Current step: ' . $this->currentStep);

            // Фильтруем пустые элементы семьи перед валидацией
            $this->filterEmptyFamilyElements();

            $rules = $this->getStepRules();
            
            logger()->debug('Validation rules for step ' . $this->currentStep . ':', $rules);
            logger()->debug('Current family data:', [
                'parents' => $this->parents,
                'siblings' => $this->siblings, 
                'children' => $this->children
            ]);

            // Специальная обработка для фото на первом шаге
            if ($this->currentStep === 1) {
                // Если фото уже загружено в базу или есть предпросмотр, не требуем его
                if ($this->candidate?->photo || $this->photoPreview) {
                    unset($rules['photo']);
                }
            }

            logger()->debug('Validation rules:', $rules);

            $this->validate($rules);
            logger()->debug('Validation passed');

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
                logger()->debug('New step: ' . $this->currentStep);

                // Переинициализируем валидацию для нового шага
                logger()->debug('About to call reinitializeValidation()');
                $this->reinitializeValidation();
                logger()->debug('reinitializeValidation() completed');

                $this->saveProgress();
                logger()->debug('Progress saved');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->debug('Validation errors:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            logger()->error('Unexpected error in nextStep:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function previousStep()
    {
        try {
            logger()->debug('Starting previousStep method');
            logger()->debug('Current step: ' . $this->currentStep);

        if ($this->currentStep > 1) {
            $this->currentStep--;
                logger()->debug('New step: ' . $this->currentStep);

                // Переинициализируем валидацию для нового шага
                logger()->debug('About to call reinitializeValidation()');
                $this->reinitializeValidation();
                logger()->debug('reinitializeValidation() completed');

                $this->saveProgress();
                logger()->debug('Progress saved');
            }
        } catch (\Exception $e) {
            logger()->error('Error in previousStep:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function getStepRules()
    {
        $allRules = $this->rules();

        return match($this->currentStep) {
            1 => [
                'last_name' => $allRules['last_name'],
                'first_name' => $allRules['first_name'],
                'middle_name' => $allRules['middle_name'],
                'email' => $allRules['email'],
                'phone' => $allRules['phone'],
                'gender' => $allRules['gender'],
                'marital_status' => $allRules['marital_status'],
                'birth_date' => $allRules['birth_date'],
                'birth_place' => $allRules['birth_place'],
                'current_city' => $allRules['current_city'],
                'photo' => !$this->candidate?->photo ? 'required|image|max:20480' : 'nullable|image|max:20480',
            ],
            2 => [
                'religion' => $allRules['religion'],
                'is_practicing' => $allRules['is_practicing'],
                // Убираем валидацию старой структуры family_members
                
                // Добавляем новые правила валидации для категорий семьи
                'parents' => $allRules['parents'],
                'siblings' => $allRules['siblings'],
                'children' => $allRules['children'],
                
                'hobbies' => $allRules['hobbies'],
                'interests' => $allRules['interests'],
                'visited_countries' => $allRules['visited_countries'],
                'visited_countries.*' => $allRules['visited_countries.*'],
                'books_per_year_min' => $allRules['books_per_year_min'],
                'books_per_year_max' => $allRules['books_per_year_max'],
                'favorite_sports' => $allRules['favorite_sports'],
                'entertainment_hours_weekly' => $allRules['entertainment_hours_weekly'],
                'educational_hours_weekly' => $allRules['educational_hours_weekly'],
                'social_media_hours_weekly' => $allRules['social_media_hours_weekly'],
                'has_driving_license' => $allRules['has_driving_license'],
            ],
            3 => [
                'school' => $allRules['school'],
                'universities' => $allRules['universities'],
                'universities.*.name' => $allRules['universities.*.name'],
                'universities.*.graduation_year' => $allRules['universities.*.graduation_year'],
                'universities.*.speciality' => $allRules['universities.*.speciality'],
                'universities.*.gpa' => $allRules['universities.*.gpa'],
                'language_skills' => $allRules['language_skills'],
                'language_skills.*.language' => $allRules['language_skills.*.language'],
                'language_skills.*.level' => $allRules['language_skills.*.level'],
                'computer_skills' => $allRules['computer_skills'],
                'work_experience' => $allRules['work_experience'],
                'work_experience.*.years' => $allRules['work_experience.*.years'],
                'work_experience.*.company' => $allRules['work_experience.*.company'],
                'work_experience.*.city' => $allRules['work_experience.*.city'],
                'work_experience.*.position' => $allRules['work_experience.*.position'],
                'total_experience_years' => $allRules['total_experience_years'],
                'job_satisfaction' => $allRules['job_satisfaction'],
                'desired_position' => $allRules['desired_position'],
                'expected_salary' => $allRules['expected_salary'],
                'employer_requirements' => $allRules['employer_requirements'],
            ],
            4 => [
                'gallup_pdf' => [
                    $this->currentStep === 4 && $this->candidate && $this->candidate->gallup_pdf ? 'nullable' : 'required',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                    function ($attribute, $value, $fail) {
                        if ($value && !is_string($value) && !$this->isGallupPdf($value)) {
                            $fail('Загруженный файл не является корректным отчетом Gallup.');
                        }
                    }
                ],
                'mbti_type' => 'required|string|in:INTJ-A,INTJ-T,INTP-A,INTP-T,ENTJ-A,ENTJ-T,ENTP-A,ENTP-T,INFJ-A,INFJ-T,INFP-A,INFP-T,ENFJ-A,ENFJ-T,ENFP-A,ENFP-T,ISTJ-A,ISTJ-T,ISFJ-A,ISFJ-T,ESTJ-A,ESTJ-T,ESFJ-A,ESFJ-T,ISTP-A,ISTP-T,ISFP-A,ISFP-T,ESTP-A,ESTP-T,ESFP-A,ESFP-T',
            ],
            default => [],
        };
    }

    /**
     * Возвращает номер шага для указанного поля
     */
    private function getFieldStep(string $field): ?int
    {
        $baseField = explode('.', $field)[0];

        $step1Fields = ['last_name', 'first_name', 'middle_name', 'email', 'phone', 'gender', 'marital_status', 'birth_date', 'birth_place', 'current_city', 'photo'];
        $step2Fields = ['religion', 'is_practicing', 'family_members', 'parents', 'siblings', 'children', 'hobbies', 'interests', 'visited_countries', 'books_per_year_min', 'books_per_year_max', 'favorite_sports', 'entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'has_driving_license', 'newCountry'];
        $step3Fields = ['school', 'universities', 'language_skills', 'computer_skills', 'work_experience', 'total_experience_years', 'job_satisfaction', 'desired_position', 'expected_salary', 'employer_requirements'];
        $step4Fields = ['gallup_pdf', 'mbti_type'];

        if (in_array($baseField, $step1Fields, true)) return 1;
        if (in_array($baseField, $step2Fields, true)) return 2;
        if (in_array($baseField, $step3Fields, true)) return 3;
        if (in_array($baseField, $step4Fields, true)) return 4;
        return null;
    }

    // Dynamic field methods
    public function addFamilyMember()
    {
        $this->family_members[] = [
            'type' => '',
            'birth_year' => '',
            'profession' => ''
        ];
    }

    // Новые методы для работы с категориями семьи
    public function addParent()
    {
        logger()->debug('addParent called', [
            'current_parents_count' => count($this->parents),
            'parents_before' => $this->parents
        ]);
        
        $this->parents[] = [
            'relation' => '',
            'birth_year' => '',
            'profession' => ''
        ];
        
        logger()->debug('addParent completed', [
            'new_parents_count' => count($this->parents),
            'parents_after' => $this->parents
        ]);
    }

    public function removeParent($index)
    {
        logger()->debug('removeParent called', [
            'index' => $index,
            'parents_before' => $this->parents,
            'count_before' => count($this->parents)
        ]);
        
        unset($this->parents[$index]);
        $this->parents = array_values($this->parents);
        
        logger()->debug('removeParent completed', [
            'parents_after' => $this->parents,
            'count_after' => count($this->parents)
        ]);
    }

    public function addSibling()
    {
        $this->siblings[] = [
            'relation' => '',
            'birth_year' => ''
        ];
    }

    public function removeSibling($index)
    {
        unset($this->siblings[$index]);
        $this->siblings = array_values($this->siblings);
    }

    public function addChild()
    {
        $this->children[] = [
            'name' => '',
            'birth_year' => ''
        ];
    }

    public function removeChild($index)
    {
        unset($this->children[$index]);
        $this->children = array_values($this->children);
    }

    /**
     * Фильтрует пустые элементы из массивов семьи перед валидацией
     */
    private function filterEmptyFamilyElements()
    {
        logger()->debug('FILTER START - Original data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children,
            'family_members' => $this->family_members
        ]);

        // ВАЖНО: Очищаем старую структуру family_members чтобы избежать конфликтов валидации
        $this->family_members = [];

        // Фильтруем родителей - удаляем полностью пустые записи
        if (is_array($this->parents)) {
            $originalCount = count($this->parents);
            $this->parents = array_filter($this->parents, function($parent) {
                $hasData = !empty($parent['relation']) || !empty($parent['birth_year']) || !empty($parent['profession']);
                logger()->debug('Parent filter check:', ['parent' => $parent, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->parents = array_values($this->parents); // Переиндексируем
            logger()->debug('Parents filtered: ' . $originalCount . ' -> ' . count($this->parents));
        }

        // Фильтруем братьев и сестер
        if (is_array($this->siblings)) {
            $originalCount = count($this->siblings);
            $this->siblings = array_filter($this->siblings, function($sibling) {
                $hasData = !empty($sibling['relation']) || !empty($sibling['birth_year']);
                logger()->debug('Sibling filter check:', ['sibling' => $sibling, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->siblings = array_values($this->siblings);
            logger()->debug('Siblings filtered: ' . $originalCount . ' -> ' . count($this->siblings));
        }

        // Фильтруем детей
        if (is_array($this->children)) {
            $originalCount = count($this->children);
            $this->children = array_filter($this->children, function($child) {
                $hasData = !empty($child['name']) || !empty($child['birth_year']);
                logger()->debug('Child filter check:', ['child' => $child, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->children = array_values($this->children);
            logger()->debug('Children filtered: ' . $originalCount . ' -> ' . count($this->children));
        }

        logger()->debug('FILTER END - Filtered data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children,
            'family_members' => $this->family_members
        ]);
    }



    public function removeFamilyMember($index)
    {
        unset($this->family_members[$index]);
        $this->family_members = array_values($this->family_members);
    }

    /**
     * Загрузка категорий семьи из старых данных или инициализация новых структур
     */
    private function loadFamilyCategories()
    {
        // Проверяем, есть ли новые поля в JSON структуре
        $familyData = $this->candidate->family_members ?? [];
        
        if (is_array($familyData) && isset($familyData['parents'])) {
            // Новая структура - загружаем как есть
            $this->parents = $familyData['parents'] ?? [];
            $this->siblings = $familyData['siblings'] ?? [];
            $this->children = $familyData['children'] ?? [];
        } else {
            // Старая структура - мигрируем данные
            $this->parents = [];
            $this->siblings = [];
            $this->children = [];
            
            foreach ($familyData as $member) {
                if (!is_array($member)) continue;
                
                $type = $member['type'] ?? '';
                switch ($type) {
                    case 'Отец':
                    case 'Мать':
                        $this->parents[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                            'profession' => $member['profession'] ?? ''
                        ];
                        break;
                    case 'Брат':
                    case 'Сестра':
                        $this->siblings[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                        ];
                        break;
                    case 'Сын':
                    case 'Дочь':
                        $this->children[] = [
                            'name' => $member['profession'] ?? '', // В старой структуре имя было в поле profession
                            'birth_year' => $member['birth_year'] ?? '',
                        ];
                        break;
                }
            }
        }
    }

    /**
     * Строит новую структуру JSON для сохранения в БД
     */
    private function buildFamilyStructure()
    {
        return [
            'parents' => $this->parents ?? [],
            'siblings' => $this->siblings ?? [],
            'children' => $this->children ?? []
        ];
    }

    /**
     * Переопределяем метод validate для фильтрации семьи перед валидацией
     */
    public function validate($rules = null, $messages = [], $attributes = [])
    {
        // Фильтруем семью перед любой валидацией
        $this->filterEmptyFamilyElements();
        
        // Добавляем кастомную валидацию для семьи
        $this->validateFamilyData();
        
        // Вызываем родительский метод с оригинальными правилами
        return parent::validate($rules, $messages, $attributes);
    }

    /**
     * Модифицирует правила валидации для семьи, делая их обязательными только для заполненных элементов
     */
    private function modifyFamilyValidationRules($rules)
    {
        // Создаем новые правила для каждого заполненного элемента
        $modifiedRules = $rules;
        
        // Удаляем старые правила для массивов семьи
        unset($modifiedRules['parents.*.relation']);
        unset($modifiedRules['parents.*.birth_year']);
        unset($modifiedRules['parents.*.profession']);
        unset($modifiedRules['siblings.*.relation']);
        unset($modifiedRules['siblings.*.birth_year']);
        unset($modifiedRules['children.*.name']);
        unset($modifiedRules['children.*.birth_year']);
        
        // Добавляем правила для каждого конкретного элемента
        foreach ($this->parents as $index => $parent) {
            $modifiedRules["parents.{$index}.relation"] = 'required|string|in:Отец,Мать';
            $modifiedRules["parents.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
            $modifiedRules["parents.{$index}.profession"] = 'required|string|max:255';
        }
        
        foreach ($this->siblings as $index => $sibling) {
            $modifiedRules["siblings.{$index}.relation"] = 'required|string|in:Брат,Сестра';
            $modifiedRules["siblings.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
        }
        
        foreach ($this->children as $index => $child) {
            $modifiedRules["children.{$index}.name"] = 'required|string|max:255';
            $modifiedRules["children.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
        }
        
        return $modifiedRules;
    }

    /**
     * Кастомная валидация данных семьи
     */
    private function validateFamilyData()
    {
        logger()->debug('CUSTOM VALIDATION - Family data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children
        ]);

        $errors = [];

        // Валидируем родителей
        foreach ($this->parents as $index => $parent) {
            if (empty($parent['relation'])) {
                $errors["parents.{$index}.relation"] = 'Поле Родство обязательно.';
            } elseif (!in_array($parent['relation'], ['Отец', 'Мать'])) {
                $errors["parents.{$index}.relation"] = 'Выберите корректное родство.';
            }

            if (empty($parent['birth_year'])) {
                $errors["parents.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($parent['birth_year']) || $parent['birth_year'] < 1900 || $parent['birth_year'] > date('Y')) {
                $errors["parents.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }

            if (empty($parent['profession'])) {
                $errors["parents.{$index}.profession"] = 'Поле Профессия обязательно.';
            }
        }

        // Валидируем братьев и сестер
        foreach ($this->siblings as $index => $sibling) {
            if (empty($sibling['relation'])) {
                $errors["siblings.{$index}.relation"] = 'Поле Родство обязательно.';
            } elseif (!in_array($sibling['relation'], ['Брат', 'Сестра'])) {
                $errors["siblings.{$index}.relation"] = 'Выберите корректное родство.';
            }

            if (empty($sibling['birth_year'])) {
                $errors["siblings.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($sibling['birth_year']) || $sibling['birth_year'] < 1900 || $sibling['birth_year'] > date('Y')) {
                $errors["siblings.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }
        }

        // Валидируем детей
        foreach ($this->children as $index => $child) {
            if (empty($child['name'])) {
                $errors["children.{$index}.name"] = 'Поле Имя ребенка обязательно.';
            }

            if (empty($child['birth_year'])) {
                $errors["children.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($child['birth_year']) || $child['birth_year'] < 1900 || $child['birth_year'] > date('Y')) {
                $errors["children.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }
        }

        // Если есть ошибки, выбрасываем исключение валидации
        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    /**
     * Переопределяем метод validateOnly для фильтрации семьи перед валидацией
     */
    public function validateOnly($field, $rules = null, $messages = [], $attributes = [], $dataOverrides = [])
    {
        // Если валидируем поля семьи, сначала фильтруем
        if (strpos($field, 'parents.') === 0 || 
            strpos($field, 'siblings.') === 0 || 
            strpos($field, 'children.') === 0) {
            $this->filterEmptyFamilyElements();
            
            // Если правила не переданы, получаем правила и модифицируем их
            if ($rules === null) {
                $allRules = $this->rules();
                $rules = $this->modifyFamilyValidationRules($allRules);
            }
        }
        
        // Вызываем родительский метод
        return parent::validateOnly($field, $rules, $messages, $attributes, $dataOverrides);
    }

    public function updatedNewCountry($value)
    {
        try {
            logger()->debug('Updating new country:', [
                'value' => $value,
                'countries_count' => count($this->countries),
                'first_country' => $this->countries[0] ?? null
            ]);

            if ($value) {
                $country = collect($this->countries)->firstWhere('name_ru', $value);
                logger()->debug('Found country:', [
                    'country' => $country,
                    'has_flag_url' => isset($country['flag_url']),
                    'flag_url_value' => $country['flag_url'] ?? null
                ]);

                if ($country && !in_array($value, $this->visited_countries)) {
                    $this->visited_countries[] = $value;
                    $this->newCountry = '';
                    logger()->debug('Added country to visited_countries:', [
                        'visited_countries' => $this->visited_countries,
                        'last_added' => end($this->visited_countries)
                    ]);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Error in updatedNewCountry:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function removeCountry($index)
    {
        unset($this->visited_countries[$index]);
        $this->visited_countries = array_values($this->visited_countries);
    }



    public function addUniversity()
    {
        $this->universities = collect($this->universities)->toArray();
        $this->universities[] = [
            'name' => '',
            'graduation_year' => '',
            'speciality' => '',
            'gpa' => ''
        ];
    }

    public function removeUniversity($index)
    {
        $universities = collect($this->universities)->toArray();
        unset($universities[$index]);
        $this->universities = array_values($universities);
    }

    public function addLanguage()
    {
        // Обеспечиваем корректную инициализацию языков
        if (empty($this->languages)) {
            $this->loadLanguages();
        }

        // Добавляем новый язык с безопасными значениями по умолчанию
        $firstLanguage = !empty($this->languages) ? $this->languages[0] : 'Русский';

        $this->language_skills[] = [
            'language' => $firstLanguage,
            'level' => 'Начальный'
        ];
    }

    private function loadLanguages()
    {
        try {
            $jsonPath = base_path('resources/json/languages.json');
            if (!file_exists($jsonPath)) {
                throw new \Exception('Languages JSON file not found');
            }

            $languagesData = json_decode(file_get_contents($jsonPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in languages file: ' . json_last_error_msg());
            }

            $this->languages = [];
            if (isset($languagesData['languages']) && is_array($languagesData['languages'])) {
                foreach ($languagesData['languages'] as $language) {
                    if (isset($language['name_ru']) && !empty($language['name_ru'])) {
                        $this->languages[] = $language['name_ru'];
                    }
                }
            }

            // Если массив языков пустой, используем fallback
            if (empty($this->languages)) {
                $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский'];
            }

        } catch (\Exception $e) {
            logger()->error('Error loading languages: ' . $e->getMessage());
            // Fallback к базовым языкам
            $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский'];
        }
    }



    public function removeLanguage($index)
    {
        unset($this->language_skills[$index]);
        $this->language_skills = array_values($this->language_skills);
    }



    public function addWorkExperience()
    {
        $this->work_experience[] = [
            'years' => '',
            'company' => '',
            'city' => '',
            'position' => ''
        ];
    }

    public function removeWorkExperience($index)
    {
        unset($this->work_experience[$index]);
        $this->work_experience = array_values($this->work_experience);
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:20480' // 20MB
        ]);

        if ($this->photo) {
            try {
                // Принудительно сохраняем фото в постоянное место хранения
                $this->savePhotoImmediately();

                // Отправляем событие в браузер
                $this->dispatch('photoUploaded');

            } catch (\Exception $e) {
                // Обработка ошибок
                $this->addError('photo', 'Ошибка при обработке фото: ' . $e->getMessage());
                $this->dispatch('photo-error', ['message' => 'Ошибка при загрузке фото']);
            }
        }
    }

    public function savePhotoImmediately()
    {
        if (!$this->photo || is_string($this->photo)) {
            return; // Фото уже сохранено или отсутствует
        }

        try {
            // Создаем или обновляем кандидата если его нет
            if (!$this->candidate) {
                $this->candidate = new Candidate();
                $this->candidate->user_id = auth()->id();
                $this->candidate->step = $this->currentStep;
                // Сохраняем базовую информацию если есть, иначе оставляем null
                if ($this->last_name || $this->first_name) {
                    $this->candidate->full_name = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
                } else {
                    $this->candidate->full_name = null; // Явно устанавливаем null
                }
                if ($this->email) $this->candidate->email = $this->email;
            }

            // Удаляем старое фото если есть
            if ($this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }

            // Сохраняем новое фото
            $photoPath = $this->photo->store('photos', 'public');
            $this->candidate->photo = $photoPath;
            $this->candidate->save();

            // Обновляем предпросмотр на постоянный URL
            $this->photoPreview = Storage::disk('public')->url($photoPath);

            // Устанавливаем фото как строку, чтобы указать что оно уже сохранено
            $this->photo = $photoPath;

            logger()->info('Photo saved immediately', ['path' => $photoPath, 'candidate_id' => $this->candidate->id]);

        } catch (\Exception $e) {
            logger()->error('Error saving photo immediately: ' . $e->getMessage());
            throw $e;
        }
    }

    public function removePhoto()
    {
        try {
            // Удаляем фото из storage если оно есть
            if ($this->candidate && $this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }

            // Очищаем свойство фото
            $this->photo = null;
            $this->photoPreview = null;

            // Обновляем базу данных если кандидат существует
            if ($this->candidate) {
                $this->candidate->photo = null;
                $this->candidate->save();
            }

            session()->flash('message', 'Фото удалено');
        } catch (\Exception $e) {
            logger()->error('Error removing photo: ' . $e->getMessage());
            session()->flash('error', 'Ошибка при удалении фото');
        }
    }

    public function updatedGallupPdf()
    {
        logger()->info('Gallup PDF upload started', [
            'file_present' => $this->gallup_pdf ? 'yes' : 'no',
            'file_type' => $this->gallup_pdf ? get_class($this->gallup_pdf) : 'null'
        ]);

        if ($this->gallup_pdf) {
            try {
                // Логируем информацию о файле
                logger()->info('Gallup PDF file info', [
                    'original_name' => $this->gallup_pdf->getClientOriginalName(),
                    'size' => $this->gallup_pdf->getSize(),
                    'mime_type' => $this->gallup_pdf->getMimeType(),
                ]);

                // Базовая валидация файла
                $this->validate([
                    'gallup_pdf' => 'file|mimes:pdf|max:10240'
                ]);

                logger()->info('Gallup PDF passed basic validation');

                // Проверяем, что это корректный Gallup PDF
                if (!$this->isGallupPdf($this->gallup_pdf)) {
                    logger()->warning('Gallup PDF failed content validation');
                    $this->addError('gallup_pdf', 'Загруженный файл не является корректным отчетом Gallup. Убедитесь, что это официальный PDF с результатами теста Gallup.');
                    $this->resetGallupFile();
                    return;
                }

                logger()->info('Gallup PDF validation successful');

                // Отправляем событие в JavaScript
                $this->dispatch('gallup-file-uploaded');

                session()->flash('message', 'PDF файл загружен и проверен');
            } catch (\Exception $e) {
                logger()->error('Error processing Gallup PDF', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->addError('gallup_pdf', 'Ошибка при обработке файла: ' . $e->getMessage());
                $this->resetGallupFile();
            }
        }
    }

    /**
     * Сбрасывает состояние gallup_pdf файла
     */
    private function resetGallupFile()
    {
        $this->gallup_pdf = null;
        $this->dispatch('gallup-file-reset'); // Отправляем событие в JavaScript для сброса UI
    }

    public function submit()
    {
        try {
            logger()->debug('Starting submit method');
            logger()->debug('Current step: ' . $this->currentStep);
            logger()->debug('Gallup PDF: ', ['gallup_pdf' => $this->gallup_pdf ? 'present' : 'null', 'candidate_gallup' => $this->candidate?->gallup_pdf]);
            logger()->debug('MBTI type: ' . $this->mbti_type);

            // Фильтруем пустые элементы семьи перед валидацией
            $this->filterEmptyFamilyElements();

            // Создаем специальные правила для финального submit
            $rules = $this->rules();

            // Если фото уже сохранено (строка) и не загружается новое, исключаем из валидации
            if ($this->candidate && $this->candidate->photo && is_string($this->photo)) {
                unset($rules['photo']);
                logger()->debug('Photo validation removed (existing file)');
            } else if ($this->candidate && $this->candidate->photo) {
                // Если есть сохраненное фото, но загружается новое
                $rules['photo'] = ['nullable', 'image', 'max:20480'];
                logger()->debug('Photo rule modified to nullable (candidate has existing photo)');
            }

            // Если Gallup PDF уже сохранен (строка) и не загружается новый, исключаем из валидации
            if ($this->candidate && $this->candidate->gallup_pdf && is_string($this->gallup_pdf)) {
                unset($rules['gallup_pdf']);
                logger()->debug('Gallup PDF validation removed (existing file)');
            } else if ($this->candidate && $this->candidate->gallup_pdf) {
                // Если есть сохраненный файл, но загружается новый
                $rules['gallup_pdf'] = [
                    'nullable',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                    function ($attribute, $value, $fail) {
                        if ($value && !is_string($value) && !$this->isGallupPdf($value)) {
                            $fail('Загруженный файл не является корректным отчетом Gallup.');
                        }
                    }
                ];
                logger()->debug('Gallup PDF rule modified to nullable with validation (file exists in DB)');
            }

            logger()->debug('Validation rules for submit:', ['photo' => $rules['photo'] ?? 'not set', 'gallup_pdf' => $rules['gallup_pdf'] ?? 'not set', 'mbti_type' => $rules['mbti_type'] ?? 'not set']);

            // Отладка значения религии
            logger()->debug('Religion debug:', [
                'current_religion_value' => $this->religion,
                'allowed_religions' => array_values(config('lists.religions')),
                'religion_validation_rule' => $rules['religion'] ?? 'not set'
            ]);

            $this->validate($rules);
            logger()->debug('Validation passed');

            if (!$this->candidate) {
                $this->candidate = new Candidate();
                $this->candidate->user_id = auth()->id();
            } else {
                // Проверяем права доступа при финальном сохранении
                if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                    abort(403, 'У вас нет прав для изменения этой анкеты.');
                }
            }

            // Basic Information
            // Объединяем ФИО
            $this->candidate->full_name = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
            $this->candidate->email = $this->email;
            $this->candidate->phone = $this->phone;
            $this->candidate->gender = $this->gender;
            $this->candidate->marital_status = $this->marital_status;
            $this->candidate->birth_date = $this->birth_date;
            $this->candidate->birth_place = $this->birth_place;
            $this->candidate->current_city = $this->current_city;
            $this->candidate->step = 5; // Устанавливаем финальный шаг

            // Handle photo upload
            if ($this->photo && !is_string($this->photo)) {
                if ($this->candidate->photo) {
                    Storage::disk('public')->delete($this->candidate->photo);
                }
                $photoPath = $this->photo->store('photos', 'public');
                $this->candidate->photo = $photoPath;
            }

            // Additional Information
            $this->candidate->religion = $this->religion;
            $this->candidate->is_practicing = $this->is_practicing;
            
            // Сохраняем новую структуру семьи в JSON
            $this->candidate->family_members = $this->buildFamilyStructure();
            
            $this->candidate->hobbies = $this->hobbies;
            $this->candidate->interests = $this->interests;
            $this->candidate->visited_countries = $this->visited_countries;
            $this->candidate->books_per_year = $this->books_per_year;
            $this->candidate->favorite_sports = $this->favorite_sports;
            $this->candidate->entertainment_hours_weekly = $this->entertainment_hours_weekly;
            $this->candidate->educational_hours_weekly = $this->educational_hours_weekly;
            $this->candidate->social_media_hours_weekly = $this->social_media_hours_weekly;
            $this->candidate->has_driving_license = $this->has_driving_license;

            // Education and Work
            $this->candidate->school = $this->school;
            $this->candidate->universities = $this->universities;

            // Фильтруем пустые языковые навыки
            $this->candidate->language_skills = array_filter($this->language_skills, function($skill) {
                return !empty($skill['language']) && !empty($skill['level']);
            });

            $this->candidate->computer_skills = $this->computer_skills;

            // Фильтруем пустые записи опыта работы
            $this->candidate->work_experience = array_filter($this->work_experience, function($experience) {
                return !empty($experience['years']) || !empty($experience['company']) || !empty($experience['city']) || !empty($experience['position']);
            });
            $this->candidate->total_experience_years = $this->total_experience_years;
            $this->candidate->job_satisfaction = $this->job_satisfaction;
            $this->candidate->desired_position = $this->desired_position;
            $this->candidate->expected_salary = $this->expected_salary;
            $this->candidate->employer_requirements = $this->employer_requirements;

            // Handle Gallup PDF upload
            if ($this->gallup_pdf && !is_string($this->gallup_pdf)) {
                if ($this->candidate->gallup_pdf) {
                    Storage::disk('public')->delete($this->candidate->gallup_pdf);
                }
                $gallupPath = $this->gallup_pdf->store('gallup', 'public');
                $this->candidate->gallup_pdf = $gallupPath;
            }

            // Save MBTI type
            $this->candidate->mbti_type = $this->mbti_type;

            $this->candidate->save();

            // Создаем запись в истории о завершении анкеты
            CandidateHistory::create([
                'candidate_id' => $this->candidate->id,
                'field_name' => 'status',
                'old_value' => 'in_progress',
                'new_value' => 'completed',
                'changed_by' => auth()->user()?->name ?? 'Guest',
                'ip_address' => request()->ip()
            ]);

            // Запускаем обработку Gallup файла в фоновом режиме
            if ($this->candidate->gallup_pdf) {
                ProcessGallupFile::dispatch($this->candidate);
                logger()->info('Gallup file processing job dispatched', [
                    'candidate_id' => $this->candidate->id,
                    'gallup_pdf' => $this->candidate->gallup_pdf
                ]);
            }

            session()->flash('message', 'Анкета успешно сохранена!');

            // Определяем, куда перенаправить пользователя
            if (auth()->user()->is_admin) {
                // Администратор возвращается в админ-панель
                return redirect()->to('/admin/candidates');
            } else {
                // Обычный пользователь попадает на дашборд
                return redirect()->route('dashboard');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->debug('Validation errors in submit:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            logger()->error('Unexpected error in submit:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function saveProgress()
    {
        // Если это новая запись, создаем новую модель
        if (!$this->candidate) {
            $this->candidate = new Candidate();
            $this->candidate->user_id = auth()->id();
        } else {
            // Проверяем права доступа при сохранении существующей анкеты
            if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для изменения этой анкеты.');
            }
        }

        // Базовые данные всегда сохраняем, если они есть
        if ($this->last_name || $this->first_name) {
            $this->candidate->full_name = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
        } else {
            $this->candidate->full_name = null; // Явно устанавливаем null если имя не введено
        }
        if ($this->email) $this->candidate->email = $this->email;
        if ($this->phone) $this->candidate->phone = $this->phone;
        if ($this->gender) $this->candidate->gender = $this->gender;
        if ($this->marital_status) $this->candidate->marital_status = $this->marital_status;
        if ($this->birth_date) $this->candidate->birth_date = $this->birth_date;
        if ($this->birth_place) $this->candidate->birth_place = $this->birth_place;
        if ($this->current_city) $this->candidate->current_city = $this->current_city;

        // Фото обрабатываем отдельно
        if ($this->photo && !is_string($this->photo)) {
            if ($this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }
            $photoPath = $this->photo->store('photos', 'public');
            $this->candidate->photo = $photoPath;
        }

        // Дополнительная информация
        if ($this->religion !== null) $this->candidate->religion = $this->religion;
        if ($this->is_practicing !== null) $this->candidate->is_practicing = $this->is_practicing;

        // Сохраняем новую структуру семьи 
        $familyStructure = $this->buildFamilyStructure();
        if (!empty($familyStructure['parents']) || 
            !empty($familyStructure['siblings']) || 
            !empty($familyStructure['children'])) {
            $this->candidate->family_members = $familyStructure;
        } else {
            // Если используем старую структуру
            if (!empty($this->family_members)) {
                $validMembers = array_filter($this->family_members, function($member) {
                    return !empty($member['type']) &&
                           !empty($member['birth_year']) &&
                           !empty($member['profession']);
                });
                if (!empty($validMembers)) {
                    $this->candidate->family_members = array_values($validMembers);
                }
            }
        }

        if ($this->hobbies !== null) $this->candidate->hobbies = $this->hobbies;
        if ($this->interests !== null) $this->candidate->interests = $this->interests;
        if (!empty($this->visited_countries)) $this->candidate->visited_countries = $this->visited_countries;
        if ($this->books_per_year !== null) $this->candidate->books_per_year = $this->books_per_year;
        if (!empty($this->favorite_sports)) $this->candidate->favorite_sports = $this->favorite_sports;
        if ($this->entertainment_hours_weekly !== null) $this->candidate->entertainment_hours_weekly = $this->entertainment_hours_weekly;
        if ($this->educational_hours_weekly !== null) $this->candidate->educational_hours_weekly = $this->educational_hours_weekly;
        if ($this->social_media_hours_weekly !== null) $this->candidate->social_media_hours_weekly = $this->social_media_hours_weekly;
        if ($this->has_driving_license !== null) $this->candidate->has_driving_license = $this->has_driving_license;

        // Образование и работа
        if ($this->school) $this->candidate->school = $this->school;
        if (!empty($this->universities)) $this->candidate->universities = $this->universities;

        // Фильтруем пустые языковые навыки
        if (!empty($this->language_skills)) {
            $filteredLanguageSkills = array_filter($this->language_skills, function($skill) {
                return !empty($skill['language']) && !empty($skill['level']);
            });
            $this->candidate->language_skills = array_values($filteredLanguageSkills);
        }

        if ($this->computer_skills !== null) $this->candidate->computer_skills = $this->computer_skills;

        // Фильтруем пустые записи опыта работы
        if (!empty($this->work_experience)) {
            $filteredWorkExperience = array_filter($this->work_experience, function($experience) {
                return !empty($experience['years']) || !empty($experience['company']) || !empty($experience['city']) || !empty($experience['position']);
            });
            $this->candidate->work_experience = array_values($filteredWorkExperience);
        }
        if ($this->total_experience_years !== null) $this->candidate->total_experience_years = $this->total_experience_years;
        if ($this->job_satisfaction !== null) $this->candidate->job_satisfaction = $this->job_satisfaction;
        if ($this->desired_position) $this->candidate->desired_position = $this->desired_position;
        if ($this->expected_salary !== null) $this->candidate->expected_salary = $this->expected_salary;
        if ($this->employer_requirements !== null) $this->candidate->employer_requirements = $this->employer_requirements;

        // Handle Gallup PDF upload
        if ($this->gallup_pdf && !is_string($this->gallup_pdf)) {
            if ($this->candidate->gallup_pdf) {
                Storage::disk('public')->delete($this->candidate->gallup_pdf);
            }
            $gallupPath = $this->gallup_pdf->store('gallup', 'public');
            $this->candidate->gallup_pdf = $gallupPath;
        }

        // Save MBTI type if set
        if ($this->mbti_type) {
            $this->candidate->mbti_type = $this->mbti_type;
        }

        // Обновляем текущий шаг
        // Не изменяем шаг, если анкета уже завершена (step >= 5)
        if ($this->candidate->step < 5) {
        $this->candidate->step = $this->currentStep;
        }

        // Сохраняем все изменения
        $this->candidate->save();

        // Создаем запись в истории
        // Только если шаг действительно изменился и анкета не завершена
        if ($this->candidate->step < 5) {
        CandidateHistory::create([
            'candidate_id' => $this->candidate->id,
            'field_name' => 'step',
            'old_value' => $this->candidate->wasRecentlyCreated ? 0 : ($this->currentStep - 1),
            'new_value' => $this->currentStep,
            'changed_by' => auth()->user()?->name ?? 'Guest',
            'ip_address' => request()->ip()
        ]);
        }

        session()->flash('message', 'Прогресс сохранен');
    }

    public function addCountry()
    {
        logger()->debug('Adding country:', [
            'newCountry' => $this->newCountry,
            'visited_countries' => $this->visited_countries,
            'countries_count' => count($this->countries),
            'first_country' => $this->countries[0] ?? null
        ]);

        if ($this->newCountry && !in_array($this->newCountry, $this->visited_countries)) {
            $this->visited_countries[] = $this->newCountry;
            $this->newCountry = '';

            logger()->debug('Country added:', [
                'visited_countries' => $this->visited_countries,
                'last_added' => end($this->visited_countries)
            ]);
        }
    }

    public function addCountryByName($countryName)
    {
        if ($countryName && !in_array($countryName, $this->visited_countries)) {
            $this->visited_countries[] = $countryName;
            
            logger()->debug('Country added by name:', [
                'country_name' => $countryName,
                'visited_countries' => $this->visited_countries
            ]);
        }
    }

    public function getGallupPdfUrl()
    {
        if (!$this->gallup_pdf) {
            return null;
        }

        // Если это временный файл Livewire (UploadedFile)
        if (!is_string($this->gallup_pdf)) {
            return null; // Для временных файлов не показываем ссылку на скачивание
        }

        // Если это сохраненный файл (строка с путем)
        return Storage::disk('public')->url($this->gallup_pdf);
    }

    public function getGallupFileInfo()
    {
        if (!$this->gallup_pdf) {
            return null;
        }

        // Если это временный файл Livewire (UploadedFile)
        if (!is_string($this->gallup_pdf)) {
            return [
                'fileName' => $this->gallup_pdf->getClientOriginalName(),
                'fileSize' => $this->formatFileSize($this->gallup_pdf->getSize()),
                'isExisting' => false
            ];
        }

        // Если это сохраненный файл (строка с путем)
        $filePath = Storage::disk('public')->path($this->gallup_pdf);

        if (!file_exists($filePath)) {
            return [
                'fileName' => 'Файл не найден',
                'fileSize' => 'Не определен',
                'isExisting' => true
            ];
        }

        $pathInfo = pathinfo($this->gallup_pdf);
        $fileName = $pathInfo['basename'];

        // Убираем timestamp префикс если есть
        $cleanName = preg_replace('/^\d+_/', '', $fileName);

        return [
            'fileName' => $cleanName ?: 'Gallup результаты.pdf',
            'fileSize' => $this->formatFileSize(filesize($filePath)),
            'isExisting' => true
        ];
    }

    private function formatFileSize($bytes)
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Проверяет, является ли загруженный файл корректным Gallup PDF
     */
    private function isGallupPdf($file): bool
    {
        try {
            // Получаем временный путь к файлу
            $tempPath = $file->getRealPath();

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($tempPath);
            $text = $pdf->getText();
            $pages = $pdf->getPages();

            logger()->info('Gallup PDF validation', [
                'page_count' => count($pages),
                'has_gallup_inc' => str_contains($text, 'Gallup, Inc.'),
                'has_clifton' => str_contains($text, 'CliftonStrengths') || str_contains($text, 'Clifton'),
                'text_sample' => substr($text, 0, 500)
            ]);

            // Смягченные условия проверки Gallup-отчета
            $hasMinimumPages = count($pages) >= 10; // Минимум 10 страниц
            $containsGallupKeywords = str_contains($text, 'Gallup') ||
                                    str_contains($text, 'CliftonStrengths') ||
                                    str_contains($text, 'StrengthsFinder') ||
                                    str_contains($text, 'Clifton');

            // Если это PDF и содержит ключевые слова Gallup - считаем валидным
            $isValid = $hasMinimumPages && $containsGallupKeywords;

            logger()->info('Gallup PDF validation result', [
                'is_valid' => $isValid,
                'minimum_pages' => $hasMinimumPages,
                'has_keywords' => $containsGallupKeywords
            ]);

            return $isValid;
        } catch (\Exception $e) {
            logger()->error('Error checking Gallup PDF: ' . $e->getMessage());
            // В случае ошибки парсинга, разрешаем загрузку (возможно это корректный PDF)
            return true;
        }
    }

    /**
     * Конвертирует английские значения религии в русские для совместимости
     */
    private function convertReligionToRussian($religion)
    {
        $religions = config('lists.religions');

        // Если это уже русское значение, возвращаем как есть
        if (in_array($religion, array_values($religions))) {
            return $religion;
        }

        // Если это английский ключ, конвертируем в русское значение
        if (array_key_exists($religion, $religions)) {
            return $religions[$religion];
        }

        return $religion;
    }

    /**
     * Конвертирует старый формат опыта работы в новый
     */
    private function convertWorkExperienceFormat($workExperience)
    {
        logger()->debug('Converting work experience format:', ['input' => $workExperience]);

        if (empty($workExperience)) {
            logger()->debug('Work experience is empty, returning empty array');
            return [];
        }

        $converted = [];

        foreach ($workExperience as $experience) {
            // Если это уже новый формат (есть поле 'years'), оставляем как есть
            if (isset($experience['years'])) {
                $converted[] = [
                    'years' => $experience['years'] ?? '',
                    'company' => $experience['company'] ?? '',
                    'city' => $experience['city'] ?? '',
                    'position' => $experience['position'] ?? '',
                ];
            }
            // Если это старый формат, конвертируем
            else {
                $years = '';
                if (isset($experience['start_date']) && isset($experience['end_date'])) {
                    $startYear = $experience['start_date'] ? date('Y', strtotime($experience['start_date'])) : '';
                    $endYear = $experience['end_date'] ? date('Y', strtotime($experience['end_date'])) : '';
                    $years = $startYear && $endYear ? "$startYear-$endYear" : ($startYear ?: $endYear);
                }

                $converted[] = [
                    'years' => $years,
                    'company' => $experience['company'] ?? '',
                    'city' => '', // В старом формате не было города
                    'position' => $experience['position'] ?? '',
                ];
            }
        }

        logger()->debug('Work experience conversion completed:', ['output' => $converted]);
        return $converted;
    }

    /**
     * Переинициализирует валидацию при переходе между шагами
     */
    protected function reinitializeValidation()
    {
        // Сбрасываем только ошибки валидации, не затрагивая состояние полей
        $this->resetErrorBag();

        // Не вызываем resetValidation() чтобы не мешать JavaScript
        // $this->resetValidation();

        // Отправляем событие в браузер для переинициализации JavaScript
        $this->dispatch('step-changed', ['step' => $this->currentStep]);

        // Дополнительно отправляем событие для переинициализации JavaScript компонентов
        $this->dispatch('reinitialize-js');

        // Логируем для отладки
        logger()->debug('Validation reinitialized for step: ' . $this->currentStep);
        logger()->debug('Events dispatched: step-changed, reinitialize-js');
    }

    public function updatedBooksPerYearMin()
    {
        $this->updateBooksPerYear();
    }

    public function updatedBooksPerYearMax()
    {
        $this->updateBooksPerYear();
    }

    private function updateBooksPerYear()
    {
        if ($this->books_per_year_min == $this->books_per_year_max) {
            $this->books_per_year = (string) $this->books_per_year_min;
        } else {
            $this->books_per_year = $this->books_per_year_min . '-' . $this->books_per_year_max;
        }
    }

    private function parseBooksPerYear($value)
    {
        if (empty($value) || $value === '0' || $value === 0) {
            $this->books_per_year_min = 0;
            $this->books_per_year_max = 0;
            return;
        }

        // Если значение содержит дефис, это диапазон
        if (strpos($value, '-') !== false) {
            $parts = explode('-', $value);
            $this->books_per_year_min = (int) trim($parts[0]);
            $this->books_per_year_max = (int) trim($parts[1]);
        } else {
            // Иначе это одно значение
            $single_value = (int) $value;
            $this->books_per_year_min = $single_value;
            $this->books_per_year_max = $single_value;
        }
    }

    public function render()
    {
        return view('livewire.candidate-form');
    }
}
