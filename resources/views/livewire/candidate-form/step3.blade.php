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
                                           wire:model.live="universities.{{ $index }}.gpa"
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

        <!-- Компьютерные навыки, Требования к работодателю и Желаемая должность в одном ряду -->
        <div class="grid grid-cols-3 gap-4">
            <!-- Компьютерные навыки -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Компьютерные навыки <span class="text-red-500">*</span></label>
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

            <!-- Желаемая должность -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Желаемая должность <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="desired_position" 
                          rows="3" 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('desired_position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Опыт работы, удовлетворенность и зарплата в одном ряду -->
        <div class="grid grid-cols-3 gap-4">
            <!-- Общий стаж работы -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">
                        Общий стаж работы (лет) <span class="text-red-500">*</span>
                    </label>
                    <span id="experience-display" class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-md">{{ $total_experience_years ?? 0 }}</span>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           id="experience-slider"
                           wire:model.live="total_experience_years"
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
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-md">{{ $job_satisfaction ?? 1 }}</span>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model.live="job_satisfaction"
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

            <!-- Ожидаемая зарплата -->
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">
                        Ожидаемая зарплата (тенге) <span class="text-red-500">*</span>
                    </label>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-md">{{ number_format($expected_salary ?? 0, 0, '.', ' ') }} тенге</span>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model.live="expected_salary"
                           min="0" 
                           max="2000000" 
                           step="10000"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-purple-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-purple-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('expected_salary') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
@endif 

@push('scripts')
<script>
// Проверяем, чтобы скрипт не выполнялся дважды  
if (typeof window.sliderHandlersLoaded === 'undefined') {
    window.sliderHandlersLoaded = true;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing optimized slider handlers...');
    
    // Функция debounce для уменьшения количества запросов
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Функция для форматирования зарплаты
    function formatSalary(value) {
        return new Intl.NumberFormat('ru-KZ', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value).replace(/,/g, ' ') + ' тенге';
    }
    
    // Функция для эффективного управления ползунком
    function setupFastSlider(sliderId, displayId, livewireProperty, formatter = null) {
        const slider = document.getElementById(sliderId);
        const display = document.getElementById(displayId);
        
        if (!slider || !display) {
            console.log(`Slider ${sliderId} or display ${displayId} not found`);
            return;
        }
        
        console.log(`Setting up fast slider: ${sliderId}`);
        
        // Мгновенное обновление отображения
        const updateDisplay = (value) => {
            if (formatter) {
                display.textContent = formatter(value);
            } else {
                display.textContent = value;
            }
        };
        
        // Функция для обновления Livewire
        const updateLivewire = (value) => {
            console.log(`Trying to update ${livewireProperty} to ${value}`);
            
            // Способ 1: Через прямое обновление атрибута wire:model и событие
            slider.setAttribute('value', value);
            
            // Способ 2: Создаем событие change для Livewire
            const changeEvent = new Event('change', { bubbles: true });
            slider.dispatchEvent(changeEvent);
            
            // Способ 3: Через Livewire компонент (если доступен)
            const component = getLivewireComponent();
            if (component) {
                try {
                    // Пробуем разные методы обновления
                    if (typeof component.set === 'function') {
                        component.set(livewireProperty, value);
                        console.log(`Updated via component.set: ${livewireProperty} = ${value}`);
                    } else if (typeof component.call === 'function') {
                        component.call('$set', livewireProperty, value);
                        console.log(`Updated via component.call $set: ${livewireProperty} = ${value}`);
                    } else if (component[livewireProperty] !== undefined) {
                        component[livewireProperty] = value;
                        console.log(`Updated via direct property: ${livewireProperty} = ${value}`);
                    }
                } catch (error) {
                    console.error('Error updating Livewire:', error);
                }
            }
        };
        
        // Debounced функция для обновления Livewire (срабатывает через 300ms после последнего изменения)
        const debouncedUpdate = debounce(updateLivewire, 300);
        
        // Обработчик для мгновенного обновления во время перетаскивания
        slider.addEventListener('input', (e) => {
            const value = e.target.value;
            updateDisplay(value); // Мгновенно обновляем отображение
            debouncedUpdate(value); // Debounced обновление Livewire
        });
        
        // Обработчик для окончательного обновления (когда отпускают мышь)
        slider.addEventListener('change', (e) => {
            const value = e.target.value;
            updateDisplay(value);
            updateLivewire(value); // Немедленное обновление при окончании
        });
        
        // Инициализируем отображение
        updateDisplay(slider.value);
        console.log(`Initialized ${sliderId} with value: ${slider.value}`);
    }
    
    // Функция для получения Livewire компонента (из step1)
    function getLivewireComponent() {
        if (typeof Livewire !== 'undefined' && Livewire.all) {
            const allComponents = Livewire.all();
            for (let component of allComponents) {
                if (component.name === 'candidate-form') {
                    return component;
                }
            }
            return allComponents[0]; // fallback
        }
        return null;
    }
    
    // Настраиваем оптимизированные ползунки
    function initializeOptimizedSliders() {
        // Общий стаж работы (мгновенное обновление отображения + обычный Livewire)
        setupDisplayOptimization('experience-slider', 'experience-display');
        
        // Остальные ползунки пока оставляем как есть для сравнения
        // Удовлетворенность работой
        const satisfactionSlider = document.querySelector('input[wire\\:model\\.live="job_satisfaction"]');
        const satisfactionDisplay = satisfactionSlider?.closest('div')?.querySelector('span');
        if (satisfactionSlider && satisfactionDisplay) {
            updateSliderValue(satisfactionSlider, satisfactionDisplay);
            console.log('Satisfaction slider initialized (old way)');
        }
        
        // Ожидаемая зарплата
        const salarySlider = document.querySelector('input[wire\\:model\\.live="expected_salary"]');
        const salaryDisplay = salarySlider?.closest('div')?.querySelector('span');
        if (salarySlider && salaryDisplay) {
            updateSliderValue(salarySlider, salaryDisplay, formatSalary);
            console.log('Salary slider initialized (old way)');
        }
        
        // ГПА в университетах
        const gpaSliders = document.querySelectorAll('input[type="range"][wire\\:model*="universities"][wire\\:model*="gpa"]');
        gpaSliders.forEach((slider, index) => {
            const display = slider.closest('div')?.querySelector('span');
            if (display) {
                updateSliderValue(slider, display, (value) => parseFloat(value).toFixed(1));
                console.log(`GPA slider ${index} initialized (old way)`);
            }
        });
    }
    
    // Оптимизация только отображения (Livewire работает как обычно)
    function setupDisplayOptimization(sliderId, displayId) {
        const slider = document.getElementById(sliderId);
        const display = document.getElementById(displayId);
        
        if (!slider || !display) {
            console.log(`Slider ${sliderId} or display ${displayId} not found`);
            return;
        }
        
        console.log('Setting up display optimization only');
        
        // Простое мгновенное обновление отображения
        slider.addEventListener('input', (e) => {
            display.textContent = e.target.value;
            console.log(`Display updated to: ${e.target.value}`);
        });
        
        // Инициализация
        display.textContent = slider.value;
        console.log(`Initialized display with: ${slider.value}`);
    }
    
    // Простой подход с debounce
    function setupSimpleSlider(sliderId, displayId) {
        const slider = document.getElementById(sliderId);
        const display = document.getElementById(displayId);
        
        if (!slider || !display) return;
        
        console.log('Setting up simple slider approach');
        
        // Функция обновления отображения
        const updateDisplay = (value) => {
            display.textContent = value;
        };
        
        // Debounced функция для отправки input события
        const debouncedInput = debounce(() => {
            // Эмулируем обычное поведение input с wire:model
            const inputEvent = new Event('input', { bubbles: true });
            slider.dispatchEvent(inputEvent);
            console.log('Dispatched debounced input event');
        }, 300);
        
        // Обработчики
        slider.addEventListener('input', (e) => {
            updateDisplay(e.target.value); // Мгновенно обновляем отображение
            debouncedInput(); // Debounced отправка в Livewire
        });
        
        slider.addEventListener('change', (e) => {
            updateDisplay(e.target.value);
            // Немедленная отправка при окончании
            const changeEvent = new Event('change', { bubbles: true });
            slider.dispatchEvent(changeEvent);
            console.log('Dispatched immediate change event');
        });
        
        // Инициализация
        updateDisplay(slider.value);
    }
    
    // Старая функция для сравнения
    function updateSliderValue(slider, displayElement, formatter = null) {
        if (!slider || !displayElement) return;
        
        const updateDisplay = () => {
            const value = slider.value;
            if (formatter) {
                displayElement.textContent = formatter(value);
            } else {
                displayElement.textContent = value;
            }
        };
        
        slider.addEventListener('input', updateDisplay);
        slider.addEventListener('change', updateDisplay);
        updateDisplay();
    }
    
    // Инициализируем сразу
    initializeOptimizedSliders();
    
    // Переинициализируем после обновлений Livewire
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.processed', () => {
            setTimeout(initializeOptimizedSliders, 100);
        });
    }
});

} // Конец проверки window.sliderHandlersLoaded
</script>
@endpush 