<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет о кандидате - {{ $candidate->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { font-size: 12px; }
            .no-print { display: none; }
        }
        
        .logo-header {
            background: transparent;
        }
        
        .section-header {
            background: linear-gradient(to right, #f8fafc, #e2e8f0);
            border-left: 4px solid #3b82f6;
        }
        
        /* Fallback TailwindCSS базовые стили если основные не загрузились */
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-white { background-color: #ffffff; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-blue-600 { color: #2563eb; }
        .font-sans { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .max-w-4xl { max-width: 56rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .p-6 { padding: 1.5rem; }
        .p-4 { padding: 1rem; }
        .p-3 { padding: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .ml-8 { margin-left: 2rem; }
        .mt-4 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .flex { display: flex; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .space-y-3 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.75rem; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
        .items-start { align-items: flex-start; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .w-150 { width: 37.5rem; }
        .w-40 { width: 10rem; }
        .w-20 { width: 5rem; }
        .w-32 { width: 8rem; }
        .w-8 { width: 2rem; }
        .w-72 { width: 18rem; }
        .w-48 { width: 12rem; }
        .w-auto { width: auto; }
        .h-14 { height: 3.5rem; }
        .h-12 { height: 3rem; }
        .h-90 { height: 22.5rem; }
        .h-60 { height: 15rem; }
        .flex-1 { flex: 1 1 0%; }
        .border-b { border-bottom-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        .rounded { border-radius: 0.25rem; }
        .object-cover { object-fit: cover; }
        .block { display: block; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-blue-500 { background-color: #3b82f6; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-green-500 { background-color: #22c55e; }
        .text-gray-700 { color: #374151; }
        .text-green-800 { color: #166534; }
        .text-green-600 { color: #16a34a; }
        .text-green-700 { color: #15803d; }
        .border { border-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-green-200 { border-color: #bbf7d0; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .rounded-full { border-radius: 9999px; }
        .mr-3 { margin-right: 0.75rem; }
        .mt-1 { margin-top: 0.25rem; }
        .flex-col { flex-direction: column; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .font-extrabold { font-weight: 800; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="max-w-4xl mx-auto bg-white shadow-lg">
        <!-- Header -->
        <div class="logo-header p-6">
            <div class="flex justify-between items-center">
                <div>
                    <img src="{{ asset('logos/divergents_logo.png') }}" alt="DIVERGENTS talent laboratory" class="h-14 w-auto">
                </div>
                <div class="text-right">
                    <img src="{{ asset('logos/talents_lab_logo.png') }}" alt="talents lab" class="h-12 w-auto">
                </div>
            </div>
        </div>

                <!-- Candidate Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start gap-8">
                <div class="w-150">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $candidate->full_name }}</h1>
                     <div class="text-sm mb-6">
                         <div class="mb-4">
                             <span class="font-medium text-gray-800">{{ $candidate->current_city }}</span>
                             <span class="font-medium text-gray-800 ml-8">{{ $candidate->phone }}</span>
                             <span class="font-medium text-gray-800 ml-8">{{ $candidate->email }}</span>
                         </div>
                         <div>
                             <span class=" text-gray-500">Дата заполнения:</span>
                             <span class="font-medium text-gray-600">{{ $candidate->created_at->format('d.m.Y') }}</span>
                         </div>
                     </div>
                     
                     <!-- Основная информация -->
                     <div>
                         <!--h2 class="section-header text-lg font-bold p-3 mb-4">Основная информация</h2-->
                         <div class="space-y-3">
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Желаемая должность:</span>
                                 <span class="text-sm font-medium">{{ $candidate->desired_position ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Ожидаемая заработная плата:</span>
                                 <span class="text-sm font-medium">{{ number_format($candidate->expected_salary) }} тг.</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Дата рождения:</span>
                                 <span class="text-sm font-medium">{{ $candidate->birth_date?->format('d.m.Y') ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Место рождения:</span>
                                 <span class="text-sm font-medium">{{ $candidate->birth_place ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Семейное положение:</span>
                                 <span class="text-sm font-medium">{{ $candidate->marital_status ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Пол:</span>
                                 <span class="text-sm font-medium">{{ $candidate->gender ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Религия:</span>
                                 <span class="text-sm font-medium">{{ $candidate->religion ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Практикующий:</span>
                                 <span class="text-sm font-medium">{{ $candidate->is_practicing ? 'Да' : 'Нет' }}</span>
                             </div>
                            <div class="flex">
                                 <span class="w-40 text-sm text-gray-600">Водительские права:</span>
                                 <span class="text-sm font-medium">{{ $candidate->has_driving_license ? 'Есть' : 'Нет' }}</span>
                             </div>                             

                         </div>
                     </div>
                </div>
                <div class="flex-1 flex justify-center">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Фото кандидата" class="w-72 h-90 object-cover rounded border-2 border-gray-300">
                    @else
                        <div class="w-48 h-60 bg-gray-300 rounded border-2 border-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm">Фото</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6">


            <!-- Родственники -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Родственники</h2>
                @if($candidate->family_members && count($candidate->family_members) > 0)
                    <div class="space-y-2">
                        @foreach($candidate->family_members as $index => $member)
                            <div class="flex text-sm">
                                <span class="w-8 text-gray-600">{{ $index + 1 }}.</span>
                                <span class="flex-1">
                                    <span class="font-medium">{{ $member['type'] ?? 'Не указано' }}</span> - 
                                    <span class="font-medium">{{ $member['birth_year'] ?? 'Не указано' }} г.р.</span> - 
                                    <span>{{ $member['profession'] ?? 'Не указано' }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Информация о родственниках не указана</p>
                @endif
            </div>

            <!-- Образование -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Образование</h2>
                
                @if($candidate->universities && count($candidate->universities) > 0)
                    <div class="space-y-2">
                    <div class="flex text-sm">
                                 <span class="w-20 text-sm text-gray-600">Школа:</span>
                                 <span class="font-medium">{{ $candidate->school ?: 'Не указано' }}</span>
                             </div>
                        @foreach($candidate->universities as $index => $university)
                            <div class="flex text-sm">
                                <span class="w-8 text-gray-600">{{ $index + 1 }}.</span>
                                <span class="flex-1">
                                    <span class="font-medium">{{ $university['graduation_year'] ?? 'Не указано' }}</span> - 
                                    <span class="font-medium">{{ $university['name'] ?? 'Не указано' }}</span> - 
                                    <span>{{ $university['speciality'] ?? 'Не указано' }}</span>
                                    @if(!empty($university['gpa']))
                                        - <span class="text-gray-600">GPA: {{ $university['gpa'] }}</span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                @else
                    <p class="text-sm text-gray-500">Информация об образовании не указана</p>
                @endif
            </div>

            <!-- Опыт работы -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Опыт работы</h2>
                @if($candidate->work_experience && count($candidate->work_experience) > 0)
                    <div class="space-y-2">
                        @foreach($candidate->work_experience as $index => $experience)
                            <div class="flex text-sm">
                                <span class="w-8 text-gray-600">{{ $index + 1 }}.</span>
                                <span class="flex-1">
                                    <span class="font-medium">{{ $experience['years'] ?? 'Не указано' }}</span> - 
                                    <span class="font-medium">{{ $experience['company'] ?? 'Не указано' }}</span> - 
                                    <span>{{ $experience['position'] ?? 'Не указано' }}</span>
                                    @if(!empty($experience['city']))
                                        - <span class="text-gray-600">{{ $experience['city'] }}</span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex gap-6">
                        <div class="flex">
                            <span class="text-sm text-gray-600 w-40">Общий стаж работы (лет):</span>
                            <span class="text-sm font-medium">{{ $candidate->total_experience_years ?? 0 }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-sm text-gray-600 w-40">Любит свою работу (из 10):</span>
                            <span class="text-sm font-medium">{{ $candidate->job_satisfaction ?? 'Не указано' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Опыт работы не указан</p>
                @endif
            </div>

            <!-- Личная информация -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Личная информация</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Кол-во книг в год:</span>
                            <span class="text-sm text-gray-800">{{ $candidate->books_per_year ?? 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Хобби:</span>
                            <span class="text-sm text-gray-800">{{ $candidate->hobbies ?: 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Интересы:</span>
                            <span class="text-sm text-gray-800">{{ $candidate->interests ?: 'Не указано' }}</span>
                        </div>
                        
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Любимые виды спорта:</span>
                            <span class="text-sm text-gray-800">
                                @if($candidate->favorite_sports)
                                    @if(is_array($candidate->favorite_sports))
                                        {{ implode(', ', $candidate->favorite_sports) }}
                                    @else
                                        {{ $candidate->favorite_sports }}
                                    @endif
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Развлекательные видео:</span>
                            <span class="text-sm text-gray-800">
                                @if($candidate->entertainment_hours_weekly)
                                    {{ $candidate->entertainment_hours_weekly }} часов в неделю
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                    
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Образовательные видео:</span>
                            <span class="text-sm text-gray-800">{{ $candidate->educational_hours_weekly ?? 'Не указано' }} часов в неделю</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Социальные сети:</span>
                            <span class="text-sm text-gray-800">{{ $candidate->social_media_hours_weekly ?? 'Не указано' }} часов в неделю</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-600 mb-1">Посетившие места:</span>
                            <span class="text-sm text-gray-800">
                                @if($candidate->visited_countries)
                                    @if(is_array($candidate->visited_countries))
                                        {{ implode(', ', $candidate->visited_countries) }}
                                    @else
                                        {{ $candidate->visited_countries }}
                                    @endif
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Языковые навыки -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Языковые навыки</h2>
                @if($candidate->language_skills && count($candidate->language_skills) > 0)
                    <div class="space-y-2">
                        @foreach($candidate->language_skills as $skill)
                            <div class="flex text-sm">
                                <span class="w-20 font-medium">{{ $skill['language'] ?? 'Не указано' }}</span>
                                <span class="w-32">{{ $skill['level'] ?? 'Не указано' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Языковые навыки не указаны</p>
                @endif
            </div>

            <!-- Компьютерные навыки -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Компьютерные навыки</h2>
                <p class="text-sm text-gray-800">{{ $candidate->computer_skills ?: 'Не указано' }}</p>
            </div>

            <!-- Психометрические данные -->
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Психометрические данные</h2>
                <div class="flex">
                    <span class="text-sm text-gray-600 w-40">Тип личности по MBTI:</span>
                    <span class="text-sm font-medium text-blue-600">{{ $candidate->mbti_type ?: 'Не указано' }}</span>
                </div>
            </div>

            <!-- Тест Гарднера -->
            @if($candidate->user && $candidate->user->gardnerTestResult)
            @php
                // Находим доминирующий тип интеллекта
                $results = $candidate->user->gardnerTestResult->results;
                $maxPercentage = 0;
                $dominantType = '';
                
                foreach($results as $type => $percentage) {
                    $numericPercentage = (int) str_replace('%', '', $percentage);
                    if ($numericPercentage > $maxPercentage) {
                        $maxPercentage = $numericPercentage;
                        $dominantType = $type;
                    }
                }
            @endphp
            <div class="mb-8">
                <h2 class="section-header text-lg font-bold p-3 mb-4">Тест типов интеллекта (Гарднер)</h2>
                <div class="grid grid-cols-2 gap-6">
                    @foreach($candidate->user->gardnerTestResult->results as $intelligenceType => $percentage)
                    @php
                        $isDominant = ($intelligenceType === $dominantType);
                        $bgClass = $isDominant ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200';
                        $textClass = $isDominant ? 'text-green-800' : 'text-gray-700';
                        $barClass = $isDominant ? 'bg-green-500' : 'bg-blue-500';
                        $percentageClass = $isDominant ? 'text-green-700 font-extrabold' : 'text-blue-600 font-bold';
                    @endphp
                    <div class="flex items-center justify-between p-3 {{ $bgClass }} rounded border-2 {{ $isDominant ? 'shadow-md' : '' }}">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium {{ $textClass }}">{{ $intelligenceType }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-24 h-2 bg-gray-200 rounded-full mr-3">
                                <div class="h-2 {{ $barClass }} rounded-full" style="width: {{ $percentage }}"></div>
                            </div>
                            <span class="text-sm {{ $percentageClass }}">{{ $percentage }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    <p>Тест пройден: {{ $candidate->user->gardnerTestResult->created_at->format('d.m.Y в H:i') }}</p>
                    <p class="mt-1"><span class="text-green-600 font-medium">Доминирующий тип:</span> {{ $dominantType }} ({{ $maxPercentage }}%)</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 p-4 text-center text-xs text-gray-500 no-print">
            <p>Отчет сгенерирован {{ now()->format('d.m.Y в H:i') }}</p>
        </div>
    </div>




</body>
</html> 