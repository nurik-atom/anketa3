import IMask from 'imask';

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

    // Простой поиск по wire:id
    const wireElements = document.querySelectorAll('[wire\\:id]');
    
    for (let element of wireElements) {
        const wireId = element.getAttribute('wire:id');
        if (wireId && Livewire.find) {
            const component = Livewire.find(wireId);
            if (component) {
                console.log('Found Livewire component:', component);
                return component;
            }
        }
    }
    
    console.error('No Livewire component found');
    return null;
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
    const component = getLivewireComponent();
    
    console.log('removePhoto called');
    
    if (component && component.call) {
        component.call('removePhoto');
    } else {
        // Fallback через скрытую кнопку
        const removeBtn = document.getElementById('hidden-remove-photo-btn');
        if (removeBtn) {
            removeBtn.click();
        }
    }
    
    // Обновляем UI
    const preview = document.getElementById('photo-preview');
    const upload = document.getElementById('upload-area');
    const photoInput = document.getElementById('photo-input');
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    
    if (preview && upload) {
        preview.classList.add('hidden');
        upload.classList.remove('hidden');
    }
    
    if (photoInput) photoInput.value = '';
    if (fallbackInput) fallbackInput.value = '';
    
    console.log('Photo removed');
}

// Функция инициализации всех компонентов
function initializeComponents() {
    if (isInitialized) {
        console.log('Components already initialized, skipping...');
        return;
    }
    
    console.log('Initializing components...');
    initPhoneMask();
    initPhotoUpload();
    isInitialized = true;
    console.log('All components initialized');
}

// Делаем функции глобальными для использования в HTML
window.saveCrop = saveCrop;
window.cancelCrop = cancelCrop;
window.removePhoto = removePhoto;

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Candidate form JavaScript loading...');
    
    // Ждем Livewire
    function initWhenReady() {
        console.log('Checking for Livewire...');
        if (typeof Livewire !== 'undefined') {
            console.log('Livewire found, initializing...');
            initializeComponents();
        } else {
            setTimeout(initWhenReady, 200);
        }
    }
    
    // Начинаем проверку через 500ms чтобы дать время Livewire загрузиться
    setTimeout(initWhenReady, 500);
});

// Только для случаев когда Livewire перезагружает страницу
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated event fired - reinitializing');
    isInitialized = false; // Сбрасываем флаг при навигации
    setTimeout(() => {
        initializeComponents();
    }, 100);
}); 