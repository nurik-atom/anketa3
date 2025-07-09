import IMask from 'imask';

// ОТЛАДОЧНЫЕ КОМАНДЫ ДЛЯ ВАЛИДАЦИИ КИРИЛЛИЦЫ:
console.log('🔤 CYRILLIC VALIDATION DEBUG COMMANDS:');
console.log('=====================================');
console.log('quickTest()              - Run complete test and fix');
console.log('testHobbiesInterests()   - Test Hobbies & Interests fields');
console.log('testStepValidation()     - Test server validation on step 2');
console.log('testLatinInput()         - Test latin validation in real fields');
console.log('testCyrillicValidation() - Check current state');
console.log('forceCyrillicValidation() - Force reinitialize');
console.log('startAutoReinit()        - Auto-fix every 3 seconds');
console.log('testEvents()             - Test Livewire events');
console.log('debugAllEvents()         - Trigger all events manually');
console.log('=====================================');

// Глобальные переменные
let phoneMask = null;
let stepCropper = null;
let currentFile = null;
let isInitialized = false; // Флаг для предотвращения повторной инициализации

// Инициализация маски телефона
export function initPhoneMask() {
    const phoneInput = document.getElementById('phone-input');
    if (!phoneInput) {
        console.log('Phone input not found');
        return null;
    }

    // Удаляем старую маску если есть
    if (phoneMask) {
        phoneMask.destroy();
    }

    try {
        phoneMask = IMask(phoneInput, {
            mask: '+7 (000) 000-00-00',
            lazy: false,
            placeholderChar: '_'
        });

        // Синхронизация с Livewire
        phoneMask.on('accept', function() {
            phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
            phoneInput.dispatchEvent(new Event('change', { bubbles: true }));
        });

        // Если в поле уже есть значение, применяем маску
        if (phoneInput.value) {
            phoneMask.value = phoneInput.value;
        }

        // Обновляем маску при изменениях от Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', () => {
                if (phoneInput.value && phoneMask) {
                    phoneMask.value = phoneInput.value;
                }
            });
        }

        console.log('Phone mask initialized successfully');
        return phoneMask;
    } catch (error) {
        console.error('Error initializing phone mask:', error);
        return null;
    }
}

// Упрощенная функция получения Livewire компонента
export function getLivewireComponent() {
    if (typeof Livewire === 'undefined') {
        console.error('Livewire not available');
        return null;
    }

    try {
        // Метод 1: Ищем по wire:id
        const wireElements = document.querySelectorAll('[wire\\:id]');
        
        for (let element of wireElements) {
            const wireId = element.getAttribute('wire:id');
            if (wireId && Livewire.find) {
                const component = Livewire.find(wireId);
                if (component) {
                    console.log('Found Livewire component by wire:id:', component);
                    return component;
                }
            }
        }
        
        // Метод 2: Ищем по data-livewire-id
        const dataWireElements = document.querySelectorAll('[data-livewire-id]');
        
        for (let element of dataWireElements) {
            const wireId = element.getAttribute('data-livewire-id');
            if (wireId && Livewire.find) {
                const component = Livewire.find(wireId);
                if (component) {
                    console.log('Found Livewire component by data-livewire-id:', component);
                    return component;
                }
            }
        }

        // Метод 3: Ищем компонент CandidateForm через все компоненты
        if (Livewire.all) {
            const components = Livewire.all();
            for (let component of components) {
                if (component.name === 'candidate-form' || component.__name === 'candidate-form') {
                    console.log('Found CandidateForm component by name:', component);
                    return component;
                }
            }
            
            // Если есть хотя бы один компонент, возьмем первый
            if (components.length > 0) {
                console.log('Using first available Livewire component:', components[0]);
                return components[0];
            }
        }

        console.error('No Livewire component found');
        return null;
    } catch (error) {
        console.error('Error finding Livewire component:', error);
        return null;
    }
}

// Инициализация загрузки фото с кропом
export function initPhotoUpload() {
    const photoInput = document.getElementById('photo-input');
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    
    if (!photoInput || !fallbackInput) {
        console.log('Photo inputs not found');
        return;
    }

    // Убираем старые обработчики
    photoInput.removeEventListener('change', handlePhotoChange);
    photoInput.addEventListener('change', handlePhotoChange);

    console.log('Photo upload initialized');
}

// Обработчик изменения фото
function handlePhotoChange(e) {
    const file = e.target.files[0];
    if (!file) return;

    console.log('File selected:', file.name);

    // Проверки
    if (file.size > 2 * 1024 * 1024) {
        alert('Размер файла не должен превышать 2MB');
        e.target.value = '';
        return;
    }

    if (!file.type.match(/image\/(jpeg|jpg|png)/)) {
        alert('Загружаемый файл должен быть изображением (JPG, JPEG, PNG)');
        e.target.value = '';
        return;
    }

    currentFile = file;
    
    // Показываем модальное окно для кропа
    showCropModal(file);
}

// Показать модальное окно кропа
function showCropModal(file) {
    const cropModal = document.getElementById('crop-modal');
    const cropImage = document.getElementById('crop-image');
    
    if (!cropModal || !cropImage) {
        // Если нет модального окна, используем простую загрузку
        console.log('Crop modal not found, using simple upload');
        uploadFileDirectly(file);
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        cropImage.src = e.target.result;
        cropModal.classList.remove('hidden');
        
        // Инициализация Cropper.js
        if (stepCropper) {
            stepCropper.destroy();
        }
        
        // Ждем загрузки Cropper.js с повторными попытками
        let attempts = 0;
        const maxAttempts = 20; // 20 попыток = 2 секунды
        
        function initCropper() {
            attempts++;
            
            if (typeof Cropper === 'undefined') {
                if (attempts < maxAttempts) {
                    console.log(`Cropper.js not loaded yet, attempt ${attempts}/${maxAttempts}`);
                    setTimeout(initCropper, 100);
                    return;
                } else {
                    console.error('Cropper.js not loaded after max attempts, using simple upload');
                    uploadFileDirectly(file);
                    return;
                }
            }
            
            try {
                stepCropper = new Cropper(cropImage, {
                    aspectRatio: 3 / 4,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    minCropBoxWidth: 100,
                    minCropBoxHeight: 133,
                    ready: function() {
                        console.log('Cropper ready event fired');
                        // Добавляем флаг готовности
                        stepCropper.isReady = true;
                    },
                    cropstart: function() {
                        console.log('Cropper cropstart event fired');
                    },
                    error: function(error) {
                        console.error('Cropper error:', error);
                        alert('Ошибка инициализации кроппера: ' + error.message);
                    }
                });
                
                console.log('Cropper initialized successfully:', stepCropper);
            } catch (error) {
                console.error('Error initializing Cropper:', error);
                alert('Ошибка при инициализации кроппера. Используем простую загрузку.');
                uploadFileDirectly(file);
            }
        }
        
        // Запускаем инициализацию с небольшой задержкой
        setTimeout(initCropper, 150);
    };
    reader.readAsDataURL(file);
}

