@if($currentStep === 3)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Образование и работа</h2>

    <div class="grid grid-cols-1 gap-6">
        <!-- Школа -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Школа <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 mt-1 mb-2">
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Формат: Название школы / город / год окончания
                </span>
            </p>
            <input type="text" 
                   wire:model="school" 
                   placeholder="Например: Школа №25 / Алматы / 2018"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('school') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Университеты -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Университет | Колледж</label>
            <div class="space-y-4">
                @foreach($universities as $index => $university)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Название университета | колледжа <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="universities.{{ $index }}.name" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Специальность <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="universities.{{ $index }}.speciality" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.speciality") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Год окончания <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="universities.{{ $index }}.graduation_year" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="" selected disabled>Выберите год</option>
                                    @for($year = 1970; $year <= 2035; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error("universities.{$index}.graduation_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    GPA <span class="text-gray-500">(не обязательно)</span>
                                </label>
                                <input type="number" 
                                       wire:model="universities.{{ $index }}.gpa"
                                       name="universities[{{ $index }}][gpa]"
                                       min="0" 
                                       max="4.0" 
                                       step="0.01"
                                       placeholder="например: 3.75"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Введите значение от 0 до 4.0</p>
                                @error("universities.{$index}.gpa") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeUniversity({{ $index }})" class="text-red-600 hover:text-red-800">
                                Удалить
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button" 
                        wire:click="addUniversity" 
                        class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Добавить университет
                </button>
            </div>
        </div>

        <!-- Опыт работы -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Опыт работы</label>
            <div class="space-y-4">
                @foreach($work_experience as $index => $experience)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Левая колонка: Период работы -->
                            <div>                                
                                <!-- Период работы с select'ами -->
                                <div class="bg-gradient-to-r from-blue-50 to-green-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm font-medium text-gray-600">Период работы</span>
                                        <span id="period-display-{{ $index }}" class="px-3 py-1 bg-white text-gray-800 text-sm font-medium rounded-full shadow-sm">
                                            {{ $experience['years'] ?? 'Выберите период' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Начало работы -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-blue-700">Начало работы</label>
                                            <span id="start-display-{{ $index }}" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                не выбрано
                                            </span>
                                        </div>
                                        
                                        <!-- Select'ы для начала работы -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Месяц начала -->
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.start_month" 
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>Месяц</option>
                                                    <option value="0">Январь</option>
                                                    <option value="1">Февраль</option>
                                                    <option value="2">Март</option>
                                                    <option value="3">Апрель</option>
                                                    <option value="4">Май</option>
                                                    <option value="5">Июнь</option>
                                                    <option value="6">Июль</option>
                                                    <option value="7">Август</option>
                                                    <option value="8">Сентябрь</option>
                                                    <option value="9">Октябрь</option>
                                                    <option value="10">Ноябрь</option>
                                                    <option value="11">Декабрь</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Год начала -->
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.start_year" 
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>Год</option>
                                                    @for($year = 1990; $year <= 2025; $year++)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Окончание работы -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-green-700">Окончание работы</label>
                                            <span id="end-display-{{ $index }}" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                                не выбрано
                                            </span>
                                        </div>
                                        
                                        <!-- Select'ы для окончания работы -->
                                        <div class="grid grid-cols-2 gap-2" id="end-period-selects-{{ $index }}">
                                            <!-- Месяц окончания -->
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.end_month" 
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>Месяц</option>
                                                    <option value="0">Январь</option>
                                                    <option value="1">Февраль</option>
                                                    <option value="2">Март</option>
                                                    <option value="3">Апрель</option>
                                                    <option value="4">Май</option>
                                                    <option value="5">Июнь</option>
                                                    <option value="6">Июль</option>
                                                    <option value="7">Август</option>
                                                    <option value="8">Сентябрь</option>
                                                    <option value="9">Октябрь</option>
                                                    <option value="10">Ноябрь</option>
                                                    <option value="11">Декабрь</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Год окончания -->
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.end_year" 
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>Год</option>
                                                    @for($year = 1990; $year <= 2025; $year++)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Текущая работа -->
                                <div class="flex items-center mt-3">
                                    <input type="checkbox" 
                                           wire:model="work_experience.{{ $index }}.is_current"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           onchange="toggleCurrentWork({{ $index }})">
                                    <label class="ml-2 text-sm text-gray-700">Работаю по настоящее время</label>
                                </div>
                                
                                <!-- Скрытое поле для сохранения -->
                                <input type="hidden" 
                                       wire:model="work_experience.{{ $index }}.years"
                                       id="period-hidden-{{ $index }}">
                                
                                @error("work_experience.{$index}.years") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Правая колонка: Информация о компании -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Название компании <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="work_experience.{{ $index }}.company" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.company") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Город <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="work_experience.{{ $index }}.city" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.city") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Должность <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="work_experience.{{ $index }}.position" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.position") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeWorkExperience({{ $index }})" class="text-red-600 hover:text-red-800">
                                Удалить
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button" 
                        wire:click="addWorkExperience" 
                        class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-indigo-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Добавить место работы
                </button>
            </div>
        </div>

        <!-- Языковые навыки -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700">Языковые навыки <span class="text-red-500">*</span></label>
            <div class="space-y-4">
                @foreach($language_skills as $index => $skill)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Язык</label>
                                <div wire:ignore>
                                    <select id="language-select-{{ $index }}" 
                                            class="language-select-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            data-index="{{ $index }}">
                                        <option value="">Выберите язык</option>
                                        @foreach($languages ?? [] as $language)
                                            <option value="{{ $language }}" {{ ($skill['language'] ?? '') == $language ? 'selected' : '' }}>
                                                {{ $language }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error("language_skills.{$index}.language") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Уровень</label>
                                <select wire:model="language_skills.{{ $index }}.level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Выберите уровень</option>
                                    <option value="Начальный">Начальный</option>
                                    <option value="Средний">Средний</option>
                                    <option value="Выше среднего">Выше среднего</option>
                                    <option value="Продвинутый">Продвинутый</option>
                                    <option value="В совершенстве">В совершенстве</option>
                                </select>
                                @error("language_skills.{$index}.level") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeLanguage({{ $index }})" class="text-red-600 hover:text-red-800">
                                Удалить
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button" 
                        wire:click="addLanguage" 
                        class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    Добавить язык
                </button>
            </div>
        </div>

        <!-- Желаемая должность, Сфера деятельности и Ожидаемая зарплата -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

            <!-- Сфера деятельности -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Сфера деятельности <span class="text-red-500">*</span>
                </label>
                <select wire:model="activity_sphere" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите сферу деятельности</option>
                    @foreach($activitySpheres as $key => $sphere)
                        <option value="{{ $sphere }}">{{ $sphere }}</option>
                    @endforeach
                </select>
                @error('activity_sphere') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Желаемая должность -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Желаемая должность <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       wire:model="desired_position" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Пример: Финансовый аналитик">
                @error('desired_position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>



            <!-- Ожидаемая зарплата -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Ожидаемая зарплата (тенге) <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input type="text" 
                           id="expected_salary_formatted"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-12"
                           placeholder="500 000"
                           autocomplete="off"
                           oninput="formatSalary(this)"
                           onpaste="handleSalaryPaste(event)"
                           onkeypress="return allowOnlyNumbers(event)"
                           onfocus="initializeSalaryField()"
                           onblur="initializeSalaryField()">
                    <input type="hidden" 
                           wire:model="expected_salary" 
                           id="expected_salary_hidden">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₸</span>
                    </div>
                </div>
                

                <p class="mt-1 text-xs text-gray-500">Введите сумму без копеек, например: 500 000</p>
                @error('expected_salary') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <!-- Компьютерные навыки и Требования к работодателю в одном ряду -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Компьютерные навыки -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Компьютерные навыки <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Пример: Word, Excel, PowerPoint, Photoshop, 1C, итд.
                    </span>
                </p>
                <textarea wire:model="computer_skills" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('computer_skills') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Требования к работодателю -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Требования к работодателю <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Пример: намазхана, ходить в платке, гибкий график, итд.
                    </span>
                </p>
                <textarea wire:model="employer_requirements" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('employer_requirements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        

        <!-- Опыт работы, удовлетворенность и зарплата в одном ряду -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Общий стаж работы -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">
                        Общий стаж работы (лет) <span class="text-red-500">*</span>
                    </label>
                    <span id="experience-display" class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-md">
                        @if($total_experience_years && $total_experience_years > 0)
                            {{ $total_experience_years }}
                        @else
                            0
                        @endif
                    </span>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           id="experience-slider"
                           wire:model="total_experience_years"
                           name="total_experience_years"
                           min="0" 
                           max="50" 
                           step="1"
                           value="{{ $total_experience_years ?? 0 }}"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-green-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-green-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('total_experience_years') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Удовлетворенность работой -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">
                        Удовлетворенность текущей работой (1-5)
                    </label>
                    <span id="satisfaction-display" class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-md">
                        @if($job_satisfaction && $job_satisfaction > 1)
                            {{ $job_satisfaction }}
                        @else
                            1
                        @endif
                    </span>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="job_satisfaction"
                           name="job_satisfaction"
                           value="{{ $job_satisfaction ?? 1 }}"
                           min="1" 
                           max="5" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-blue-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-blue-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('job_satisfaction') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>


        </div>
    </div>
</div>
@endif 


<script>
// Массивы месяцев на русском языке
const months = [
    'январь', 'февраль', 'март', 'апрель', 'май', 'июнь',
    'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'
];

// Функция для обновления отображения периода
window.updatePeriodDisplay = function(index) {
    const startMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.start_month"]`);
    const startYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.start_year"]`);
    const endMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_month"]`);
    const endYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_year"]`);
    const isCurrentCheckbox = document.querySelector(`input[wire\\:model="work_experience.${index}.is_current"]`);
    const hiddenField = document.getElementById(`period-hidden-${index}`);
    const displayElement = document.getElementById(`period-display-${index}`);
    const startDisplayElement = document.getElementById(`start-display-${index}`);
    const endDisplayElement = document.getElementById(`end-display-${index}`);
    
    if (!startMonthSelect || !startYearSelect || !endMonthSelect || !endYearSelect || !hiddenField || !displayElement) {
        return;
    }
    
    // Получаем значения и проверяем их валидность
    const startMonthValue = startMonthSelect.value;
    const startYearValue = startYearSelect.value;
    const endMonthValue = endMonthSelect.value;
    const endYearValue = endYearSelect.value;
    const isCurrent = isCurrentCheckbox ? isCurrentCheckbox.checked : false;
    
    // Проверяем, что значения не пустые и валидные
    const startMonth = startMonthValue !== '' ? parseInt(startMonthValue) : null;
    const startYear = startYearValue !== '' ? parseInt(startYearValue) : null;
    const endMonth = endMonthValue !== '' ? parseInt(endMonthValue) : null;
    const endYear = endYearValue !== '' ? parseInt(endYearValue) : null;
    
    // Формируем строки периода
    let startPeriod = '';
    let endPeriod = '';
    
    // Формируем период начала работы
    if (startMonth !== null && startYear !== null && !isNaN(startMonth) && !isNaN(startYear)) {
        startPeriod = `${months[startMonth]} ${startYear}`;
    }
    
    // Формируем период окончания работы
    if (isCurrent) {
        endPeriod = 'настоящее время';
    } else if (endMonth !== null && endYear !== null && !isNaN(endMonth) && !isNaN(endYear)) {
        endPeriod = `${months[endMonth]} ${endYear}`;
    }
    
    // Обновляем основное отображение периода
    if (startPeriod && endPeriod) {
        displayElement.textContent = `${startPeriod} - ${endPeriod}`;
    } else if (startPeriod) {
        displayElement.textContent = startPeriod;
    } else {
        displayElement.textContent = 'Выберите период';
    }
    
    // Обновляем отдельные отображения
    if (startDisplayElement) {
        startDisplayElement.textContent = startPeriod || 'не выбрано';
    }
    if (endDisplayElement) {
        endDisplayElement.textContent = endPeriod || 'не выбрано';
    }
    
    // Обновляем скрытое поле для Livewire
    if (startPeriod && endPeriod) {
        hiddenField.value = `${startPeriod} - ${endPeriod}`;
    } else {
        hiddenField.value = '';
    }
    
    // Уведомляем Livewire об изменении
    hiddenField.dispatchEvent(new Event('input', { bubbles: true }));
}

// Функция для обработки чекбокса "Работаю по настоящее время"
window.toggleCurrentWork = function(index) {
    const isCurrentCheckbox = document.querySelector(`input[wire\\:model="work_experience.${index}.is_current"]`);
    const endMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_month"]`);
    const endYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_year"]`);
    const endPeriodSelects = document.getElementById(`end-period-selects-${index}`);
    
    if (isCurrentCheckbox && endMonthSelect && endYearSelect && endPeriodSelects) {
        if (isCurrentCheckbox.checked) {
            // Если выбрано "по настоящее время", отключаем select'ы
            endMonthSelect.disabled = true;
            endYearSelect.disabled = true;
            endPeriodSelects.style.opacity = '0.5';
            endPeriodSelects.style.pointerEvents = 'none';
        } else {
            // Если снято "по настоящее время", включаем select'ы
            endMonthSelect.disabled = false;
            endYearSelect.disabled = false;
            endPeriodSelects.style.opacity = '1';
            endPeriodSelects.style.pointerEvents = 'auto';
        }
    }
    
    updatePeriodDisplay(index);
}

// Глобальные функции для форматирования зарплаты
window.formatSalary = function(input) {
    console.log('formatSalary called with:', input.value);
    
    // Получаем только цифры
    let numericValue = input.value.replace(/\D/g, '');
    console.log('Numeric value:', numericValue);
    
    // Форматируем с пробелами
    let formatted = '';
    if (numericValue) {
        formatted = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    
    console.log('Formatted value:', formatted);
    
    // Устанавливаем отформатированное значение
    input.value = formatted;
    
    // Обновляем скрытое поле
    const hiddenInput = document.getElementById('expected_salary_hidden');
    if (hiddenInput) {
        hiddenInput.value = numericValue;
        // Уведомляем Livewire
        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.allowOnlyNumbers = function(event) {
    const key = event.key;
    console.log('Key pressed:', key);
    
    // Разрешаем цифры
    if (key >= '0' && key <= '9') {
        return true;
    }
    
    // Разрешаем служебные клавиши
    if (['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(key)) {
        return true;
    }
    
    // Блокируем все остальное
    console.log('Blocked key:', key);
    event.preventDefault();
    return false;
};

window.handleSalaryPaste = function(event) {
    event.preventDefault();
    console.log('Paste event triggered');
    
    // Получаем вставляемый текст
    const paste = (event.clipboardData || window.clipboardData).getData('text');
    console.log('Pasted text:', paste);
    
    // Извлекаем только цифры
    const numericOnly = paste.replace(/\D/g, '');
    console.log('Numeric from paste:', numericOnly);
    
    if (numericOnly) {
        // Устанавливаем значение и форматируем
        const input = event.target;
        input.value = numericOnly;
        window.formatSalary(input);
    }
};

// Функция инициализации поля зарплаты
function initializeSalaryField() {
    console.log('=== Initializing salary field ===');
    
    const formattedInput = document.getElementById('expected_salary_formatted');
    const hiddenInput = document.getElementById('expected_salary_hidden');
    
    console.log('Formatted input found:', !!formattedInput);
    console.log('Hidden input found:', !!hiddenInput);
    
    if (formattedInput && hiddenInput) {
        console.log('Hidden input value:', hiddenInput.value);
        console.log('Hidden input value type:', typeof hiddenInput.value);
        console.log('Hidden input value length:', hiddenInput.value ? hiddenInput.value.length : 0);
        
        // Проверяем wire:model значение через Livewire
        if (window.Livewire) {
            const component = window.Livewire.find(hiddenInput.closest('[wire\\:id]')?.getAttribute('wire:id'));
            if (component && component.data.expected_salary) {
                console.log('Livewire expected_salary:', component.data.expected_salary);
                
                let value = component.data.expected_salary.toString();
                if (value.includes('.')) {
                    value = value.split('.')[0];
                }
                
                if (value && value !== '0') {
                    const formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                    formattedInput.value = formatted;
                    console.log('Set from Livewire data:', formatted);
                    return;
                }
            }
        }
        
        if (hiddenInput.value && hiddenInput.value !== '0' && hiddenInput.value !== '') {
            // Убираем десятичную часть (.00) если есть
            let value = hiddenInput.value.toString();
            if (value.includes('.')) {
                value = value.split('.')[0];
            }
            
            console.log('Cleaned value:', value);
            
            // Форматируем и устанавливаем значение
            const formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            formattedInput.value = formatted;
            
            console.log('Set formatted value:', formatted);
        } else {
            console.log('No valid value to format');
        }
    } else {
        console.log('Salary inputs not found');
    }
    console.log('=== End salary field initialization ===');
}

// Функция инициализации периодов работы
function initializeWorkPeriods() {
    console.log('=== Initializing work periods ===');
    
    // Находим все элементы периодов работы
    const periodDisplays = document.querySelectorAll('[id^="period-display-"]');
    
    periodDisplays.forEach(function(displayElement) {
        const index = displayElement.id.replace('period-display-', '');
        console.log('Initializing period for index:', index);
        
        // Вызываем updatePeriodDisplay для каждого элемента
        if (typeof updatePeriodDisplay === 'function') {
            updatePeriodDisplay(index);
        }
        
        // Инициализируем состояние чекбокса "Работаю по настоящее время"
        initializeCurrentWorkCheckbox(index);
    });
    
    console.log('=== End work periods initialization ===');
}

// Функция инициализации чекбокса "Работаю по настоящее время"
function initializeCurrentWorkCheckbox(index) {
    const isCurrentCheckbox = document.querySelector(`input[wire\\:model="work_experience.${index}.is_current"]`);
    const endMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_month"]`);
    const endYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_year"]`);
    const endPeriodSelects = document.getElementById(`end-period-selects-${index}`);
    
    if (isCurrentCheckbox && endMonthSelect && endYearSelect && endPeriodSelects) {
        console.log('Initializing current work checkbox for index:', index, 'checked:', isCurrentCheckbox.checked);
        
        if (isCurrentCheckbox.checked) {
            // Если чекбокс отмечен, отключаем поля окончания работы
            endMonthSelect.disabled = true;
            endYearSelect.disabled = true;
            endPeriodSelects.style.opacity = '0.5';
            endPeriodSelects.style.pointerEvents = 'none';
        } else {
            // Если чекбокс не отмечен, включаем поля окончания работы
            endMonthSelect.disabled = false;
            endYearSelect.disabled = false;
            endPeriodSelects.style.opacity = '1';
            endPeriodSelects.style.pointerEvents = 'auto';
        }
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    setTimeout(initializeSalaryField, 100);
    setTimeout(initializeSalaryField, 500);
    setTimeout(initializeSalaryField, 1000);
    setTimeout(initializeSalaryField, 2000);
    
    // Инициализируем отображение периодов работы
    setTimeout(initializeWorkPeriods, 100);
    setTimeout(initializeWorkPeriods, 500);
});

// Инициализация после загрузки Livewire
document.addEventListener('livewire:load', function() {
    console.log('Livewire loaded');
    setTimeout(initializeSalaryField, 100);
    setTimeout(initializeSalaryField, 500);
    setTimeout(initializeWorkPeriods, 100);
    setTimeout(initializeWorkPeriods, 500);
});

// Инициализация после обновлений Livewire
document.addEventListener('livewire:update', function() {
    console.log('Livewire updated');
    setTimeout(initializeSalaryField, 100);
    setTimeout(initializeWorkPeriods, 100);
    
    // Дополнительно инициализируем чекбоксы текущей работы
    setTimeout(function() {
        const checkboxes = document.querySelectorAll('input[wire\\:model*="work_experience"][wire\\:model*="is_current"]');
        checkboxes.forEach(function(checkbox) {
            const wireModel = checkbox.getAttribute('wire:model');
            const index = wireModel.match(/work_experience\.(\d+)\.is_current/);
            if (index) {
                initializeCurrentWorkCheckbox(index[1]);
            }
        });
    }, 150);
});

// Для новых версий Livewire
if (typeof Livewire !== 'undefined') {
    Livewire.hook('message.processed', (message, component) => {
        console.log('Livewire message processed');
        setTimeout(initializeSalaryField, 100);
        setTimeout(initializeWorkPeriods, 100);
        
        // Инициализируем чекбоксы текущей работы
        setTimeout(function() {
            const checkboxes = document.querySelectorAll('input[wire\\:model*="work_experience"][wire\\:model*="is_current"]');
            checkboxes.forEach(function(checkbox) {
                const wireModel = checkbox.getAttribute('wire:model');
                const index = wireModel.match(/work_experience\.(\d+)\.is_current/);
                if (index) {
                    initializeCurrentWorkCheckbox(index[1]);
                }
            });
        }, 150);
    });
}

// Дополнительная инициализация при фокусе на поле
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'expected_salary_formatted') {
        console.log('Salary field clicked, attempting initialization');
        initializeSalaryField();
    }
});

// Инициализация при изменении window
window.addEventListener('load', function() {
    console.log('Window loaded');
    setTimeout(initializeSalaryField, 500);
    setTimeout(initializeSalaryField, 1000);
});

// Наблюдатель за изменениями DOM для автоматической инициализации
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            const salaryField = document.getElementById('expected_salary_formatted');
            if (salaryField && !salaryField.hasAttribute('data-initialized')) {
                console.log('Salary field detected in DOM, initializing...');
                salaryField.setAttribute('data-initialized', 'true');
                setTimeout(initializeSalaryField, 100);
                setTimeout(initializeSalaryField, 500);
                setTimeout(initializeSalaryField, 1000);
            }
        }
    });
});

// Запускаем наблюдатель
observer.observe(document.body, {
    childList: true,
    subtree: true
});

// Инициализация при видимости элемента
const checkVisibilityAndInit = function() {
    const salaryField = document.getElementById('expected_salary_formatted');
    if (salaryField) {
        const rect = salaryField.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
            console.log('Salary field is visible, initializing...');
            initializeSalaryField();
            return true;
        }
    }
    return false;
};

// Проверяем видимость каждые 500ms в течение первых 10 секунд
let visibilityCheckCount = 0;
const visibilityInterval = setInterval(function() {
    visibilityCheckCount++;
    if (checkVisibilityAndInit() || visibilityCheckCount >= 20) {
        clearInterval(visibilityInterval);
    }
}, 500);
</script>

<!-- Select2 CSS для языков (загружается только на шаге 3) -->
@if($currentStep === 3)
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Кастомные стили для Select2 языков -->
<style>
/* Контейнер Select2 для языков */
.select2-container--default .select2-selection--single {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    height: 38px;
    padding: 4px 8px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
    color: #374151;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Стили при фокусе */
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Dropdown */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6;
    color: white;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 6px 12px;
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Dropdown результаты */
.select2-results__option {
    padding: 8px 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Placeholder */
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af;
}

/* Width fix */
.select2-container {
    width: 100% !important;
}

/* Dropdown container - убираем горизонтальный скролл */
.select2-dropdown {
    overflow-x: hidden !important;
}

.select2-results__options {
    overflow-x: hidden !important;
}

.select2-results__option {
    white-space: normal !important;
    word-wrap: break-word !important;
}
</style>
@endif

<!-- jQuery и Select2 JS для языков (загружается всегда, но проверяем наличие jQuery) -->
<script>
// Загружаем jQuery только если его еще нет
if (typeof jQuery === 'undefined') {
    console.log('📦 Loading jQuery for Language Select2...');
    const jQueryScript = document.createElement('script');
    jQueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    jQueryScript.onload = function() {
        console.log('✅ jQuery loaded for Language Select2');
        loadSelect2ForLanguages();
    };
    document.head.appendChild(jQueryScript);
} else {
    console.log('✅ jQuery already loaded, proceeding with Language Select2');
    loadSelect2ForLanguages();
}

function loadSelect2ForLanguages() {
    // Загружаем Select2 только если его еще нет
    if (typeof $.fn.select2 === 'undefined') {
        console.log('📦 Loading Select2 library for languages...');
        const select2Script = document.createElement('script');
        select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
        select2Script.onload = function() {
            console.log('✅ Select2 library loaded for languages');
            initLanguageSelect2System();
        };
        document.head.appendChild(select2Script);
    } else {
        console.log('✅ Select2 already loaded, initializing language fields');
        initLanguageSelect2System();
    }
}

function initLanguageSelect2System() {
    console.log('🌐 Language Select2 system initialization started');
    
    // Функция инициализации Select2 для всех полей языков
    window.initLanguageSelect2 = function() {
        console.log('✨ Initializing Language Select2 fields');
        
        // Находим все поля языка
        const languageSelects = document.querySelectorAll('.language-select-field');
        console.log(`📋 Found ${languageSelects.length} language select fields`);
        
        if (languageSelects.length === 0) {
            console.log('ℹ️ No language select fields found (probably not on step 3)');
            return;
        }
        
        languageSelects.forEach(function(selectElement) {
            const $select = $(selectElement);
            
            // Проверяем, не инициализирован ли уже Select2
            if ($select.hasClass('select2-hidden-accessible')) {
                console.log(`ℹ️ Select2 already initialized for ${selectElement.id}`);
                return;
            }
            
            const index = selectElement.getAttribute('data-index');
            console.log(`🔄 Initializing Select2 for language-select-${index}`);
            
            try {
                // Инициализируем Select2
                $select.select2({
                    placeholder: 'Выберите язык',
                    allowClear: false,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Язык не найден";
                        },
                        searching: function() {
                            return "Поиск...";
                        }
                    }
                });
                
                // Обработчик выбора языка
                $select.on('select2:select', function(e) {
                    const selectedLanguage = e.params.data.id;
                    console.log(`✅ Language selected for index ${index}:`, selectedLanguage);
                    
                    // Находим компонент Livewire
                    const livewireComponent = selectElement.closest('[wire\\:id]');
                    if (livewireComponent && window.Livewire) {
                        const componentId = livewireComponent.getAttribute('wire:id');
                        const component = window.Livewire.find(componentId);
                        
                        if (component) {
                            // Обновляем значение в Livewire
                            component.set(`language_skills.${index}.language`, selectedLanguage);
                            console.log(`🔄 Updated Livewire: language_skills.${index}.language = ${selectedLanguage}`);
                        } else {
                            console.error('❌ Livewire component not found');
                        }
                    } else {
                        console.error('❌ Livewire component element not found');
                    }
                });
                
                console.log(`✅ Select2 initialized successfully for language-select-${index}`);
            } catch (error) {
                console.error(`❌ Error initializing Select2 for language-select-${index}:`, error);
            }
        });
    };
    
    // Инициализация при загрузке
    setTimeout(() => window.initLanguageSelect2(), 100);
    setTimeout(() => window.initLanguageSelect2(), 300);
    setTimeout(() => window.initLanguageSelect2(), 500);
    setTimeout(() => window.initLanguageSelect2(), 1000);
    
    // Слушаем Livewire событие смены шага
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('step-changed', (event) => {
                console.log('🔄 Step changed event received (Language Select2):', event);
                const step = event.step || event[0]?.step || event[0];
                console.log('📍 Current step:', step);
                
                if (step === 3) {
                    console.log('✅ Moved to step 3, will initialize Language Select2');
                    setTimeout(() => window.initLanguageSelect2(), 100);
                    setTimeout(() => window.initLanguageSelect2(), 300);
                    setTimeout(() => window.initLanguageSelect2(), 500);
                    setTimeout(() => window.initLanguageSelect2(), 800);
                }
            });
            
            console.log('✅ Livewire event listener registered for languages');
        });
        
        // Переинициализация при обновлении Livewire (ловит добавление/удаление языков)
        Livewire.hook('message.processed', (message, component) => {
            // Пробуем с разными задержками для надежности
            const delays = [50, 100, 200, 300, 500];
            
            delays.forEach(delay => {
                setTimeout(() => {
                    // Проверяем наличие неинициализированных полей языка
                    const languageSelects = document.querySelectorAll('.language-select-field');
                    
                    if (languageSelects.length === 0) {
                        return; // Нет полей - выходим
                    }
                    
                    let hasUninitialized = false;
                    
                    languageSelects.forEach(function(selectElement) {
                        const $select = $(selectElement);
                        if (!$select.hasClass('select2-hidden-accessible')) {
                            hasUninitialized = true;
                        }
                    });
                    
                    if (hasUninitialized) {
                        console.log(`🔄 Livewire message.processed (delay ${delay}ms): Initializing uninitialized Language Select2 fields`);
                        window.initLanguageSelect2();
                    }
                }, delay);
            });
        });
    }
    
    // Дополнительный механизм: следим за изменениями DOM постоянно
    setInterval(() => {
        const languageSelects = document.querySelectorAll('.language-select-field');
        
        if (languageSelects.length === 0) {
            return; // Нет полей - выходим
        }
        
        let needsInit = false;
        
        languageSelects.forEach(function(selectElement) {
            const $select = $(selectElement);
            if (!$select.hasClass('select2-hidden-accessible')) {
                needsInit = true;
            }
        });
        
        if (needsInit) {
            console.log('⏰ Interval check: Found uninitialized Language Select2, initializing...');
            window.initLanguageSelect2();
        }
    }, 1000); // Проверяем каждую секунду
    
    console.log('✅ Language Select2 system loaded and ready');
}
</script>
 