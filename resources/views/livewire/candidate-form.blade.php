<?php
// resources/views/livewire/candidate-form.blade.php
?>
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl rounded-lg">
        <!-- Step Navigation -->
        <div class="mb-6 px-3 sm:px-6 pt-6">
            <!-- Mobile Navigation (Grid for smaller screens) -->
            <div class="block lg:hidden">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach([
                        ['step' => 1, 'title' => 'Основная информация'],
                        ['step' => 2, 'title' => 'Дополнительная информация'],
                        ['step' => 3, 'title' => 'Образование и работа'],
                        ['step' => 4, 'title' => 'Тесты']
                    ] as $stepInfo)
                    <button type="button" 
                            wire:click="$set('currentStep', {{ $stepInfo['step'] }})"
                            class="flex items-center p-3 rounded-lg border-2 transition-all duration-200 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                        <div class="flex-shrink-0 mr-3">
                            @if($currentStep > $stepInfo['step'])
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-5 h-5 border-2 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600 bg-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium {{ $currentStep === $stepInfo['step'] ? 'text-white' : 'text-gray-500' }}">{{ $stepInfo['step'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium {{ $currentStep === $stepInfo['step'] ? 'text-blue-600' : 'text-gray-600' }} truncate">
                                {{ $stepInfo['title'] }}
                            </p>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Desktop Navigation (Horizontal for larger screens) -->
            <div class="hidden lg:flex items-center justify-between">
                <div class="flex items-center space-x-2 w-full">
                    @foreach([
                        ['step' => 1, 'title' => 'Основная информация'],
                        ['step' => 2, 'title' => 'Дополнительная информация'], 
                        ['step' => 3, 'title' => 'Образование и работа'],
                        ['step' => 4, 'title' => 'Тесты']
                    ] as $index => $stepInfo)
                    <!-- Step -->
                    <div class="flex items-center {{ $index === 3 ? '' : 'flex-1' }}">
                        <div class="flex items-center {{ $currentStep >= $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }}">
                            <div class="flex-shrink-0">
                                @if($currentStep > $stepInfo['step'])
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <div class="w-6 h-6 border-2 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                        <span class="text-sm {{ $currentStep === $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }}">{{ $stepInfo['step'] }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <button type="button" wire:click="$set('currentStep', {{ $stepInfo['step'] }})" class="text-sm font-medium {{ $currentStep >= $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }}">
                                    {{ $stepInfo['title'] }}
                                </button>
                            </div>
                        </div>
                        @if($index < 3)
                        <div class="flex-1 px-6">
                            <div class="h-0.5 {{ $currentStep > $stepInfo['step'] ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Remove old progress bar -->

        <form wire:submit.prevent="submit" class="p-3 sm:p-6 space-y-6">
            <!-- Step Content -->
            @include('livewire.candidate-form.step1')
            @include('livewire.candidate-form.step2')
            @include('livewire.candidate-form.step3')
            @include('livewire.candidate-form.step4')

            <!-- Navigation Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mt-8 px-3 sm:px-0">
                @if ($currentStep > 1)
                    <button type="button"
                            wire:click="previousStep"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Назад
                    </button>
                @else
                    <div class="hidden sm:block"></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button"
                            wire:click="nextStep"
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        Сохранить и далее
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @else
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Сохранить и завершить
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

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
                
                // Хук для смены шагов
                document.addEventListener('livewire:step-changed', (event) => {
                    console.log('🎚️ SliderManager: Step changed to:', event.detail.step);
                    this.reinitializeSliders('step-changed');
                });
                
                // Хук для переинициализации JS
                document.addEventListener('livewire:reinitialize-js', (event) => {
                    console.log('🎚️ SliderManager: Reinitialize JS event received');
                    this.reinitializeSliders('reinitialize-js');
                });
            }
        },
        
        scanAndInitSliders() {
            console.log('🔍 Scanning for sliders...');
            
            // Находим все ползунки на странице
            const allSliders = document.querySelectorAll('input[type="range"]');
            console.log(`Found ${allSliders.length} sliders total`);
            
            // Очищаем старые обработчики только если есть активные
            if (this.activeSliders.size > 0) {
                this.clearAllHandlers();
            }
            
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
            
            // Инициализируем каждый ползунок только если он виден
            sliderConfigs.forEach(config => {
                const slider = document.querySelector(`input[name="${config.name}"]`);
                if (slider && this.isElementVisible(slider)) {
                    this.initSlider(config);
                }
            });
            
            // Инициализируем GPA ползунки отдельно (динамические)
            this.initGpaSliders();
            
            console.log(`✅ SliderManager: ${this.activeSliders.size} sliders active`);
        },
        
        isElementVisible(element) {
            if (!element) return false;
            return element.offsetParent !== null && 
                   getComputedStyle(element).display !== 'none' &&
                   getComputedStyle(element).visibility !== 'hidden';
        },
        
        reinitializeSliders(source = 'manual') {
            console.log(`🔄 SliderManager: Reinitializing sliders (source: ${source})...`);
            
            // Очищаем старые обработчики
            this.clearAllHandlers();
            
            // Переинициализируем с задержкой для надежности
            setTimeout(() => {
                console.log('🔍 SliderManager: Scanning for new sliders...');
                this.scanAndInitSliders();
                console.log(`✅ SliderManager: Reinitialization complete (source: ${source})`);
            }, 100);
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
    
    // Дебаг функции для тестирования
    window.debugSliders = function() {
        console.log('🧪 DEBUG: Slider status');
        console.log('Active sliders:', window.SliderManager.activeSliders.size);
        console.log('All range inputs:', document.querySelectorAll('input[type="range"]').length);
        
        // Проверяем каждый ползунок
        document.querySelectorAll('input[type="range"]').forEach((slider, index) => {
            console.log(`Slider ${index}:`, {
                name: slider.name,
                value: slider.value,
                visible: slider.offsetParent !== null,
                hasListeners: window.SliderManager.activeSliders.has(slider.name)
            });
        });
    };
    
    window.testValidation = function() {
        console.log('🧪 DEBUG: Testing validation reset');
        if (typeof Livewire !== 'undefined' && Livewire.find) {
            const components = Livewire.all();
            console.log('Livewire components:', components.length);
            if (components[0]) {
                console.log('Current step:', components[0].data.currentStep);
                console.log('Has errors:', Object.keys(components[0].errors || {}).length > 0);
                console.log('Errors:', components[0].errors);
            }
        }
    };
    
    // Глобальная функция для принудительной переинициализации всех JS компонентов
    window.forceReinitializeJS = function() {
        console.log('🚀 FORCE: Manual reinitialization of all JS components');
        
        // Переинициализируем ползунки
        if (window.SliderManager) {
            window.SliderManager.reinitializeSliders('manual-force');
        }
        
        // Переинициализируем основные компоненты
        if (typeof reinitializeAllComponents === 'function') {
            reinitializeAllComponents(null, 'manual-force');
        } else {
            // Альтернативный способ если функция не доступна
            document.dispatchEvent(new CustomEvent('livewire:reinitialize-js'));
        }
        
        console.log('✅ FORCE: Manual reinitialization completed');
    };
    
    // Тестовая функция для проверки событий
    window.testEventChain = function() {
        console.log('🧪 TEST: Testing event chain...');
        
        // Тестируем отправку события step-changed
        console.log('📤 Dispatching step-changed event');
        document.dispatchEvent(new CustomEvent('livewire:step-changed', {
            detail: { step: 99 }
        }));
        
        // Тестируем отправку события reinitialize-js
        setTimeout(() => {
            console.log('📤 Dispatching reinitialize-js event');
            document.dispatchEvent(new CustomEvent('livewire:reinitialize-js'));
        }, 500);
        
        // Проверяем состояние через 2 секунды
        setTimeout(() => {
            console.log('🔍 Checking state after events...');
            window.debugSliders();
            window.testValidation();
            console.log('✅ TEST: Event chain test completed');
        }, 2000);
    };
    
})();
</script>
@endpush 