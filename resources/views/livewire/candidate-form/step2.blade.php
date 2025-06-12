@if($currentStep === 2)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Дополнительная информация</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Водительские права -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Водительские права <span class="text-red-500">*</span>
            </label>
            <select wire:model="has_driving_license" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Выберите ответ</option>
                <option value="1">Да</option>
                <option value="0">Нет</option>
            </select>
            @error('has_driving_license') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Религия -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Религия <span class="text-red-500">*</span>
            </label>
            <select wire:model="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Выберите религию</option>
                @foreach($religions as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Практикующий</label>
            <select wire:model="is_practicing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Выберите ответ</option>
                <option value="1">Да</option>
                <option value="0">Нет</option>
            </select>
            @error('is_practicing') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>


        <!-- Семья -->
        <div class="col-span-3">
            <label class="block text-sm font-medium text-gray-700">Члены семьи</label>
            
            <!-- Список добавленных членов семьи -->
            <div class="mt-2 space-y-2">
                @foreach($family_members as $index => $member)
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <span class="font-medium">
                                @switch($member['type'])
                                    @case('father')
                                        Отец
                                        @break
                                    @case('mother')
                                        Мать
                                        @break
                                    @case('brother')
                                        Брат
                                        @break
                                    @case('sister')
                                        Сестра
                                        @break
                                    @case('wife')
                                        Жена
                                        @break
                                    @case('husband')
                                        Муж
                                        @break
                                    @case('son')
                                        Сын
                                        @break
                                    @case('daughter')
                                        Дочь
                                        @break
                                @endswitch
                            </span>
                            <span class="mx-2">|</span>
                            <span>{{ $member['birth_year'] }} г.р.</span>
                            <span class="mx-2">|</span>
                            <span>{{ $member['profession'] }}</span>
                        </div>
                        <button type="button" wire:click="removeFamilyMember({{ $index }})" class="text-red-500 hover:text-red-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Форма добавления нового члена семьи -->
            <div class="mt-4 p-4 border rounded-md bg-gray-50">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Добавить члена семьи</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <select wire:model="familyMemberType" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Выберите тип родства</option>
                            <option value="father">Отец</option>
                            <option value="mother">Мать</option>
                            <option value="brother">Брат</option>
                            <option value="sister">Сестра</option>
                            <option value="wife">Жена</option>
                            <option value="husband">Муж</option>
                            <option value="son">Сын</option>
                            <option value="daughter">Дочь</option>
                        </select>
                        @error('familyMemberType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <select wire:model="familyMemberBirthYear" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Год рождения</option>
                            @foreach($familyYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('familyMemberBirthYear') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <input type="text" 
                               wire:model="familyMemberProfession" 
                               placeholder="Профессия"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('familyMemberProfession') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" 
                            wire:click="addFamilyMember" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Добавить
                    </button>
                </div>
            </div>
        </div>

        <!-- Хобби и интересы -->
        <div class="col-span-3 grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Хобби</label>
                <textarea wire:model="hobbies" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('hobbies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Интересы</label>
                <textarea wire:model="interests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('interests') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Посещенные страны и Любимые виды спорта в одном ряду -->
        <div class="col-span-3 grid grid-cols-2 gap-6">
            <!-- Посещенные страны -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Посещенные страны</label>
                <div class="mt-1">
                    <div class="flex flex-wrap gap-2 mb-2 min-h-[2.5rem] bg-gray-50 p-2 rounded-md">
                        @foreach($visited_countries as $index => $country)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                @php
                                    $countryData = collect($countries)->firstWhere('name_ru', $country);
                                @endphp
                                @if($countryData && isset($countryData['flag_url']))
                                    <img src="{{ $countryData['flag_url'] }}" 
                                         alt="flag" 
                                         class="inline w-4 h-4 mr-1 align-middle">
                                @endif
                                {{ $country }}
                                <button type="button" wire:click="removeCountry({{ $index }})" class="ml-1 text-blue-400 hover:text-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <select wire:model="newCountry" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Выберите страну</option>
                            @foreach($countries as $country)
                                <option value="{{ $country['name_ru'] }}">{{ $country['name_ru'] }}</option>
                            @endforeach
                        </select>
                        <button type="button" 
                                wire:click="addCountry" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Добавить
                        </button>
                    </div>
                </div>
                @error('visited_countries') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Спорт -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Любимые виды спорта</label>
                <div class="mt-1">
                    <div class="flex flex-wrap gap-2 mb-2 min-h-[2.5rem] bg-gray-50 p-2 rounded-md">
                        @foreach($favorite_sports as $index => $sport)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $sports[$sport] ?? $sport }}
                                <button type="button" wire:click="removeSport({{ $index }})" class="ml-1 text-green-400 hover:text-green-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <select wire:model="newSport" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Выберите вид спорта</option>
                            @foreach($sports as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="button" 
                                wire:click="addSport" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Добавить
                        </button>
                    </div>
                </div>
                @error('favorite_sports') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Время и чтение в одном ряду -->
        <div class="col-span-3 grid grid-cols-4 gap-4">
            <!-- Количество книг читаемых в год -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество книг читаемых в год</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-md">{{ $books_per_year ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="books_per_year"
                           name="books_per_year"
                           value="{{ $books_per_year ?? 0 }}"
                           min="1" 
                           max="100" 
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
                @error('books_per_year') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Развлекательные видео -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых при просмотре развлекательных видео (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-md">{{ $entertainment_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="entertainment_hours_weekly"
                           name="entertainment_hours_weekly"
                           value="{{ $entertainment_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-indigo-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-indigo-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('entertainment_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Образовательные видео -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых при просмотре образовательных видео (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-md">{{ $educational_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="educational_hours_weekly"
                           name="educational_hours_weekly"
                           value="{{ $educational_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-emerald-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-emerald-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('educational_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Социальные сети -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых на соц. сети (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-md">{{ $social_media_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="social_media_hours_weekly"
                           name="social_media_hours_weekly"
                           value="{{ $social_media_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
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
                @error('social_media_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
@endif

<!-- Удаляем дублирующиеся скрипты Tom Select --> 

@push('scripts')
<script>
// Глобальный обработчик ползунков - версия 2.0
(function() {
    'use strict';
    
    // Предотвращаем повторную загрузку
    if (window.SliderManager) return;
    
    window.SliderManager = {
        activeSliders: new Map(),
        observer: null,
        
        init() {
            console.log('🎚️ SliderManager: Initializing...');
            this.setupMutationObserver();
            this.scanAndInitSliders();
            this.setupLivewireHooks();
        },
        
        setupMutationObserver() {
            // Отслеживаем изменения в DOM
            this.observer = new MutationObserver((mutations) => {
                let needsReinit = false;
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                if (node.matches('input[type="range"]') || 
                                    node.querySelector('input[type="range"]')) {
                                    needsReinit = true;
                                }
                            }
                        });
                    }
                });
                
                if (needsReinit) {
                    console.log('🔄 DOM changed, reinitializing sliders...');
                    setTimeout(() => this.scanAndInitSliders(), 50);
                }
            });
            
            this.observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },
        
        setupLivewireHooks() {
            if (typeof Livewire !== 'undefined') {
                // Хук для обновлений Livewire
                Livewire.hook('message.processed', () => {
                    console.log('🔄 Livewire message processed');
                    setTimeout(() => this.scanAndInitSliders(), 100);
                });
                
                // Хук для навигации
                document.addEventListener('livewire:navigated', () => {
                    console.log('🔄 Livewire navigated');
                    setTimeout(() => this.scanAndInitSliders(), 100);
                });
            }
        },
        
        scanAndInitSliders() {
            console.log('🔍 Scanning for sliders...');
            
            // Находим все ползунки на странице
            const allSliders = document.querySelectorAll('input[type="range"]');
            console.log(`Found ${allSliders.length} sliders total`);
            
            // Очищаем старые обработчики
            this.clearAllHandlers();
            
            // Конфигурация всех ползунков
            const sliderConfigs = [
                // Step 2
                { name: 'books_per_year', displaySelector: null },
                { name: 'entertainment_hours_weekly', displaySelector: null },
                { name: 'educational_hours_weekly', displaySelector: null },
                { name: 'social_media_hours_weekly', displaySelector: null },
                
                // Step 3
                { name: 'total_experience_years', displaySelector: '#experience-display', minValue: 0 },
                { name: 'job_satisfaction', displaySelector: '#satisfaction-display', minValue: 1 }
            ];
            
            // Инициализируем каждый ползунок
            sliderConfigs.forEach(config => {
                this.initSlider(config);
            });
            
            // Инициализируем GPA ползунки отдельно (динамические)
            this.initGpaSliders();
            
            console.log(`✅ SliderManager: ${this.activeSliders.size} sliders active`);
        },
        
        initSlider(config) {
            const slider = document.querySelector(`input[name="${config.name}"]`);
            if (!slider) return;
            
            let display;
            if (config.displaySelector) {
                display = document.querySelector(config.displaySelector);
            } else {
                // Ищем span в родительском элементе (для step2)
                display = slider.closest('div')?.parentElement?.querySelector('span');
            }
            
            if (!display) {
                console.warn(`❌ Display not found for ${config.name}`);
                return;
            }
            
            console.log(`🎚️ Initializing ${config.name}`);
            
            const handlers = this.createSliderHandlers(slider, display, config);
            this.activeSliders.set(config.name, handlers);
        },
        
        initGpaSliders() {
            const gpaSliders = document.querySelectorAll('input[type="range"][name*="universities"][name*="gpa"]');
            console.log(`🎓 Found ${gpaSliders.length} GPA sliders`);
            
            gpaSliders.forEach((slider, index) => {
                const display = slider.closest('div')?.parentElement?.querySelector('span');
                if (display) {
                    const key = `gpa_${index}`;
                    console.log(`🎚️ Initializing GPA slider ${index}`);
                    
                    const handlers = this.createSliderHandlers(slider, display, {
                        formatter: (value) => parseFloat(value).toFixed(2),
                        minValue: 0
                    });
                    this.activeSliders.set(key, handlers);
                }
            });
        },
        
        createSliderHandlers(slider, display, config = {}) {
            const updateDisplay = () => {
                const value = slider.value;
                const numValue = parseFloat(value);
                const minVal = config.minValue !== undefined ? config.minValue : parseFloat(slider.min);
                
                if (config.minValue !== undefined && numValue <= minVal) {
                    // Специальная обработка минимальных значений
                    if (slider.name === 'job_satisfaction') {
                        display.textContent = '1';
                    } else {
                        display.textContent = '0';
                    }
                } else {
                    // Применяем форматирование или выводим как есть
                    if (config.formatter) {
                        display.textContent = config.formatter(value);
                    } else {
                        display.textContent = value;
                    }
                }
            };
            
            // Создаем обработчики событий
            const inputHandler = (e) => {
                updateDisplay();
                // Не мешаем Livewire
                e.stopPropagation();
            };
            
            const changeHandler = (e) => {
                updateDisplay();
                // Позволяем Livewire обработать изменение
            };
            
            // Добавляем обработчики
            slider.addEventListener('input', inputHandler);
            slider.addEventListener('change', changeHandler);
            
            // Инициализируем отображение
            updateDisplay();
            
            return {
                slider,
                display,
                inputHandler,
                changeHandler,
                cleanup: () => {
                    slider.removeEventListener('input', inputHandler);
                    slider.removeEventListener('change', changeHandler);
                }
            };
        },
        
        clearAllHandlers() {
            console.log('🧹 Clearing all slider handlers...');
            this.activeSliders.forEach((handlers, key) => {
                handlers.cleanup();
            });
            this.activeSliders.clear();
        },
        
        destroy() {
            this.clearAllHandlers();
            if (this.observer) {
                this.observer.disconnect();
            }
        }
    };
    
    // Автозапуск
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.SliderManager.init(), 100);
        });
    } else {
        setTimeout(() => window.SliderManager.init(), 100);
    }
    
    // Очистка при выгрузке страницы
    window.addEventListener('beforeunload', () => {
        window.SliderManager.destroy();
    });
    
})();
</script>
@endpush 