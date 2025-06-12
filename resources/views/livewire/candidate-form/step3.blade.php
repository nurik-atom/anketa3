@if($currentStep === 3)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Образование и работа</h2>

    <div class="grid grid-cols-1 gap-6">
        <!-- Школа -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Школа <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model="school" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                       max="{{ date('Y') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.graduation_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">
                                        GPA <span class="text-red-500">*</span>
                                    </label>
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-md">{{ $universities[$index]['gpa'] ?? 0 }}</span>
                                </div>
                                <div class="relative mt-2">
                                    <input type="range" 
                                           wire:model="universities.{{ $index }}.gpa"
                                           name="universities[{{ $index }}][gpa]"
                                           value="{{ $universities[$index]['gpa'] ?? 0 }}"
                                           min="0" 
                                           max="4" 
                                           step="0.01"
                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                                  [&::-webkit-slider-thumb]:w-4
                                                  [&::-webkit-slider-thumb]:h-4
                                                  [&::-webkit-slider-thumb]:appearance-none
                                                  [&::-webkit-slider-thumb]:bg-orange-600
                                                  [&::-webkit-slider-thumb]:rounded-full
                                                  [&::-webkit-slider-thumb]:cursor-pointer
                                                  [&::-moz-range-thumb]:w-4
                                                  [&::-moz-range-thumb]:h-4
                                                  [&::-moz-range-thumb]:appearance-none
                                                  [&::-moz-range-thumb]:bg-orange-600
                                                  [&::-moz-range-thumb]:rounded-full
                                                  [&::-moz-range-thumb]:cursor-pointer">
                                </div>
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

        <!-- Языковые навыки -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Языковые навыки</label>
            <div class="space-y-4">
                @foreach($language_skills as $index => $skill)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Язык</label>
                                <select wire:model="language_skills.{{ $index }}.language" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Выберите язык</option>
                                    @foreach($languages as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error("language_skills.{$index}.language") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Уровень</label>
                                <select wire:model="language_skills.{{ $index }}.level" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Выберите уровень</option>
                                    <option value="beginner">Начальный</option>
                                    <option value="intermediate">Средний</option>
                                    <option value="advanced">Продвинутый</option>
                                    <option value="native">Родной</option>
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
<!-- Желаемая должность и Ожидаемая зарплата в одном ряду -->
<div class="grid grid-cols-2 gap-4">
            <!-- Желаемая должность -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Желаемая должность <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       wire:model="desired_position" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Введите желаемую должность">
                @error('desired_position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Ожидаемая зарплата -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Ожидаемая зарплата (тенге) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       wire:model="expected_salary" 
                       min="0" 
                       step="1000"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Введите ожидаемую зарплату">
                @error('expected_salary') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <!-- Компьютерные навыки и Требования к работодателю в одном ряду -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Компьютерные навыки -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Компьютерные навыки</label>
                <textarea wire:model="computer_skills" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Опишите ваши навыки работы с компьютером"></textarea>
                @error('computer_skills') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Требования к работодателю -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Требования к работодателю</label>
                <textarea wire:model="employer_requirements" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('employer_requirements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        

        <!-- Опыт работы, удовлетворенность и зарплата в одном ряду -->
        <div class="grid grid-cols-2 gap-4">
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
                        Удовлетворенность текущей работой (1-10)
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
                           max="10" 
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

@push('scripts')
<script>
// JavaScript код для ползунков теперь находится в step2.blade.php (универсальный обработчик)
// Этот блок можно удалить
</script>
@endpush 