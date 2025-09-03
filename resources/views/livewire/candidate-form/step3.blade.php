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
                                    GPA <span class="text-red-500">*</span>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Период <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="work_experience.{{ $index }}.years" 
                                       placeholder="например: январь 2020 - апрель 2021"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("work_experience.{$index}.years") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

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
            <label class="block text-sm font-medium text-gray-700">Языковые навыки</label>
            <div class="space-y-4">
                @foreach($language_skills as $index => $skill)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Язык</label>
                                <input type="text" 
                                       wire:model="language_skills.{{ $index }}.language" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Например: Русский, Английский, Казахский">
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

        <!-- Желаемая должность и Ожидаемая зарплата в одном ряду -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
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
                <label class="block text-sm font-medium text-gray-700">Компьютерные навыки</label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Пример: Excel, AutoCAD, Python;
                    </span>
                </p>
                <textarea wire:model="computer_skills" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Например: Word, Excel, PowerPoint, Photoshop, 1C"></textarea>
                @error('computer_skills') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Требования к работодателю -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Требования к работодателю</label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Пример: намазхана, возможность ходить на Джума намаз, ходить в платке и т.д.
                    </span>
                </p>
                <textarea wire:model="employer_requirements" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Например: гибкий график, соблюдение религиозных требований, дресс-код"></textarea>
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

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    setTimeout(initializeSalaryField, 100);
    setTimeout(initializeSalaryField, 500);
    setTimeout(initializeSalaryField, 1000);
    setTimeout(initializeSalaryField, 2000);
});

// Инициализация после загрузки Livewire
document.addEventListener('livewire:load', function() {
    console.log('Livewire loaded');
    setTimeout(initializeSalaryField, 100);
    setTimeout(initializeSalaryField, 500);
});

// Инициализация после обновлений Livewire
document.addEventListener('livewire:update', function() {
    console.log('Livewire updated');
    setTimeout(initializeSalaryField, 100);
});

// Для новых версий Livewire
if (typeof Livewire !== 'undefined') {
    Livewire.hook('message.processed', (message, component) => {
        console.log('Livewire message processed');
        setTimeout(initializeSalaryField, 100);
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

 