// Простая загрузка файла без кропа
function uploadFileDirectly(file) {
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    if (fallbackInput) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fallbackInput.files = dataTransfer.files;
        fallbackInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('Photo uploaded directly via Livewire');
    }
}

// Сохранить обрезанное фото
export function saveCrop() {
    console.log('saveCrop called');
    
    if (!stepCropper) {
        console.error('Cropper not initialized');
        alert('Ошибка: кроппер не инициализирован');
        return;
    }

    // Правильная проверка готовности кроппера
    if (!stepCropper.isReady) {
        console.error('Cropper not ready');
        alert('Кроппер еще не готов. Подождите немного и попробуйте снова.');
        return;
    }

    if (!currentFile) {
        console.error('No file selected');
        alert('Ошибка: файл не выбран');
        return;
    }

    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');

    try {
        console.log('Getting cropped canvas...');
        
        // Получаем обрезанное изображение с проверкой
        const canvas = stepCropper.getCroppedCanvas({
            width: 300,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            console.error('getCroppedCanvas returned null');
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            alert('Ошибка: не удалось получить обрезанное изображение. Попробуйте выбрать область для обрезки.');
            return;
        }

        console.log('Canvas obtained, converting to blob...');

        canvas.toBlob(function(blob) {
            if (!blob) {
                console.error('toBlob returned null');
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                alert('Ошибка: не удалось преобразовать изображение');
                return;
            }

            console.log('Blob created:', blob.size, 'bytes');

            // Создаем File объект
            const file = new File([blob], currentFile.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Загружаем через fallback input
            const fallbackInput = document.getElementById('photo-livewire-fallback');
            if (fallbackInput) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fallbackInput.files = dataTransfer.files;
                fallbackInput.dispatchEvent(new Event('change', { bubbles: true }));
                
                console.log('Cropped photo uploaded via Livewire');
                
                // Закрываем модальное окно
                cancelCrop();
                
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
            } else {
                console.error('Fallback input not found');
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                alert('Ошибка: не удается загрузить обрезанное фото');
            }
        }, 'image/jpeg', 0.9);

    } catch (error) {
        console.error('Error in saveCrop:', error);
        if (loadingIndicator) loadingIndicator.classList.add('hidden');
        alert('Ошибка при обрезке изображения: ' + error.message);
    }
}

// Отменить кроп
export function cancelCrop() {
    const cropModal = document.getElementById('crop-modal');
    const photoInput = document.getElementById('photo-input');
    
    if (stepCropper) {
        stepCropper.destroy();
        stepCropper = null;
    }
    
    if (cropModal) cropModal.classList.add('hidden');
    if (photoInput) photoInput.value = '';
    
    currentFile = null;
    console.log('Crop cancelled');
}

// Функция удаления фото
export function removePhoto() {
    console.log('removePhoto called');
    
    try {
        // Метод 1: Поиск и клик по скрытой кнопке (самый надежный)
        const removeBtn = document.getElementById('hidden-remove-photo-btn');
        if (removeBtn) {
            console.log('Calling removePhoto via hidden button click');
            removeBtn.click();
            return;
        }
        
        // Метод 2: Поиск кнопки с wire:click="removePhoto"
        const wireRemoveBtn = document.querySelector('[wire\\:click="removePhoto"]');
        if (wireRemoveBtn) {
            console.log('Calling removePhoto via wire:click button');
            wireRemoveBtn.click();
            return;
        }
        
        // Метод 3: Через Livewire.emit
        if (typeof Livewire !== 'undefined' && Livewire.emit) {
            console.log('Trying Livewire.emit for removePhoto');
            Livewire.emit('removePhoto');
            return;
        }
        
        // Метод 4: Через dispatch event (Livewire v3)
        if (typeof Livewire !== 'undefined' && Livewire.dispatch) {
            console.log('Trying Livewire.dispatch for removePhoto');
            Livewire.dispatch('removePhoto');
            return;
        }
        
        // Метод 5: Через найденный компонент
        const component = getLivewireComponent();
        if (component && component.call) {
            console.log('Calling removePhoto via Livewire component');
            component.call('removePhoto');
            return;
        }
        
        console.error('All methods failed to call removePhoto');
        alert('Не удалось удалить фото. Попробуйте обновить страницу.');
        
    } catch (error) {
        console.error('Error in removePhoto:', error);
        alert('Ошибка при удалении фото: ' + error.message);
    }
}

// Функция инициализации всех компонентов
function initializeComponents(force = false) {
    if (isInitialized && !force) {
        console.log('Components already initialized, skipping...');
        return;
    }
    
    console.log('🚀 Initializing components...', { force });
    
    // Добавляем небольшую задержку чтобы DOM точно загрузился
    setTimeout(() => {
        console.log('📞 Initializing phone mask...');
        initPhoneMask();
        
        console.log('📷 Initializing photo upload...');
        initPhotoUpload();
        
        console.log('🔤 Initializing cyrillic validation...');
        initCyrillicValidation();
        
        if (!force) {
            isInitialized = true;
        }
        console.log('✅ All components initialized');
    }, 100);
}

// Функция проверки кириллицы
export function isCyrillic(text) {
    if (!text || text.trim() === '') return true; // Пустые значения разрешены (валидация обязательности отдельно)
    
    // Проверяем на наличие латинских букв (более точная проверка)
    const hasLatinLetters = /[a-zA-Z]/.test(text);
    if (hasLatinLetters) {
        console.log(`❌ Text contains latin letters: "${text}"`);
        return false;
    }
    
    // Регулярное выражение для кириллицы, цифр, пробелов, знаков препинания
    // Соответствует серверной валидации в CyrillicRule
    const cyrillicRegex = /^[а-яё\s\-\.',():;№\d\u0401\u0451А-ЯЁ/+=!?&\n\r\t]+$/u;
    const isValid = cyrillicRegex.test(text);
    
    console.log(`🔍 Cyrillic validation for "${text}": ${isValid ? '✅ Valid' : '❌ Invalid'}`);
    return isValid;
}

// Функция отображения ошибки кириллицы
function showCyrillicError(input, show = true) {
    // Создаем уникальный ID для ошибки на основе ID поля или wire:model
    let errorId;
    if (input.id) {
        errorId = input.id + '-cyrillic-error';
    } else {
        const wireModel = input.getAttribute('wire:model');
        if (wireModel) {
            // Заменяем точки и квадратные скобки на дефисы для создания валидного ID
            errorId = wireModel.replace(/[\.\[\]]/g, '-') + '-cyrillic-error';
        } else {
            errorId = 'field-cyrillic-error';
        }
    }
    
    let errorElement = document.getElementById(errorId);
    
    if (show) {
        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.id = errorId;
            errorElement.className = 'cyrillic-error text-red-500 text-sm block mt-1';
            errorElement.setAttribute('data-field', input.getAttribute('wire:model') || input.id || 'unknown');
            errorElement.textContent = 'Поле должно содержать только кириллические символы, цифры и знаки препинания';
            
            console.log(`📝 Creating cyrillic error element with ID: ${errorId} for field: ${input.getAttribute('wire:model') || input.id}`);
            
            // Ищем подходящее место для вставки ошибки
            let insertAfter = input;
            
            // Вставляем после элемента
            if (insertAfter.nextSibling) {
                insertAfter.parentNode.insertBefore(errorElement, insertAfter.nextSibling);
            } else {
                insertAfter.parentNode.appendChild(errorElement);
            }
        }
        errorElement.style.display = 'block';
        input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        
        console.log(`🚨 Showing cyrillic error for field: ${input.getAttribute('wire:model') || input.id}`);
    } else {
        if (errorElement) {
            errorElement.style.display = 'none';
        }
        input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        
        console.log(`✅ Hiding cyrillic error for field: ${input.getAttribute('wire:model') || input.id}`);
    }
}

// Функция валидации кириллицы для конкретного поля
function validateCyrillicField(input) {
    const value = input.value.trim();
    const isValid = isCyrillic(value);
    
    console.log(`🔍 Validating cyrillic for field: ${input.id || input.getAttribute('wire:model')}`, {
        value: `"${value}"`,
        isValid: isValid,
        isEmpty: !value
    });
    
    showCyrillicError(input, !isValid);
    
    return isValid;
}

// Инициализация валидации кириллицы
export function initCyrillicValidation() {
    console.log('🔤 Starting cyrillic validation initialization...');
    
    // Убираем старые обработчики со всех полей
    removeCyrillicHandlers();
    
    // Находим ВСЕ input и textarea поля, которые видимы
    const allInputs = document.querySelectorAll('input[type="text"], textarea');
    const allVisibleInputs = Array.from(allInputs).filter(input => {
        return isElementVisible(input) && shouldValidateCyrillic(input);
    });
    
    console.log(`🔍 Found ${allVisibleInputs.length} visible inputs/textareas to check for cyrillic`);
    
    let initializedFields = 0;
    
    allVisibleInputs.forEach(input => {
        const identifier = getInputIdentifier(input);
        console.log(`✅ Initializing cyrillic validation for: ${identifier}`);
        initCyrillicField(input, identifier);
        initializedFields++;
    });
    
    console.log(`🔤 Cyrillic validation initialized for ${initializedFields} fields`);
}

// Функция для определения, нужно ли проверять поле на кириллицу
function shouldValidateCyrillic(input) {
    const wireModel = input.getAttribute('wire:model');
    const id = input.id;
    
    // Список полей, которые должны проверяться на кириллицу
    const cyrillicFields = [
        // Step 1 (по ID)
        'last-name-input', 'first-name-input', 'middle-name-input', 
        'birth-place-input', 'current-city-input',
        
        // Step 2 и 3 (по wire:model)
        'hobbies', 'interests', 'favorite_sports', 'school', 
        'desired_position', 'employer_requirements'
    ];
    
    // Проверяем по ID
    if (id && cyrillicFields.includes(id)) {
        return true;
    }
    
    // Проверяем по wire:model
    if (wireModel && cyrillicFields.includes(wireModel)) {
        return true;
    }
    
    // Проверяем динамические поля (члены семьи)
    if (wireModel && wireModel.includes('family_members') && wireModel.includes('profession')) {
        return true;
    }
    
    return false;
}

// Функция для получения идентификатора поля
function getInputIdentifier(input) {
    if (input.id) {
        return `#${input.id}`;
    }
    
    const wireModel = input.getAttribute('wire:model');
    if (wireModel) {
        return `[wire:model="${wireModel}"]`;
    }
    
    return input.tagName.toLowerCase();
}

// Функция для удаления старых обработчиков
function removeCyrillicHandlers() {
    // Удаляем обработчики со всех полей
    const allInputs = document.querySelectorAll('input[type="text"], textarea');
    allInputs.forEach(input => {
        input.removeEventListener('input', handleCyrillicInput);
        input.removeEventListener('blur', handleCyrillicBlur);
        // Убираем маркер инициализации
        if (input.dataset.cyrillicInit) {
            delete input.dataset.cyrillicInit;
        }
    });
}

// Функция инициализации одного поля кириллицы
function initCyrillicField(input, identifier) {
    // Проверяем, не было ли поле уже инициализировано
    if (input.dataset.cyrillicInit === 'true') {
        console.log(`⏭️ Field already initialized, skipping: ${identifier}`);
        return;
    }
    
    console.log(`🎯 Initializing cyrillic validation for: ${identifier}`, {
        tagName: input.tagName,
        type: input.type,
        hasValue: !!input.value,
        wireModel: input.getAttribute('wire:model')
    });
    
    // Удаляем старые обработчики (на всякий случай)
    input.removeEventListener('input', handleCyrillicInput);
    input.removeEventListener('blur', handleCyrillicBlur);
    
    // Добавляем новые обработчики
    input.addEventListener('input', handleCyrillicInput);
    input.addEventListener('blur', handleCyrillicBlur);
    
    // Маркируем поле как инициализированное
    input.dataset.cyrillicInit = 'true';
    
    // Добавляем визуальный индикатор для тестирования
    input.style.boxShadow = '0 0 0 1px rgba(34, 197, 94, 0.4)';
    setTimeout(() => {
        input.style.boxShadow = '';
    }, 800);
    
    // Проверяем существующее значение
    if (input.value && input.value.trim() !== '') {
        console.log(`🔍 Validating existing value: "${input.value}"`);
        validateCyrillicField(input);
    }
}



// Обработчик ввода для проверки кириллицы
function handleCyrillicInput(e) {
    const input = e.target;
    const value = input.value;
    
    console.log(`⌨️ Cyrillic input event for field: ${input.id || input.getAttribute('wire:model')}`, {
        value: value
    });
    
    // Если обнаружены латинские буквы, показываем ошибку немедленно
    const hasLatinLetters = /[a-zA-Z]/.test(value);
    if (hasLatinLetters && value.trim() !== '') {
        console.log(`🚨 Latin letters detected immediately in: ${input.id || input.getAttribute('wire:model')}`);
        validateCyrillicField(input); // Немедленная валидация
        return;
    }
    
    // Для остальных случаев используем debounce для избежания слишком частых проверок
    clearTimeout(input.cyrillicTimeout);
    input.cyrillicTimeout = setTimeout(() => {
        console.log(`⏱️ Debounced validation for: ${input.id || input.getAttribute('wire:model')}`);
        validateCyrillicField(input);
    }, 500); // Увеличили до 500ms для обычной валидации
}

// Обработчик потери фокуса для проверки кириллицы
function handleCyrillicBlur(e) {
    const input = e.target;
    console.log(`👀 Cyrillic blur event for field: ${input.id || input.getAttribute('wire:model')}`);
    validateCyrillicField(input);
}

// Функция для проверки видимости элемента
function isElementVisible(element) {
    if (!element) return false;
    
    // Проверяем, что элемент и его родители не скрыты
    let current = element;
    while (current && current !== document.body) {
        const style = window.getComputedStyle(current);
        if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
            return false;
        }
        current = current.parentElement;
    }
    
    // Дополнительная проверка на размеры элемента
    const rect = element.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
}

