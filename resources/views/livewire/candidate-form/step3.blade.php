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
            <label class="block text-sm font-medium text-gray-700">Университеты</label>
            <div class="space-y-4">
                @foreach($universities as $index => $university)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Название университета <span class="text-red-500">*</span>
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
                                <input type="number" 
                                       wire:model="universities.{{ $index }}.graduation_year" 
                                       min="1950"
                                       placeholder="например: {{ date('Y') + 2 }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.graduation_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    GPA
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
                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                                <div class="mt-1">
                                    <!-- Объединенный select с поиском -->
                                    <div class="relative" x-data="languageSearch({{ $index }}, '{{ $skill['language'] ?? '' }}')">
                                        <input 
                                            type="text" 
                                            x-model="search"
                                            @click="showDropdown = true"
                                            @keydown.escape="showDropdown = false"
                                            @keydown.arrow-down.prevent="highlightNext()"
                                            @keydown.arrow-up.prevent="highlightPrev()"
                                            @keydown.enter.prevent="selectHighlighted()"
                                            :placeholder="selectedLanguage ? '' : 'Поиск языка...'"
                                            :value="selectedLanguage || search"
                                            @input="search = $event.target.value; selectedLanguage = ''"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10"
                                            autocomplete="off"
                                            x-ref="languageInput"
                                        >
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <button type="button" 
                                                    @click="clearLanguage()" 
                                                    x-show="selectedLanguage"
                                                    class="text-gray-400 hover:text-gray-600 mr-2">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        
                                        <!-- Выпадающий список -->
                                        <div x-show="showDropdown && filteredLanguages.length > 0" 
                                             x-transition
                                             @click.away="showDropdown = false"
                                             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                                            <template x-for="(language, langIndex) in filteredLanguages" :key="language">
                                                <div @click="selectLanguage(language)" 
                                                     :class="{'bg-blue-100': langIndex === highlightedIndex}"
                                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50">
                                                    <span x-text="language" class="font-normal block truncate"></span>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Скрытое поле для синхронизации с Livewire -->
                                        <input type="hidden" 
                                               wire:model="language_skills.{{ $index }}.language" 
                                               name="language_skills[{{ $index }}][language]"
                                               :value="selectedLanguage">
                                    </div>
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
                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Добавить язык
                </button>
            </div>
        </div>

        <!-- Желаемая должность, Сфера деятельности и Ожидаемая зарплата -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
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

            <!-- Сфера деятельности -->
            <div wire:key="activity-sphere-container-{{ $activity_sphere }}-{{ $activity_sphere_other }}" 
                 x-data="{ 
                showOther: @entangle('activity_sphere').live === 'Другое',
                init() {
                    // Обновляем состояние при изменении Livewire
                    this.$watch('$wire.activity_sphere', (value) => {
                        this.showOther = value === 'Другое';
                    });
                    // Устанавливаем начальное состояние
                    this.showOther = this.$wire.activity_sphere === 'Другое';
                }
            }">
                <label class="block text-sm font-medium text-gray-700">
                    Сфера деятельности <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <select wire:model.live="activity_sphere" 
                            x-on:change="showOther = $event.target.value === 'Другое'"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Выберите сферу деятельности</option>
                        @foreach($activitySpheres as $key => $sphere)
                            <option value="{{ $sphere }}">{{ $sphere }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div x-show="showOther" x-transition class="mt-2" wire:key="activity-sphere-other-field-{{ $activity_sphere_other }}">
                    <input type="text" 
                           wire:model.live="activity_sphere_other" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Введите свою сферу деятельности">
                    @error('activity_sphere_other') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                @error('activity_sphere') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

<script>
function languageSearch(index, initialLanguage = '') {
    return {
        search: '',
        selectedLanguage: initialLanguage,
        showDropdown: false,
        highlightedIndex: -1,
        languages: @json($languages ?? []),
        
        get filteredLanguages() {
            if (!this.search.trim()) {
                return this.languages.slice(0, 10); // Показываем первые 10 языков по умолчанию
            }
            
            const searchTerm = this.search.toLowerCase();
            return this.languages.filter(language => 
                language.toLowerCase().includes(searchTerm)
            ).slice(0, 20); // Ограничиваем до 20 результатов
        },
        
        selectLanguage(language) {
            // Устанавливаем выбранный язык
            this.selectedLanguage = language;
            this.search = '';
            
            // Добавляем язык через Livewire
            @this.call('setLanguageForSkill', index, language);
            
            // Закрываем dropdown
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },
        
        clearLanguage() {
            this.selectedLanguage = '';
            this.search = '';
            this.showDropdown = false;
            
            // Удаляем язык через Livewire
            @this.call('removeLanguageFromSkill', index);
        },
        
        highlightNext() {
            if (this.highlightedIndex < this.filteredLanguages.length - 1) {
                this.highlightedIndex++;
            }
        },
        
        highlightPrev() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },
        
        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.filteredLanguages[this.highlightedIndex]) {
                this.selectLanguage(this.filteredLanguages[this.highlightedIndex]);
            }
        },
        
        init() {
            // Принудительно обновляем поле ввода
            this.$nextTick(() => {
                if (this.$refs.languageInput && this.selectedLanguage) {
                    this.$refs.languageInput.value = this.selectedLanguage;
                }
            });
            
            // Сбрасываем индекс при изменении поиска
            this.$watch('search', () => {
                this.highlightedIndex = -1;
                this.showDropdown = this.search.length > 0 || this.search === '';
            });
            
            // Следим за изменениями в Livewire
            this.$watch('selectedLanguage', () => {
                if (this.selectedLanguage) {
                    this.showDropdown = false;
                    // Принудительно обновляем поле ввода
                    this.$nextTick(() => {
                        if (this.$refs.languageInput) {
                            this.$refs.languageInput.value = this.selectedLanguage;
                        }
                    });
                }
            });
        }
    }
}

</script>

 