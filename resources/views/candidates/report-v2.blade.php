<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if($isReducedReport)Урезанный отчет о кандидате@elseОтчет о кандидате@endif - {{ $candidate->full_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 10mm 0mm 10mm 0mm !important;
        }
        body {
            /*padding-left: 10mm !important;*/
            /*padding-right: 10mm !important;*/
            margin: 0 !important;
        }
        * {
            margin: 0;
            padding: 0;
        }
        @media print {
            body { font-size: 15px; }
            .no-print { display: none; }
        }

        .logo-header {
            background: transparent;
        }

        /* Fallback TailwindCSS базовые стили если основные не загрузились */
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-white { background-color: #ffffff; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-blue-600 { color: #2563eb; }
        body { font-family: 'Montserrat', sans-serif; }
        .font-sans { font-family: 'Montserrat', sans-serif; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
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
        .mb-2 { margin-bottom: 0.5rem; }
        .flex { display: flex; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .space-y-3 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.75rem; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
        .space-y-1 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.25rem; }
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
        .w-60 { width: 19rem; }
        .w-auto { width: auto; }
        .h-14 { height: 3.5rem; }
        .h-4 { height: 1.5rem; }
        .h-8 { height: 2rem; }
        .h-12 { height: 3rem; }
        .h-90 { height: 22.5rem; }
        .h-60 { height: 15rem; }
        .h-80 { height: 20rem; }
        .w-64 { width: 16rem; }
        .w-47 { width: 11.75rem; }
        .h-69 { height: 17.25rem; }
        .flex-1 { flex: 1 1 0%; }
        .flex-shrink-0 { flex-shrink: 0; }
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
        .float-right { float: right; }
        .clear-both { clear: both; }
        .ml-6 { margin-left: 1.5rem; }

        /* Стили для PDF содержимого */
        .pdf-content {
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .pdf-content p {
            margin-bottom: 0.5rem;
        }
        .bg-red-50 { background-color: #fef2f2; }
        .border-red-200 { border-color: #fecaca; }
        .text-red-600 { color: #dc2626; }
        .leading-relaxed { line-height: 1.625; }
        .hover\:text-blue-800:hover { color: #1e40af; }

        /* Стили для PDF изображений */
        .pdf-images-container {
            margin-top: 1rem;
        }

        .pdf-page-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .pdf-page-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #374151;
        }

        .pdf-image-wrapper {
            padding: 1rem;
            text-align: center;
        }

        .pdf-page-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .pdf-page-image:hover {
            transform: scale(1.02);
            cursor: pointer;
        }

        /* Стили для переключения видимости */
        .pdf-images-container {
            transition: opacity 0.3s ease-in-out, max-height 0.3s ease-in-out;
        }

        .pdf-images-container.hidden {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }

        /* Адаптивные стили для изображений */
        @media (max-width: 768px) {
            .pdf-image-wrapper {
                padding: 0.5rem;
            }

            .pdf-page-image {
                max-width: 100%;
            }
        }

        /* Стили для полноэкранного просмотра */
        .pdf-fullscreen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .pdf-fullscreen-image {
            max-width: 90%;
            max-height: 90%;
            border-radius: 0.5rem;
        }

        .pdf-fullscreen-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 1001;
        }

        /* Стили для переключателя PDF */
        .space-x-2 > * + * {
            margin-left: 0.5rem;
        }

        .pdf-viewer {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .text-purple-600 { color: #9333ea; }
        .hover\:text-purple-800:hover { color: #6b21a8; }

        /* Анимация для переключения */
        .pdf-content, .pdf-viewer {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto bg-white ">
        <!-- Header -->
        <div class="logo-header p-3">
            <div class="flex justify-between items-center">
                <div>
               <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logos/divergents_logo.png'))) }}" alt="DIVERGENTS talent laboratory" class="h-14 w-auto">
                </div>
                <div class="text-right">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logos/talents_lab_logo.png'))) }}" alt="talents lab" class="h-4 w-auto">
                </div>
            </div>
        </div>

        <!-- Candidate Header -->
        <div class="p-3 border-b border-gray-200">
            <div>
                <!-- Фото справа с обтеканием -->
                <div class="float-right ml-6 flex-shrink-0">
                    @if($photoUrl)
                        <img src="{{$photoUrl}}" alt="Фото кандидата" class="w-47 h-69 object-cover rounded border-2 border-gray-300">
{{--                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($photoUrl)) }}" alt="Фото кандидата" class="w-47 h-69 object-cover rounded border-2 border-gray-300">--}}
                    @else
                        <div class="w-47 h-69 bg-gray-300 rounded border-2 border-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm">Фото</span>
                        </div>
                    @endif
                </div>

                <div>
                        @if($isReducedReport)
                            <span class="text-lg text-gray-500 font-normal">(урезанная версия)</span>
                        @endif
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">
                        {{ $candidate->full_name }}
                    </h1>
                     @if($isFullReport)
                     <div class="text-base mb-6">
                         <div class="mb-4">
                             <!-- <span class="font-medium text-gray-800"></span> -->
                             <span class="font-medium text-gray-800 ml-8">{{ $candidate->email }}</span>
                             <span class="font-medium text-gray-800 ml-8">{{ $candidate->phone }}</span>

                             @if($candidate->instagram)
                                 <span class="font-medium text-gray-800 ml-8">{{ $candidate->instagram }}</span>
                             @endif
                         </div>
                     </div>
                     @endif
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Основная информация</h2>
                     <!-- Основная информация -->
                     <div class="space-y-1">
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Текущий город:</span>
                             <span class="text-base font-medium">{{ $candidate->current_city }}</span>
                         </div>
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Готов к переезду:</span>
                             <span class="text-base font-medium">{{ $candidate->ready_to_relocate ? 'Да' : 'Нет' }}</span>
                         </div>
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Сфера деятельности:</span>
                             <span class="text-base font-medium flex-1">{{ $candidate->activity_sphere ?: 'Не указано' }}</span>
                         </div>
                        <div class="flex items-start">
                            <span class="w-60 text-base text-gray-600">Желаемая должность:</span>
                            <span class="text-base font-medium flex-1">{{ $candidate->desired_position ?: 'Не указано' }}</span>
                        </div>
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Ожидаемая заработная плата:</span>
                             <span class="text-base font-medium">{{ number_format($candidate->expected_salary) }} тг.</span>
                         </div>
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Дата рождения:</span>
                             <span class="text-base font-medium">{{ $candidate->birth_date?->format('d.m.Y') ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Место рождения:</span>
                             <span class="text-base font-medium flex-1">{{ $candidate->birth_place ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Пол:</span>
                             <span class="text-base font-medium">{{ $candidate->gender ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex">
                             <span class="w-60 text-base text-gray-600">Семейное положение:</span>
                             <span class="text-base font-medium">{{ $candidate->marital_status ?: 'Не указано' }}</span>
                         </div>
                         @php
                             $family = $candidate->getFamilyStructured();
                         @endphp

                         @if($isFullReport)
                         <!-- Дети -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Дети:</span>
                             <span class="text-base font-medium flex-1">
                                 @if(!empty($family['children']))
                                     @foreach($family['children'] as $child)
                                         <span>{{ $child['name'] ?? 'Не указано' }} - {{ $child['birth_year'] ?? 'Не указано' }}</span>
                                         @if(!$loop->last)<br>@endif
                                     @endforeach
                                 @else
                                     Не указано
                                 @endif
                             </span>
                         </div>
                         @endif

                        @if($isFullReport)
                        <!-- Родители -->
                        <div class="flex items-start">
                            <span class="w-60 text-base text-gray-600">Родители:</span>
                            <span class="text-base font-medium flex-1">
                                @if(!empty($family['parents']))
                                    @foreach($family['parents'] as $parent)
                                        <span>
                                            {{ $parent['relation'] ?? 'Не указано' }} - {{ $parent['birth_year'] ?? 'Не указано' }}
                                            @if(!empty($parent['profession']))
                                                - {{ $parent['profession'] }}
                                            @endif
                                        </span>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                        @endif

                         @if($isFullReport)
                         <!-- Братья и сестры -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Кол-во братьев-сестер:</span>
                             <span class="text-base font-medium flex-1">
                                 @if(!empty($family['siblings']))
                                     {{ count($family['siblings']) }}
                                     @foreach($family['siblings'] as $sibling)
                                         ({{ ($sibling['relation'] ?? 'Не указано') === 'Брат' ? 'Б' : 'С' }}{{ $sibling['birth_year'] ?? 'Не указано' }})
                                     @endforeach
                                 @else
                                     Не указано
                                 @endif
                             </span>
                         </div>
                         @endif

                        <!-- Школа -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Школа:</span>
                             <span class="text-base font-medium flex-1">{{ $candidate->school ?: 'Не указано' }}</span>
                         </div>

                        <!-- Образование -->
                        <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Образование:</span>
                             <span class="text-base font-medium flex-1">
                                @if($candidate->universities && count($candidate->universities) > 0)
                                    @foreach($candidate->universities as $index => $university)
                                        <span>
                                            <span class="font-medium">{{ $university['name'] ?? 'Не указано' }}</span> /
                                            <span class="font-medium">{{ $university['speciality'] ?? 'Не указано' }}</span> /
                                            <span>{{ $university['graduation_year'] ?? 'Не указано' }}</span>
                                            @if(!empty($university['gpa']))
                                                / <span>{{ $university['gpa'] }}</span>
                                            @endif
                                        </span>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @else
                                    Не указано
                                @endif
                            </span>
                         </div>
                    </div>
               </div>

               <div class="clear-both"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-3">
            <!-- Опыт работы -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Опыт работы</h2>
                @if($candidate->work_experience && count($candidate->work_experience) > 0)
                    <div class="space-y-1">
                        <div class="flex items-start">
                            <span class="w-60 text-base text-gray-600">Компании и должности:</span>
                            <div class="text-base font-medium flex-1 space-y-1">
                                @foreach($candidate->work_experience as $index => $experience)
                                    <div>
                                        {{ $index + 1 }}.
                                        @if(!empty($experience['years']))
                                            {{ $experience['years'] }} -
                                        @endif
                                        {{ $experience['company'] ?? 'Не указано' }}
                                        @if(!empty($experience['city']))
                                            ({{ $experience['city'] }})
                                        @endif
                                         - {{ $experience['position'] ?? 'Не указано' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex" style="margin-top: 1rem;">
                            <span class="w-60 text-base text-gray-600">Общий стаж работы (лет):</span>
                            <span class="text-base font-medium">{{ $candidate->total_experience_years ?? 0 }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-60 text-base text-gray-600">Любит свою работу на (из 5):</span>
                            <span class="text-base font-medium">{{ $candidate->job_satisfaction ?? 'Не указано' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-base text-gray-500">Опыт работы не указан</p>
                @endif
            </div>

            <!-- Прочая информация -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Прочая информация</h2>
                <div class="space-y-1">
                    @if($isFullReport)
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Религия:</span>
                        <span class="text-base font-medium">{{ $candidate->religion ?: 'Не указано' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Рел. практика:</span>
                        <span class="text-base font-medium">{{ $candidate->is_practicing ? 'Да' : 'Нет' }}</span>
                    </div>
                    @endif
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Водительские права:</span>
                        <span class="text-base font-medium">{{ $candidate->has_driving_license ? 'Есть' : 'Нет' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Хобби:</span>
                        <span class="text-base flex-1">{{ $candidate->hobbies ?: 'Не указано' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Интересы:</span>
                        <span class="text-base flex-1">{{ $candidate->interests && trim($candidate->interests) ? mb_convert_case(trim($candidate->interests), MB_CASE_TITLE, 'UTF-8') : 'Не указано' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Любимые виды спорта:</span>
                        <span class="text-base flex-1">
                            @if($candidate->favorite_sports)
                                @if(is_array($candidate->favorite_sports))
                                    {{ implode(', ', mb_convert_case(trim($candidate->favorite_sports), MB_CASE_TITLE, 'UTF-8')) }}
                                @else
                                    {{ mb_convert_case(trim($candidate->favorite_sports), MB_CASE_TITLE, 'UTF-8') }}
                                @endif
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Посещенные страны:</span>
                        <span class="text-base flex-1">
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
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Кол-во книг в год:</span>
                        <span class="text-base">{{ $candidate->books_per_year ?? 'Не указано' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Часы на разв. видео в неделю:</span>
                        <span class="text-base">
                            @if($candidate->entertainment_hours_weekly)
                                {{ $candidate->entertainment_hours_weekly }} час{{ $candidate->entertainment_hours_weekly == 1 ? '' : ($candidate->entertainment_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Часы на обра. видео в неделю:</span>
                        <span class="text-base">
                            @if($candidate->educational_hours_weekly)
                                {{ $candidate->educational_hours_weekly }} час{{ $candidate->educational_hours_weekly == 1 ? '' : ($candidate->educational_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-60 text-base text-gray-600">Часы на соц. сети в неделю:</span>
                        <span class="text-base">
                            @if($candidate->social_media_hours_weekly)
                                {{ $candidate->social_media_hours_weekly }} час{{ $candidate->social_media_hours_weekly == 1 ? '' : ($candidate->social_media_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Пожелания на рабочем месте:</span>
                        <span class="text-base flex-1">
                            @if($candidate->workplace_preferences)
                                {{ $candidate->workplace_preferences }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    @if($candidate->computer_skills)
                    <div class="flex items-start">
                        <span class="w-60 text-base text-gray-600">Компьютерные навыки:</span>
                        <span class="text-base flex-1">
                                {{ $candidate->computer_skills}}
                        </span>
                    </div>
                    @else
                    @endif
                </div>
            </div>

            <!-- Языковые навыки -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Языковые навыки</h2>
                @if($candidate->language_skills && count($candidate->language_skills) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->language_skills as $skill)
                            <div class="flex text-base">
                                <span class="w-32 font-medium">{{ $skill['language'] ?? 'Не указано' }}</span>
                                <span class="w-40">{{ $skill['level'] ?? 'Не указано' }}</span>
                                @if(isset($skill['additional_language']) && $skill['additional_language'])
                                    <span class="w-32 font-medium">{{ $skill['additional_language'] ?? '' }}</span>
                                    <span class="text-gray-600">-</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-base text-gray-500">Языковые навыки не указаны</p>
                @endif
            </div>

            <!-- Компьютерные навыки -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2"></h2>
                <p class="text-base text-gray-800"></p>
            </div>

            <!-- Психометрические данные -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Психометрические данные</h2>
                <div class="flex">
                    <span class="text-base text-gray-600 w-60">Тип личности по MBTI:</span>
                    <span class="text-base font-medium text-blue-600">{{ $candidate->mbti_type ?: 'Не указано' }}</span>
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
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Тест типов интеллекта (Гарднер)</h2>
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
                            <span class="text-base font-medium {{ $textClass }}">{{ $intelligenceType }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-24 h-2 bg-gray-200 rounded-full mr-3">
                                <div class="h-2 {{ $barClass }} rounded-full" style="width: {{ $percentage }}"></div>
                            </div>
                            <span class="text-base {{ $percentageClass }}">{{ $percentage }}</span>
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
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 p-4 text-center text-xs text-gray-500 no-print">
            <p>Отчет сгенерирован {{ now()->format('d.m.Y в H:i') }}</p>
        </div>
    </div>
</body>
</html>