// Функция с повторными попытками инициализации
function initializeWithRetry(maxAttempts = 5, currentAttempt = 1, force = false) {
    console.log(`Initialization attempt ${currentAttempt}/${maxAttempts}`);
    
    // Проверяем наличие ключевых элементов
    const phoneInput = document.getElementById('phone-input');
    const photoInput = document.getElementById('photo-input');
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    
    console.log('DOM elements check:', {
        phoneInput: phoneInput ? 'found' : 'not found',
        photoInput: photoInput ? 'found' : 'not found', 
        fallbackInput: fallbackInput ? 'found' : 'not found'
    });
    
    // Если элементы найдены или достигли максимума попыток
    if ((phoneInput || photoInput) || currentAttempt >= maxAttempts) {
        if (!isInitialized || force) {
            initializeComponents(force);
        }
        return;
    }
    
    // Повторная попытка через 500ms
    setTimeout(() => {
        initializeWithRetry(maxAttempts, currentAttempt + 1, force);
    }, 500);
}

// Функция для реинициализации при изменении DOM
function reinitializeOnDOMChange() {
    console.log('DOM potentially changed, checking for new elements...');
    
    // Всегда пытаемся реинициализировать элементы (с force = true)
    setTimeout(() => {
        initializeWithRetry(3, 1, true);
        // Также переинициализируем валидацию кириллицы
        initCyrillicValidation();
    }, 200);
}

