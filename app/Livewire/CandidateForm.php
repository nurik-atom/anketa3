<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\CandidateFile;
use App\Models\CandidateHistory;
use App\Models\CandidateStatus;
use App\Jobs\ProcessGallupFile;
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
    public $familyMemberType = '';
    public $familyMemberBirthYear;
    public $familyMemberProfession = '';
    public $hobbies;
    public $interests;
    public $visited_countries = [];
    public $newCountry = '';
    public $books_per_year;
    public $favorite_sports = [];
    public $newSport = '';
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
            $this->books_per_year = 0;
            
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
        $this->visited_countries = [];
        $this->favorite_sports = [];
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
                    // Убеждаемся что work_experience пустой массив
                    $this->work_experience = [];
                }
            }
        }
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
        $this->hobbies = $this->candidate->hobbies;
        $this->interests = $this->candidate->interests;
        $this->visited_countries = $this->candidate->visited_countries ?? [];
        $this->books_per_year = $this->candidate->books_per_year;
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
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/'],
            'gender' => 'required|in:Мужской,Женский',
            'marital_status' => 'required|in:Холост/Не замужем,Женат/Замужем,Разведен(а),Вдовец/Вдова',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
        'current_city' => 'required|string|max:255',
            'photo' => !$this->candidate?->photo ? 'required|image|max:2048' : 'nullable|image|max:2048',

            // Step 2 validation rules
            'religion' => 'required|string|in:' . implode(',', array_values(config('lists.religions'))),
            'is_practicing' => 'nullable|boolean',
            'family_members' => 'nullable|array',
            'visited_countries' => 'nullable|array',
            'visited_countries.*' => 'string|in:' . implode(',', collect($this->countries)->pluck('name_ru')->all()),
            'books_per_year' => 'nullable|integer|min:0',
            'favorite_sports' => 'nullable|array',
            'favorite_sports.*' => 'in:' . implode(',', array_values(config('lists.sports'))),
            'entertainment_hours_weekly' => 'nullable|integer|min:0|max:168',
            'educational_hours_weekly' => 'nullable|integer|min:0|max:168',
            'social_media_hours_weekly' => 'nullable|integer|min:0|max:168',
            'has_driving_license' => 'required|boolean',

            // Step 3 validation rules
            'school' => 'required|string|max:255',
            'universities' => 'nullable|array',
            'universities.*.name' => 'required|string|max:255',
            'universities.*.graduation_year' => 'required|integer|min:1950|max:' . date('Y'),
            'universities.*.speciality' => 'required|string|max:255',
            'universities.*.gpa' => 'required|numeric|min:0|max:4',
            'language_skills' => 'nullable|array',
            'language_skills.*.language' => 'required|string' . (!empty($this->languages) ? '|in:' . implode(',', $this->languages) : ''),
            'language_skills.*.level' => 'required|in:Начальный,Средний,Продвинутый,Родной',
            'computer_skills' => 'nullable|string',
            'work_experience' => 'nullable|array',
            'work_experience.*.years' => 'required_with:work_experience.*|string|max:255',
            'work_experience.*.company' => 'required_with:work_experience.*|string|max:255',
            'work_experience.*.city' => 'required_with:work_experience.*|string|max:255',
            'work_experience.*.position' => 'required_with:work_experience.*|string|max:255',
            'total_experience_years' => 'required|integer|min:0',
            'job_satisfaction' => 'nullable|integer|min:1|max:10',
            'desired_position' => 'required|string|max:255',
            'expected_salary' => 'required|numeric|min:0|max:999999999999',
            'employer_requirements' => 'nullable|string',

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
                Rule::when($this->currentStep === 4, ['required', 'string', 'in:INTJ-A,INTJ-T,INTP-A,INTP-T,ENTJ-A,ENTJ-T,ENTP-A,ENTP-T,INFJ-A,INFJ-T,INFP-A,INFP-T,ENFJ-A,ENFJ-T,ENFP-A,ENFP-T,ISTJ-A,ISTJ-T,ISFJ-A,ISFJ-T,ESTJ-A,ESTJ-T,ESFJ-A,ESFJ-T,ISTP-A,ISTP-T,ISFP-A,ISFP-T,ESTP-A,ESTP-T,ESFP-A,ESFP-T']),
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
        'email.required' => 'Email обязателен для заполнения',
        'email.email' => 'Введите корректный email адрес',
        'phone.required' => 'Телефон обязателен для заполнения',
        'phone.regex' => 'Введите телефон в формате +7 (XXX) XXX-XX-XX',
        'gender.required' => 'Выберите пол',
        'marital_status.required' => 'Выберите семейное положение',
        'birth_date.required' => 'Дата рождения обязательна для заполнения',
        'birth_date.before' => 'Дата рождения должна быть раньше текущей даты',
        'birth_place.required' => 'Место рождения обязательно для заполнения',
        'current_city.required' => 'Введите текущий город',
        'photo.required' => 'Фото обязательно для загрузки',
        'photo.image' => 'Загружаемый файл должен быть изображением (jpg, jpeg, png)',
        'photo.max' => 'Размер изображения не должен превышать 2MB',
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
    ];

    public function updated($propertyName)
    {
        // Если обновляется поле университета
        if (strpos($propertyName, 'universities.') === 0) {
            $this->validateOnly($propertyName);
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
            // Извлекаем индекс языка из имени свойства
            if (preg_match('/language_skills\.(\d+)\./', $propertyName, $matches)) {
                $index = $matches[1];
                // Валидируем только если оба поля заполнены
                if (!empty($this->language_skills[$index]['language']) && !empty($this->language_skills[$index]['level'])) {
                    // Обеспечиваем корректную инициализацию языков для валидации
                    if (empty($this->languages)) {
                        $this->loadLanguages();
                    }
                    $this->validateOnly($propertyName);
                }
            }
            return;
        }

        // Валидируем только поля текущего шага
        $rules = collect($this->rules())->filter(function ($rule, $field) {
            return $this->isFieldInCurrentStep($field);
        })->toArray();

        // Исключаем валидацию language_skills если языки не загружены
        if (empty($this->languages) && isset($rules['language_skills.*.language'])) {
            unset($rules['language_skills.*.language']);
        }

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
        $step1Fields = ['last_name', 'first_name', 'middle_name', 'email', 'phone', 'gender', 'marital_status', 'birth_date', 'birth_place', 'current_city', 'photo'];
        $step2Fields = ['religion', 'is_practicing', 'family_members', 'hobbies', 'interests', 'visited_countries', 'books_per_year', 'favorite_sports', 'entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'has_driving_license'];
        $step3Fields = ['school', 'universities', 'language_skills', 'computer_skills', 'work_experience', 'total_experience_years', 'job_satisfaction', 'desired_position', 'expected_salary', 'employer_requirements'];
        $step4Fields = ['gallup_pdf', 'mbti_type'];

        return match($this->currentStep) {
            1 => in_array($field, $step1Fields),
            2 => in_array($field, $step2Fields),
            3 => in_array($field, $step3Fields),
            4 => in_array($field, $step4Fields),
            default => false,
        };
    }

    public function nextStep()
    {
        try {
            logger()->debug('Starting nextStep method');
            logger()->debug('Current step: ' . $this->currentStep);
            
            $rules = $this->getStepRules();
            
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
        return match($this->currentStep) {
            1 => [
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => ['required', 'string', 'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/'],
                'gender' => 'required|in:Мужской,Женский',
                'marital_status' => 'required|in:Холост/Не замужем,Женат/Замужем,Разведен(а),Вдовец/Вдова',
                'birth_date' => 'required|date|before:today',
                'birth_place' => 'required|string|max:255',
                'current_city' => 'required|string|max:255',
                'photo' => !$this->candidate?->photo ? 'required|image|max:2048' : 'nullable|image|max:2048',
            ],
            2 => [
                'religion' => $this->rules()['religion'],
                'is_practicing' => $this->rules()['is_practicing'],
                'family_members' => $this->rules()['family_members'],
                'visited_countries' => $this->rules()['visited_countries'],
                'visited_countries.*' => 'string|in:' . implode(',', collect($this->countries)->pluck('name_ru')->all()),
                'books_per_year' => $this->rules()['books_per_year'],
                'favorite_sports' => $this->rules()['favorite_sports'],
                'entertainment_hours_weekly' => $this->rules()['entertainment_hours_weekly'],
                'educational_hours_weekly' => $this->rules()['educational_hours_weekly'],
                'social_media_hours_weekly' => $this->rules()['social_media_hours_weekly'],
                'has_driving_license' => $this->rules()['has_driving_license'],
            ],
            3 => [
                'school' => $this->rules()['school'],
                'universities' => $this->rules()['universities'],
                'language_skills' => $this->rules()['language_skills'],
                'computer_skills' => $this->rules()['computer_skills'],
                'work_experience' => $this->rules()['work_experience'],
                'total_experience_years' => $this->rules()['total_experience_years'],
                'job_satisfaction' => $this->rules()['job_satisfaction'],
                'desired_position' => $this->rules()['desired_position'],
                'expected_salary' => $this->rules()['expected_salary'],
                'employer_requirements' => $this->rules()['employer_requirements'],
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

    // Dynamic field methods
    public function addFamilyMember()
    {
        $this->validate([
            'familyMemberType' => 'required|string|in:Отец,Мать,Брат,Сестра,Жена,Муж,Сын,Дочь',
            'familyMemberBirthYear' => 'required|integer|min:1900|max:' . date('Y'),
            'familyMemberProfession' => 'required|string|max:255',
        ]);

        $this->family_members[] = [
            'type' => $this->familyMemberType,
            'birth_year' => $this->familyMemberBirthYear,
            'profession' => $this->familyMemberProfession
        ];

        // Очищаем поля после добавления
        $this->familyMemberType = '';
        $this->familyMemberBirthYear = null;
        $this->familyMemberProfession = '';
    }

    public function removeFamilyMember($index)
    {
        unset($this->family_members[$index]);
        $this->family_members = array_values($this->family_members);
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

    public function addSport()
    {
        if (!empty($this->newSport) && !in_array($this->newSport, $this->favorite_sports)) {
            $this->favorite_sports[] = $this->newSport;
            $this->newSport = '';
        }
    }

    public function removeSport($index)
    {
        unset($this->favorite_sports[$index]);
        $this->favorite_sports = array_values($this->favorite_sports);
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
            'photo' => 'image|max:2048' // 2MB
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
                // Сохраняем базовую информацию если есть
                if ($this->last_name || $this->first_name) {
                    $this->candidate->full_name = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
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
        if ($this->gallup_pdf) {
            try {
                // Базовая валидация файла
                $this->validate([
                    'gallup_pdf' => 'file|mimes:pdf|max:10240'
                ]);
                
                // Проверяем, что это корректный Gallup PDF
                if (!$this->isGallupPdf($this->gallup_pdf)) {
                    $this->addError('gallup_pdf', 'Загруженный файл не является корректным отчетом Gallup. Убедитесь, что это официальный PDF с результатами теста Gallup.');
                    $this->resetGallupFile();
                    return;
                }
                
                // Отправляем событие в JavaScript
                $this->dispatch('gallup-file-uploaded');
                
                session()->flash('message', 'PDF файл загружен и проверен');
            } catch (\Exception $e) {
                $this->addError('gallup_pdf', 'Ошибка при обработке файла: ' . $e->getMessage());
                $this->resetGallupFile();
                logger()->error('Error processing Gallup PDF: ' . $e->getMessage());
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
            
            // Создаем специальные правила для финального submit
            $rules = $this->rules();
            
            // Если фото уже сохранено (строка) и не загружается новое, исключаем из валидации
            if ($this->candidate && $this->candidate->photo && is_string($this->photo)) {
                unset($rules['photo']);
                logger()->debug('Photo validation removed (existing file)');
            } else if ($this->candidate && $this->candidate->photo) {
                // Если есть сохраненное фото, но загружается новое
                $rules['photo'] = ['nullable', 'image', 'max:2048'];
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
            $this->candidate->family_members = $this->family_members;
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
            $this->candidate->language_skills = $this->language_skills;
            $this->candidate->computer_skills = $this->computer_skills;
            $this->candidate->work_experience = $this->work_experience;
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
            
            // Перенаправляем на дашборд
            return redirect()->route('dashboard');
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
        }

        // Базовые данные всегда сохраняем, если они есть
        if ($this->last_name || $this->first_name) {
            $this->candidate->full_name = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
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
        
        // Сохраняем членов семьи только если массив не пустой и все обязательные поля заполнены
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
        if (!empty($this->language_skills)) $this->candidate->language_skills = $this->language_skills;
        if ($this->computer_skills !== null) $this->candidate->computer_skills = $this->computer_skills;
        if (!empty($this->work_experience)) $this->candidate->work_experience = $this->work_experience;
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

            // Ключевые признаки Gallup-отчета
            $hasCorrectPageCount = count($pages) === 26;
            $containsCliftonHeader = str_contains($text, 'Gallup, Inc. All rights reserved.');
            $containsGallupCopyright = str_contains($text, 'Gallup, Inc.');
            $containsTalentList = preg_match('/1\.\s+[A-Za-z-]+/', $text);

            return $hasCorrectPageCount && $containsCliftonHeader && $containsGallupCopyright && $containsTalentList;
        } catch (\Exception $e) {
            logger()->error('Error checking Gallup PDF: ' . $e->getMessage());
            return false;
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

    public function render()
    {
        return view('livewire.candidate-form');
    }
} 