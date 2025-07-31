<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Панель') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-8">Добро пожаловать, {{ Auth::user()->name }}!</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Анкета -->
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                        <h2 class="text-xl font-semibold mb-4">Анкета</h2>
                        @if($candidate)
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <div class="w-3 h-3 rounded-full {{ $candidate->step >= 5 ? 'bg-green-500' : 'bg-yellow-500' }} mr-2"></div>
                                    <p class="text-gray-600">
                                        Статус: {{ $candidate->step >= 5 ? 'Завершена' : 'В процессе' }}
                                    </p>
                                </div>
                                @if($candidate->step < 5)
                                    <p class="text-sm text-gray-500">Текущий шаг: {{ $candidate->step }} из 4</p>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('candidate.form', ['id' => $candidate->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                    Изменить анкету
                                </a>
                                @if($candidate->step >= 5 && $gardnerTest)
                                    <a href="{{ route('candidate.report', $candidate) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                        Посмотреть отчет
                                    </a>
                                @endif
                            </div>
                            @if($candidate->step >= 5 && !$gardnerTest)
                                <p class="text-sm text-amber-600 bg-amber-50 p-3 rounded border border-amber-200">
                                    ⚠️ Чтобы посмотреть отчет анкеты, необходимо пройти тест Гарднера.
                                </p>
                           @endif
                            @if($lastUpdate)
                                <p class="mt-2 text-sm text-gray-500">обновлено в {{ $lastUpdate }}</p>
                            @endif
                        @else
                        <p class="text-gray-600 mb-4">Заполните анкету для участия в отборе.</p>
                        <a href="{{ route('candidate.form') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring focus:ring-orange-200 disabled:opacity-25 transition">
                            Создать анкету
                        </a>
                        @endif
                    </div>

                    <!-- Тест Гарднера -->
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                        <h2 class="text-xl font-semibold mb-4">Тест Гарднера</h2>
                        @if($gardnerTest)
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <p class="text-gray-600">Статус: Завершен</p>
                                </div>
{{--                                @php--}}
{{--                                    $results = is_string($gardnerTest->results) ? json_decode($gardnerTest->results, true) : $gardnerTest->results;--}}
{{--                                    $maxScore = max($results);--}}
{{--                                    $dominantType = array_search($maxScore, $results);--}}
{{--                                    $typeNames = [--}}
{{--                                        'linguistic' => 'Лингвистический',--}}
{{--                                        'logical_mathematical' => 'Логико-математический',--}}
{{--                                        'spatial' => 'Пространственный',--}}
{{--                                        'musical' => 'Музыкальный',--}}
{{--                                        'bodily_kinesthetic' => 'Телесно-кинестетический',--}}
{{--                                        'intrapersonal' => 'Внутриличностный',--}}
{{--                                        'interpersonal' => 'Межличностный',--}}
{{--                                        'naturalistic' => 'Натуралистический',--}}
{{--                                    ];--}}
{{--                                @endphp--}}
{{--                                <p class="text-sm text-gray-500 mb-2">--}}
{{--                                    Доминирующий тип: {{ $typeNames[$dominantType] ?? 'Неизвестен' }}--}}
{{--                                </p>--}}
                            </div>
                            <a href="{{ route('candidate.test') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition">
                                Посмотреть результаты
                            </a>
                            <p class="mt-2 text-sm text-gray-500">
                                завершен {{ $gardnerTest->created_at->format('d.m.Y H:i') }}
                            </p>
                        @else
                            <p class="text-gray-600 mb-4">Пройдите тест множественного интеллекта.</p>
                            <a href="{{ route('candidate.test') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition">
                                Пройти тест
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