// Делаем функции глобальными для использования в HTML
window.saveCrop = saveCrop;
window.cancelCrop = cancelCrop;
window.removePhoto = removePhoto;
window.initCyrillicValidation = initCyrillicValidation;
window.isCyrillic = isCyrillic;

// Тестовая функция для отладки валидации кириллицы
window.testCyrillicValidation = function() {
    console.log('🧪 Testing cyrillic validation...');
    
    // Тестируем функцию проверки
    const testCases = [
        'Привет мир',      // ✅ должно пройти
        'Hello world',     // ❌ не должно пройти (латиница)
        'Тест 123',        // ✅ должно пройти
        'Test, test!',     // ❌ не должно пройти (латиница)
        'Работа (8:00-17:00)', // ✅ должно пройти
        'Программист',     // ✅ должно пройти
        'Developer',       // ❌ не должно пройти (латиница)
        'IT-специалист',   // ✅ должно пройти  
        'Web developer',   // ❌ не должно пройти (латиница)
        'Спорт, чтение, музыка', // ✅ должно пройти
        'Sport, reading',  // ❌ не должно пройти (латиница)
        'Москва',          // ✅ должно пройти
        'Moscow',          // ❌ не должно пройти (латиница)
        'Иван Иванович',   // ✅ должно пройти
        'John Smith',      // ❌ не должно пройти (латиница)
        '',                // ✅ пустое значение
        '   ',             // ✅ только пробелы
    ];
    
    console.log('📝 Testing cyrillic validation function:');
    testCases.forEach(test => {
        const result = isCyrillic(test);
        console.log(`  "${test}": ${result ? '✅ Valid' : '❌ Invalid'}`);
    });
    
    // Показываем текущее состояние полей
    console.log('📊 Current field status:');
    const allInputs = document.querySelectorAll('input[type="text"], textarea');
    const visibleInputs = Array.from(allInputs).filter(input => isElementVisible(input));
    console.log(`  Total inputs/textareas: ${allInputs.length}`);
    console.log(`  Visible inputs/textareas: ${visibleInputs.length}`);
    
    const cyrillicInputs = visibleInputs.filter(input => shouldValidateCyrillic(input));
    console.log(`  Fields that should validate cyrillic: ${cyrillicInputs.length}`);
    
    cyrillicInputs.forEach(input => {
        const identifier = getInputIdentifier(input);
        const hasHandler = input.dataset.cyrillicInit === 'true';
        console.log(`    ${identifier}: ${hasHandler ? '✅ Has handler' : '❌ No handler'}`);
    });
    
    // Переинициализируем валидацию
    console.log('🔄 Force reinitializing cyrillic validation...');
    initCyrillicValidation();
};

