<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            @if(!$isCompleted)
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Тест Гарднера - Множественные интеллекты</h1>
                        <div class="text-sm text-gray-500">
                            Все {{ $totalQuestions }} вопросов
                        </div>
                    </div>

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @php
                        $answeredCount = count(array_filter($answers, fn($x) => $x !== null));
                        $allAnswered = !in_array(null, $answers);
                        $progressPercentage = ($answeredCount / $totalQuestions) * 100;
                    @endphp
                    
                    <div class="mb-6 sticky top-0 bg-white p-4 border-b z-10">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                Прогресс: {{ $answeredCount }} из {{ $totalQuestions }}
                            </span>
                            <span class="text-sm font-medium text-gray-700">{{ round($progressPercentage, 1) }}%</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        
                        @if($allAnswered)
                            <div class="flex justify-center">
                                <button type="button" 
                                        wire:click="submitTest"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                                    🎉 Завершить тест
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                        Вопрос {{ $index + 1 }}
                                    </span>
                                    <span class="text-gray-900 font-medium">{{ $question['text'] }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach([5 => 'Полностью согласен', 4 => 'Согласен', 3 => 'Частично согласен', 2 => 'Не согласен', 1 => 'Совершенно не согласен'] as $value => $label)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer {{ isset($answers[$index]) && $answers[$index] == $value ? 'bg-blue-100 border-blue-500' : '' }}">
                                            <input type="radio" 
                                                   name="question_{{ $index }}" 
                                                   value="{{ $value }}"
                                                   wire:click="selectAnswerByIndex({{ $index }}, {{ $value }})"
                                                   {{ isset($answers[$index]) && $answers[$index] == $value ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <span class="ml-3 text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 text-center">
                        @if($allAnswered)
                            <button type="button" 
                                    wire:click="submitTest"
                                    class="inline-flex items-center px-8 py-4 bg-green-600 border border-transparent rounded-lg font-semibold text-lg text-white uppercase tracking-wider hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                                🎉 Завершить тест и получить результаты
                            </button>
                        @else
                            <div class="bg-gray-100 rounded-lg p-6">
                                <p class="text-gray-600 mb-2">Для завершения теста ответьте на все {{ $totalQuestions }} вопросов</p>
                                <p class="text-sm text-gray-500">Осталось: {{ $totalQuestions - $answeredCount }} вопросов</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Результаты теста -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Результаты теста Гарднера</h1>
                        <button type="button" 
                                wire:click="retakeTest"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 transition">
                            Пройти заново
                        </button>
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        @php
                            // Поскольку результаты теперь сохраняются с русскими названиями и процентами,
                            // нам нужно обработать их по-другому
                            $descriptions = [
                                'Лингвистический интеллект' => 'Способность эффективно использовать слова и язык. Люди с развитым лингвистическим интеллектом хорошо выражают свои мысли устно и письменно.',
                                'Логико-математический интеллект' => 'Способность эффективно использовать числа и логически рассуждать. Включает чувствительность к логическим схемам и отношениям.',
                                'Пространственный интеллект' => 'Способность воспринимать зрительно-пространственный мир и преобразовывать зрительно-пространственные представления.',
                                'Музыкальный интеллект' => 'Способность воспринимать, различать, преобразовывать и выражать музыкальные формы.',
                                'Телесно-кинестетический интеллект' => 'Способность использовать все тело или части тела для решения проблем или создания продуктов.',
                                'Внутриличностный интеллект' => 'Способность понимать себя, свои чувства, страхи и мотивации.',
                                'Межличностный интеллект' => 'Способность воспринимать и проводить различия в настроениях, намерениях, мотивациях и чувствах других людей.',
                                'Натуралистический интеллект' => 'Способность распознавать и классифицировать различные виды флоры и фауны в окружающей среде.',
                                'Экзистенциальный интеллект' => 'Способность размышлять над фундаментальными вопросами человеческого существования, смыслом жизни, духовностью и философскими концепциями.'
                            ];
                            
                            // Находим максимальный процент
                            $percentages = [];
                            foreach($results as $name => $percentageStr) {
                                $percentages[$name] = (int) str_replace('%', '', $percentageStr);
                            }
                            $maxPercentage = max($percentages);
                        @endphp

                        @foreach($results as $name => $percentageStr)
                            @php
                                $percentage = (int) str_replace('%', '', $percentageStr);
                                $isHighest = $percentage == $maxPercentage;
                            @endphp
                            
                            <div class="bg-gray-50 p-4 rounded-lg {{ $isHighest ? 'ring-2 ring-green-500 bg-green-50' : '' }}">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-semibold text-gray-900 {{ $isHighest ? 'text-green-900' : '' }}">
                                        {{ $name }}
                                        @if($isHighest)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                Доминирующий
                                            </span>
                                        @endif
                                    </h3>
                                    <span class="text-sm font-medium {{ $isHighest ? 'text-green-700' : 'text-gray-600' }}">
                                        {{ $percentageStr }}
                                    </span>
                                </div>
                                
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $isHighest ? 'bg-green-600' : 'bg-indigo-600' }}" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                
                                <p class="text-sm text-gray-600">{{ $descriptions[$name] ?? 'Описание недоступно' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-blue-900 mb-2">Интерпретация результатов</h3>
                        <p class="text-sm text-blue-800">
                            Ваши результаты показывают относительную силу различных типов интеллекта. 
                            Доминирующие типы интеллекта указывают на ваши естественные склонности и способности. 
                            Помните, что все типы интеллекта важны и могут быть развиты.
                        </p>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 transition">
                            ← Вернуться в панель
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> 