<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет о кандидате - {{ $candidate->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                font-size: 10px;
                margin: 0;
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        body {
            font-size: 12px;
            line-height: 1.4;
        }
        
                 .logo-header {
             background: transparent;
         }
        
        .section-header {
            background: linear-gradient(to right, #f8fafc, #e2e8f0);
            border-left: 4px solid #3b82f6;
        }
        
        .text-xs { font-size: 10px; }
        .text-sm { font-size: 11px; }
        .text-base { font-size: 12px; }
        .text-lg { font-size: 14px; }
        .text-xl { font-size: 16px; }
        .text-2xl { font-size: 18px; }
        .text-3xl { font-size: 20px; }
    </style>
</head>
<body class="bg-white font-sans">
    <div class="max-w-full">
                 <!-- Header -->
         <div class="logo-header p-4">
             <div class="flex justify-between items-center">
                 <div>
                     <img src="{{ asset('logos/divergents_logo.png') }}" alt="DIVERGENTS talent laboratory" class="h-8 w-auto">
                 </div>
                 <div class="text-right">
                     <img src="{{ asset('logos/talents_lab_logo.png') }}" alt="talents lab" class="h-6 w-auto">
                 </div>
             </div>
         </div>

                 <!-- Candidate Header -->
         <div class="p-4 border-b border-gray-200">
             <div class="flex items-start gap-6">
                 <div class="w-80">
                     <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $candidate->full_name }}</h1>
                     <div class="text-sm mb-4">
                         <div class="mb-3">
                             <span class="font-medium text-gray-800">{{ $candidate->current_city }}</span>
                             <span class="font-medium text-gray-800 ml-6">{{ $candidate->phone }}</span>
                             <span class="font-medium text-gray-800 ml-6">{{ $candidate->email }}</span>
                         </div>
                         <div>
                             <span class="font-medium text-gray-600">Дата заполнения:</span>
                             <span class="text-gray-800">{{ $candidate->created_at->format('d.m.Y') }}</span>
                         </div>
                     </div>
                     
                     <!-- Основная информация -->
                     <div>
                         <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Основная информация</h2>
                         <div class="space-y-1">
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Желаемая должность:</span>
                                 <span class="text-sm font-medium">{{ $candidate->desired_position ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Ожидаемая заработная плата:</span>
                                 <span class="text-sm font-medium">{{ number_format($candidate->expected_salary) }} тг.</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Дата рождения:</span>
                                 <span class="text-sm font-medium">{{ $candidate->birth_date?->format('d.m.Y') ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Место рождения:</span>
                                 <span class="text-sm font-medium">{{ $candidate->birth_place ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Семейное положение:</span>
                                 <span class="text-sm font-medium">{{ $candidate->marital_status ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Пол:</span>
                                 <span class="text-sm font-medium">{{ $candidate->gender ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Религия:</span>
                                 <span class="text-sm font-medium">{{ $candidate->religion ?: 'Не указано' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Практикующий:</span>
                                 <span class="text-sm font-medium">{{ $candidate->is_practicing ? 'Да' : 'Нет' }}</span>
                             </div>
                             <div class="flex">
                                 <span class="w-28 text-sm text-gray-600">Школа:</span>
                                 <span class="text-sm font-medium">{{ $candidate->school ?: 'Не указано' }}</span>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="flex-1 flex justify-center">
                     @if($photoUrl)
                         <img src="{{ $photoUrl }}" alt="Фото кандидата" class="w-36 h-48 object-cover rounded border border-gray-300">
                     @else
                         <div class="w-36 h-48 bg-gray-300 rounded border border-gray-300 flex items-center justify-center">
                             <span class="text-gray-500 text-xs">Фото</span>
                         </div>
                     @endif
                 </div>
             </div>
         </div>

        <!-- Main Content -->
        <div class="p-4">


            <!-- Родственники -->
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Родственники</h2>
                @if($candidate->family_members && count($candidate->family_members) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->family_members as $index => $member)
                            <div class="flex text-sm">
                                <span class="w-6 text-gray-600">{{ $index + 1 }}.</span>
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
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Образование</h2>
                @if($candidate->universities && count($candidate->universities) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->universities as $index => $university)
                            <div class="flex text-sm">
                                <span class="w-6 text-gray-600">{{ $index + 1 }}.</span>
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
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Опыт работы</h2>
                @if($candidate->work_experience && count($candidate->work_experience) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->work_experience as $index => $experience)
                            <div class="flex text-xs">
                                <span class="w-6 text-gray-600">{{ $index + 1 }}.</span>
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
                    <div class="mt-3 flex gap-6">
                        <div class="flex">
                            <span class="text-xs text-gray-600 w-32">Общий стаж работы (лет):</span>
                            <span class="text-xs font-medium">{{ $candidate->total_experience_years ?? 0 }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-xs text-gray-600 w-32">Любит свою работу (из 10):</span>
                            <span class="text-xs font-medium">{{ $candidate->job_satisfaction ?? 'Не указано' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-500">Опыт работы не указан</p>
                @endif
            </div>

            <!-- Личная информация -->
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Личная информация</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Хобби:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->hobbies ?: 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Интересы:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->interests ?: 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Любимые развлечения:</span>
                            <span class="text-xs text-gray-800">
                                @if($candidate->entertainment_hours_weekly)
                                    {{ $candidate->entertainment_hours_weekly }} часов в неделю
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Любимые виды спорта:</span>
                            <span class="text-xs text-gray-800">
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
                    <div class="space-y-2">
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Кол-во книг в год:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->books_per_year ?? 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Водительские права:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->has_driving_license ? 'Есть' : 'Нет' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Религиозная практика:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->is_practicing ? 'Да' : 'Нет' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Часы на обр. видео в неделю:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->educational_hours_weekly ?? 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Часы на соц. сети в неделю:</span>
                            <span class="text-xs text-gray-800">{{ $candidate->social_media_hours_weekly ?? 'Не указано' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-600 mb-1">Посещенные страны:</span>
                            <span class="text-xs text-gray-800">
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
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Языковые навыки</h2>
                @if($candidate->language_skills && count($candidate->language_skills) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->language_skills as $skill)
                            <div class="flex text-xs">
                                <span class="w-20 font-medium">{{ $skill['language'] ?? 'Не указано' }}</span>
                                <span class="w-24">{{ $skill['level'] ?? 'Не указано' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500">Языковые навыки не указаны</p>
                @endif
            </div>

            <!-- Компьютерные навыки -->
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Компьютерные навыки</h2>
                <p class="text-xs text-gray-800">{{ $candidate->computer_skills ?: 'Не указано' }}</p>
            </div>

            <!-- Психометрические данные -->
            <div class="mb-6">
                <h2 class="section-header text-sm font-medium text-gray-500 p-2 mb-3">Психометрические данные</h2>
                <div class="flex">
                    <span class="text-xs text-gray-600 w-32">Тип личности по MBTI:</span>
                    <span class="text-xs font-medium text-blue-600">{{ $candidate->mbti_type ?: 'Не указано' }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 p-3 text-center text-xs text-gray-500 mt-6">
            <p>Отчет сгенерирован {{ now()->format('d.m.Y в H:i') }}</p>
        </div>
    </div>

    <!-- Auto print script -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html> 