// Простая функция для принудительной переинициализации только валидации кириллицы
window.forceCyrillicValidation = function() {
    console.log('🚀 Force initializing ONLY cyrillic validation...');
    try {
        initCyrillicValidation();
        console.log('✅ Cyrillic validation force initialized');
    } catch (error) {
        console.error('❌ Error force initializing cyrillic validation:', error);
    }
};

// Автоматическая переинициализация каждые несколько секунд для отладки
window.startAutoReinit = function(intervalSeconds = 3) {
    console.log(`🔄 Starting auto-reinit every ${intervalSeconds} seconds...`);
    
    const interval = setInterval(() => {
        console.log('⏰ Auto-reinit: Checking for cyrillic validation...');
        
        // Проверяем, есть ли поля, которые должны иметь валидацию, но не имеют
        const inputs = document.querySelectorAll('input[type="text"], textarea');
        const visibleInputs = Array.from(inputs).filter(input => isElementVisible(input));
        const cyrillicInputs = visibleInputs.filter(input => shouldValidateCyrillic(input));
        const uninitializedInputs = cyrillicInputs.filter(input => input.dataset.cyrillicInit !== 'true');
        
        if (uninitializedInputs.length > 0) {
            console.log(`⚠️ Found ${uninitializedInputs.length} uninitialized cyrillic fields, reinitializing...`);
            
            uninitializedInputs.forEach(input => {
                const identifier = getInputIdentifier(input);
                console.log(`  🔧 Reinitializing: ${identifier}`);
            });
            
            try {
                initCyrillicValidation();
                console.log('✅ Auto-reinit: Cyrillic validation reinitialized');
            } catch (error) {
                console.error('❌ Auto-reinit: Error reinitializing cyrillic validation:', error);
            }
        } else {
            console.log(`✅ Auto-reinit: All ${cyrillicInputs.length} cyrillic fields are properly initialized`);
        }
    }, intervalSeconds * 1000);
    
    // Останавливаем автоматическую переинициализацию через 30 секунд
    setTimeout(() => {
        console.log('⏹️ Stopping auto-reinit after 30 seconds');
        clearInterval(interval);
    }, 30000);
    
    return interval;
};

// Функция для остановки автоматической переинициализации
window.stopAutoReinit = function(interval) {
    if (interval) {
        clearInterval(interval);
        console.log('⏹️ Auto-reinit stopped manually');
    }
};

// Улучшенная функция для проверки всех событий
window.debugAllEvents = function() {
    console.log('🔍 Debugging all events...');
    
    // Проверяем, какие обработчики событий зарегистрированы
    const eventTypes = [
        'livewire:updated',
        'livewire:message.processed',
        'livewire:morph.updated',
        'livewire:component.updated',
        'livewire:step-changed',
        'livewire:reinitialize-js',
        'step-changed',
        'reinitialize-js'
    ];
    
    eventTypes.forEach(eventType => {
        console.log(`📝 Testing event: ${eventType}`);
        
        // Создаем и отправляем тестовое событие
        const testEvent = new CustomEvent(eventType, {
            detail: { step: 999, test: true }
        });
        
        document.dispatchEvent(testEvent);
    });
    
    // Проверяем состояние валидации кириллицы
    setTimeout(() => {
        testCyrillicValidation();
    }, 1000);
};

// Отладка всех событий Livewire
if (typeof window !== 'undefined') {
    // Перехватываем регистрацию событий для отладки
    const originalAddEventListener = document.addEventListener;
    document.addEventListener = function(type, listener, options) {
        if (type.includes('livewire') || type.includes('step') || type.includes('reinitialize')) {
            console.log('📝 Registering event listener for:', type);
        }
        return originalAddEventListener.call(this, type, listener, options);
    };
    
    // Отслеживаем конкретные события Livewire
    const livewireEvents = [
        'livewire:updated',
        'livewire:message.processed',
        'livewire:morph.updated',
        'livewire:component.updated',
        'livewire:step-changed',
        'livewire:reinitialize-js'
    ];
    
    livewireEvents.forEach(eventType => {
        document.addEventListener(eventType, function(event) {
            console.log(`🔍 Livewire event detected: ${eventType}`, event.detail);
        });
    });
}

// Альтернативный механизм отслеживания изменений DOM
let lastStepElement = null;
let currentStepNumber = null;

function detectStepChange() {
    // Ищем активный шаг по различным селекторам
    const stepSelectors = [
        '.step:not(.hidden)',
        '[class*="step"]:not(.hidden)',
        'div[wire\\:if*="currentStep"]',
        '.current-step'
    ];
    
    let activeStep = null;
    for (const selector of stepSelectors) {
        activeStep = document.querySelector(selector);
        if (activeStep) break;
    }
    
    if (activeStep && activeStep !== lastStepElement) {
        console.log('🔄 Step change detected via DOM observation');
        lastStepElement = activeStep;
        
        // Попытаемся определить номер шага
        let stepNum = null;
        
        // Метод 1: ищем в тексте заголовка
        const stepHeader = activeStep.querySelector('h2, h1, .step-title');
        if (stepHeader) {
            const stepText = stepHeader.textContent;
            console.log('Step header text:', stepText);
        }
        
        // Метод 2: проверяем wire:if атрибуты
        const conditionalElements = document.querySelectorAll('[wire\\:if]');
        conditionalElements.forEach(el => {
            const condition = el.getAttribute('wire:if');
            if (condition && condition.includes('currentStep') && !el.classList.contains('hidden')) {
                const match = condition.match(/currentStep\s*===\s*(\d+)/);
                if (match) {
                    stepNum = parseInt(match[1]);
                    console.log('Detected step from wire:if:', stepNum);
                }
            }
        });
        
        if (stepNum && stepNum !== currentStepNumber) {
            currentStepNumber = stepNum;
            console.log(`🎯 Step changed to: ${stepNum} (DOM detection)`);
            
            // Инициализируем валидацию кириллицы
            setTimeout(() => {
                console.log(`🔤 DOM Step ${stepNum}: Reinitializing cyrillic validation...`);
                try {
                    initCyrillicValidation();
                    console.log(`✅ DOM Step ${stepNum}: Cyrillic validation reinitialized`);
                } catch (error) {
                    console.error(`❌ DOM Step ${stepNum}: Error reinitializing cyrillic validation:`, error);
                }
            }, 100);
        }
    }
}

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Candidate form JavaScript loading...');
    
    // Ждем Livewire
    function initWhenReady() {
        console.log('Checking for Livewire...');
        if (typeof Livewire !== 'undefined') {
            console.log('Livewire found, initializing...');
            initializeWithRetry();
            
            // Запускаем отслеживание изменений DOM
            console.log('🔍 Starting DOM step change detection...');
            setInterval(detectStepChange, 500); // Проверяем каждые 500ms
            
        } else {
            setTimeout(initWhenReady, 200);
        }
    }
    
    // Начинаем проверку через 500ms чтобы дать время Livewire загрузиться
    setTimeout(initWhenReady, 500);
});

// События Livewire для отслеживания изменений
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated event fired - reinitializing');
    isInitialized = false; // Сбрасываем флаг при навигации
    setTimeout(() => {
        initializeWithRetry();
    }, 200);
});

// Обработка обновлений компонента Livewire
document.addEventListener('livewire:updated', () => {
    console.log('📡 Livewire updated event fired - checking for new elements');
    
    // Добавляем переинициализацию валидации кириллицы при любом обновлении
    setTimeout(() => {
        console.log('🔤 Livewire updated: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Livewire updated: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Livewire updated: Error reinitializing cyrillic validation:', error);
        }
    }, 200);
    
    reinitializeOnDOMChange();
});

// Обработка завершения сообщений Livewire
document.addEventListener('livewire:message.processed', () => {
    console.log('📡 Livewire message processed - checking for new elements');
    
    // Дополнительная переинициализация валидации кириллицы
    setTimeout(() => {
        console.log('🔤 Message processed: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Message processed: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Message processed: Error reinitializing cyrillic validation:', error);
        }
    }, 250);
    
    reinitializeOnDOMChange();
});

// Дополнительные обработчики для надежности
document.addEventListener('livewire:morph.updated', () => {
    console.log('📡 Livewire morph updated - reinitializing components');
    
    // Переинициализация валидации кириллицы
    setTimeout(() => {
        console.log('🔤 Morph updated: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Morph updated: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Morph updated: Error reinitializing cyrillic validation:', error);
        }
    }, 150);
    
    setTimeout(() => {
        reinitializeAllComponents(null, 'morph-updated');
    }, 100);
});

document.addEventListener('livewire:component.updated', () => {
    console.log('📡 Livewire component updated - reinitializing components');
    
    // Переинициализация валидации кириллицы
    setTimeout(() => {
        console.log('🔤 Component updated: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Component updated: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Component updated: Error reinitializing cyrillic validation:', error);
        }
    }, 150);
    
    setTimeout(() => {
        reinitializeAllComponents(null, 'component-updated');
    }, 100);
});

// Обработка смены шагов - попробуем разные варианты имен событий
document.addEventListener('livewire:step-changed', (event) => {
    console.log('📢 Step changed event received:', event.detail);
    const newStep = event.detail?.step;
    
    // Специальная обработка для валидации кириллицы при смене шагов
    setTimeout(() => {
        console.log(`🔤 Step ${newStep}: Reinitializing cyrillic validation...`);
        try {
            initCyrillicValidation();
            console.log(`✅ Step ${newStep}: Cyrillic validation reinitialized`);
        } catch (error) {
            console.error(`❌ Step ${newStep}: Error reinitializing cyrillic validation:`, error);
        }
    }, 300); // Достаточная задержка для обновления DOM
    
    reinitializeAllComponents(newStep, 'step-changed');
});

// Альтернативное имя события для смены шагов (без префикса livewire:)
document.addEventListener('step-changed', (event) => {
    console.log('📢 Alternative step-changed event received:', event.detail);
    const newStep = event.detail?.step;
    
    setTimeout(() => {
        console.log(`🔤 Alt Step ${newStep}: Reinitializing cyrillic validation...`);
        try {
            initCyrillicValidation();
            console.log(`✅ Alt Step ${newStep}: Cyrillic validation reinitialized`);
        } catch (error) {
            console.error(`❌ Alt Step ${newStep}: Error reinitializing cyrillic validation:`, error);
        }
    }, 300);
    
    reinitializeAllComponents(newStep, 'alt-step-changed');
});

// Обработка переинициализации JS
document.addEventListener('livewire:reinitialize-js', (event) => {
    console.log('📢 Reinitialize JS event received');
    
    // Дополнительная переинициализация валидации кириллицы
    setTimeout(() => {
        console.log('🔤 Reinitialize-JS: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Reinitialize-JS: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Reinitialize-JS: Error reinitializing cyrillic validation:', error);
        }
    }, 350);
    
    reinitializeAllComponents(null, 'reinitialize-js');
});

// Альтернативное имя события переинициализации (без префикса livewire:)
document.addEventListener('reinitialize-js', (event) => {
    console.log('📢 Alternative reinitialize-js event received');
    
    setTimeout(() => {
        console.log('🔤 Alt Reinitialize-JS: Reinitializing cyrillic validation...');
        try {
            initCyrillicValidation();
            console.log('✅ Alt Reinitialize-JS: Cyrillic validation reinitialized');
        } catch (error) {
            console.error('❌ Alt Reinitialize-JS: Error reinitializing cyrillic validation:', error);
        }
    }, 350);
    
    reinitializeAllComponents(null, 'alt-reinitialize-js');
});

// Универсальная функция переинициализации
function reinitializeAllComponents(step = null, source = 'manual') {
    console.log(`🔄 Reinitializing all components (source: ${source}, step: ${step})...`);
    
    // Сбрасываем флаг инициализации для переинициализации компонентов
    isInitialized = false;
    
    // Переинициализируем все компоненты с задержкой
    setTimeout(() => {
        console.log(`🛠️ Starting reinitialization process (source: ${source})...`);
        
        // Переинициализируем основные компоненты
        console.log('🔧 Reinitializing main components...');
        initializeWithRetry(3, 1, true);
        
        // Дополнительная задержка перед инициализацией валидации кириллицы
        setTimeout(() => {
            console.log('🔤 Reinitializing cyrillic validation...');
            try {
                initCyrillicValidation();
                console.log('✅ Cyrillic validation reinitialized successfully');
            } catch (error) {
                console.error('❌ Error reinitializing cyrillic validation:', error);
            }
            
            console.log(`✅ All components reinitialized (source: ${source})`);
        }, 100);
        
    }, 200); // Уменьшаем общую задержку
}

// Делаем функции глобальными
window.reinitializeAllComponents = reinitializeAllComponents;

// Главная функция для быстрого тестирования всего
window.quickTest = function() {
    console.log('🚀 Starting quick test of cyrillic validation...');
    console.log('=====================================');
    
    // 1. Показываем текущее состояние
    testCyrillicValidation();
    
    // 2. Принудительно переинициализируем
    console.log('\n🔄 Force reinitializing...');
    forceCyrillicValidation();
    
    // 3. Проверяем результат
    setTimeout(() => {
        console.log('\n✅ After reinitialization:');
        testCyrillicValidation();
        
        console.log('\n📋 SUMMARY:');
        console.log('- If validation is now working, the issue was initialization timing');
        console.log('- Use forceCyrillicValidation() to reinitialize manually');
        console.log('- Use startAutoReinit() to enable automatic reinitialization');
        console.log('- Use testLatinInput() to test latin validation in real fields');
        console.log('- Use testHobbiesInterests() to test Hobbies & Interests validation');
        console.log('=====================================');
    }, 500);
};

// Функция для тестирования валидации латинских символов в реальных полях
window.testLatinInput = function() {
    console.log('🧪 Testing latin input validation in real fields...');
    
    // Находим все поля с валидацией кириллицы
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    const visibleInputs = Array.from(inputs).filter(input => isElementVisible(input));
    const cyrillicInputs = visibleInputs.filter(input => shouldValidateCyrillic(input));
    
    if (cyrillicInputs.length === 0) {
        console.log('❌ No cyrillic validation fields found');
        return;
    }
    
    console.log(`✅ Found ${cyrillicInputs.length} fields with cyrillic validation`);
    
    // Тестируем первое найденное поле
    const testField = cyrillicInputs[0];
    const originalValue = testField.value;
    const fieldId = testField.id || testField.getAttribute('wire:model');
    
    console.log(`🎯 Testing field: ${fieldId}`);
    console.log('📝 Testing latin input "Hello" (should show error)...');
    
    // Вводим латинский текст
    testField.value = 'Hello';
    testField.dispatchEvent(new Event('input', { bubbles: true }));
    
    // Проверяем через небольшую задержку
    setTimeout(() => {
        const errorElement = document.getElementById((testField.id || 'field') + '-cyrillic-error');
        const hasError = errorElement && errorElement.style.display !== 'none';
        
        console.log(`${hasError ? '✅' : '❌'} Error display: ${hasError ? 'SHOWN' : 'NOT SHOWN'}`);
        console.log(`${testField.classList.contains('border-red-500') ? '✅' : '❌'} Red border: ${testField.classList.contains('border-red-500') ? 'APPLIED' : 'NOT APPLIED'}`);
        
        // Тестируем кириллический текст
        console.log('📝 Testing cyrillic input "Привет" (should hide error)...');
        testField.value = 'Привет';
        testField.dispatchEvent(new Event('input', { bubbles: true }));
        
        setTimeout(() => {
            const errorElement2 = document.getElementById((testField.id || 'field') + '-cyrillic-error');
            const hasError2 = errorElement2 && errorElement2.style.display !== 'none';
            
            console.log(`${!hasError2 ? '✅' : '❌'} Error hidden: ${!hasError2 ? 'YES' : 'NO'}`);
            console.log(`${!testField.classList.contains('border-red-500') ? '✅' : '❌'} Red border removed: ${!testField.classList.contains('border-red-500') ? 'YES' : 'NO'}`);
            
            // Восстанавливаем оригинальное значение
            testField.value = originalValue;
            testField.dispatchEvent(new Event('input', { bubbles: true }));
            
            console.log('🔄 Original value restored');
            console.log('=====================================');
            console.log('🎉 Test completed! Check the results above.');
        }, 200);
    }, 200);
};

// Функция для проверки событий
window.testEvents = function() {
    console.log('🧪 Testing Livewire events...');
    
    debugAllEvents();
    
    console.log('\n⏰ Watch the console for event responses...');
    console.log('If you see event responses, events are working correctly.');
    console.log('If not, events might not be dispatched from PHP.');
};

// Функция для тестирования серверной валидации при нажатии "Далее"
window.testStepValidation = function() {
    console.log('🚀 Testing step validation (server-side)...');
    console.log('===============================================');
    
    // Проверяем, на каком шаге мы находимся
    const stepElements = document.querySelectorAll('[wire\\:if*="currentStep"]');
    let currentStep = null;
    
    stepElements.forEach(el => {
        const condition = el.getAttribute('wire:if');
        if (condition && !el.classList.contains('hidden')) {
            const match = condition.match(/currentStep\s*===\s*(\d+)/);
            if (match) {
                currentStep = parseInt(match[1]);
            }
        }
    });
    
    console.log(`📍 Current step: ${currentStep || 'Unknown'}`);
    
    if (currentStep === 2) {
        console.log('🎯 Perfect! You are on step 2 where Hobbies and Interests are located.');
        console.log('\n📝 Testing server validation:');
        console.log('1. Enter latin text in Hobbies field: "Reading books"');
        console.log('2. Enter latin text in Interests field: "Technology"');
        console.log('3. Click "Далее" button');
        console.log('4. Check if validation errors appear from server');
        
        // Автоматически заполняем поля для тестирования
        const hobbiesField = document.querySelector('textarea[wire\\:model="hobbies"]');
        const interestsField = document.querySelector('textarea[wire\\:model="interests"]');
        
        if (hobbiesField && interestsField) {
            console.log('\n🔧 Auto-filling fields with latin text for testing...');
            
            // Сохраняем оригинальные значения
            const originalHobbies = hobbiesField.value;
            const originalInterests = interestsField.value;
            
            // Заполняем латинским текстом
            hobbiesField.value = 'Reading books, playing games';
            interestsField.value = 'Technology, science';
            
            // Отправляем события в Livewire
            hobbiesField.dispatchEvent(new Event('input', { bubbles: true }));
            interestsField.dispatchEvent(new Event('input', { bubbles: true }));
            
            console.log('✅ Fields filled with latin text');
            console.log('🔘 Now click "Далее" button to test server validation');
            console.log('⚠️  Expected: Validation errors should appear');
            
            // Создаем функцию для восстановления оригинальных значений
            window.restoreOriginalValues = function() {
                hobbiesField.value = originalHobbies;
                interestsField.value = originalInterests;
                hobbiesField.dispatchEvent(new Event('input', { bubbles: true }));
                interestsField.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('🔄 Original values restored');
            };
            
            console.log('📝 Use restoreOriginalValues() to restore original text');
        } else {
            console.log('❌ Could not find Hobbies/Interests fields');
        }
    } else {
        console.log('⚠️  You need to be on step 2 to test Hobbies and Interests validation');
        console.log('   Navigate to step 2 and run this function again');
    }
    
    console.log('\n===============================================');
};

// Специальная функция для тестирования полей Хобби и Интересы
window.testHobbiesInterests = function() {
    console.log('🎯 Testing Hobbies and Interests validation...');
    console.log('================================================');
    
    // Ищем поля Хобби и Интересы
    const hobbiesField = document.querySelector('textarea[wire\\:model="hobbies"]');
    const interestsField = document.querySelector('textarea[wire\\:model="interests"]');
    
    console.log('🔍 Field detection:');
    console.log(`  Hobbies field: ${hobbiesField ? '✅ Found' : '❌ Not found'}`);
    console.log(`  Interests field: ${interestsField ? '✅ Found' : '❌ Not found'}`);
    
    if (!hobbiesField && !interestsField) {
        console.log('❌ No fields found. Make sure you are on step 2.');
        return;
    }
    
    // Проверяем, инициализированы ли поля
    if (hobbiesField) {
        console.log(`  Hobbies initialized: ${hobbiesField.dataset.cyrillicInit === 'true' ? '✅ Yes' : '❌ No'}`);
        console.log(`  Hobbies visible: ${isElementVisible(hobbiesField) ? '✅ Yes' : '❌ No'}`);
        console.log(`  Should validate cyrillic: ${shouldValidateCyrillic(hobbiesField) ? '✅ Yes' : '❌ No'}`);
    }
    
    if (interestsField) {
        console.log(`  Interests initialized: ${interestsField.dataset.cyrillicInit === 'true' ? '✅ Yes' : '❌ No'}`);
        console.log(`  Interests visible: ${isElementVisible(interestsField) ? '✅ Yes' : '❌ No'}`);
        console.log(`  Should validate cyrillic: ${shouldValidateCyrillic(interestsField) ? '✅ Yes' : '❌ No'}`);
    }
    
    // Принудительно инициализируем валидацию
    console.log('\n🔄 Force initializing validation...');
    forceCyrillicValidation();
    
    // Тестируем поле Хобби
    if (hobbiesField) {
        console.log('\n🧪 Testing Hobbies field...');
        const originalHobbies = hobbiesField.value;
        
        // Тест латинского текста
        hobbiesField.value = 'Reading books, playing games';
        hobbiesField.dispatchEvent(new Event('input', { bubbles: true }));
        
        setTimeout(() => {
            const errorElement = document.getElementById('hobbies-cyrillic-error') || 
                                 document.querySelector('[data-field="hobbies"].cyrillic-error');
            console.log(`  Latin text error: ${errorElement && errorElement.style.display !== 'none' ? '✅ Shown' : '❌ Not shown'}`);
            
            // Тест кириллического текста
            hobbiesField.value = 'Чтение книг, игры';
            hobbiesField.dispatchEvent(new Event('input', { bubbles: true }));
            
            setTimeout(() => {
                const errorElement2 = document.getElementById('hobbies-cyrillic-error') || 
                                     document.querySelector('[data-field="hobbies"].cyrillic-error');
                console.log(`  Cyrillic text error: ${!errorElement2 || errorElement2.style.display === 'none' ? '✅ Hidden' : '❌ Still shown'}`);
                
                // Восстанавливаем оригинальное значение
                hobbiesField.value = originalHobbies;
                hobbiesField.dispatchEvent(new Event('input', { bubbles: true }));
            }, 300);
        }, 300);
    }
    
    // Тестируем поле Интересы
    if (interestsField) {
        console.log('\n🧪 Testing Interests field...');
        const originalInterests = interestsField.value;
        
        setTimeout(() => {
            // Тест латинского текста
            interestsField.value = 'Technology, science';
            interestsField.dispatchEvent(new Event('input', { bubbles: true }));
            
            setTimeout(() => {
                const errorElement = document.getElementById('interests-cyrillic-error') || 
                                     document.querySelector('[data-field="interests"].cyrillic-error');
                console.log(`  Latin text error: ${errorElement && errorElement.style.display !== 'none' ? '✅ Shown' : '❌ Not shown'}`);
                
                // Тест кириллического текста
                interestsField.value = 'Технологии, наука';
                interestsField.dispatchEvent(new Event('input', { bubbles: true }));
                
                setTimeout(() => {
                    const errorElement2 = document.getElementById('interests-cyrillic-error') || 
                                         document.querySelector('[data-field="interests"].cyrillic-error');
                    console.log(`  Cyrillic text error: ${!errorElement2 || errorElement2.style.display === 'none' ? '✅ Hidden' : '❌ Still shown'}`);
                    
                    // Восстанавливаем оригинальное значение
                    interestsField.value = originalInterests;
                    interestsField.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    console.log('\n================================================');
                    console.log('🎉 Test completed! Check results above.');
                    console.log('If validation is not working, try: forceCyrillicValidation()');
                    console.log('================================================');
                }, 300);
            }, 300);
        }, 600);
    }
};

// Дополнительная инициализация при полной загрузке страницы
window.addEventListener('load', () => {
    console.log('Window load event fired');
    if (!isInitialized) {
        setTimeout(() => {
            initializeWithRetry();
        }, 300);
    }
});

// Отслеживание изменений DOM с помощью MutationObserver
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver((mutations) => {
        let shouldReinitialize = false;
        
        mutations.forEach((mutation) => {
            // Проверяем добавление новых элементов
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) { // Element node
                        // Проверяем, появились ли нужные элементы
                        if (node.id === 'phone-input' || node.id === 'photo-input' || 
                            node.querySelector && (node.querySelector('#phone-input') || node.querySelector('#photo-input'))) {
                            shouldReinitialize = true;
                        }
                    }
                });
            }
        });
        
        if (shouldReinitialize) {
            console.log('MutationObserver detected relevant DOM changes');
            reinitializeOnDOMChange();
        }
    });
    
    // Начинаем наблюдение после загрузки DOM
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            console.log('MutationObserver started');
        }, 1000);
    });
} 