@if($currentStep === 1)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Основная информация</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Фото -->
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Фото <span class="text-red-500">*</span>
            </label>
            
            <!-- Предпросмотр фото -->
            <div id="photo-preview" class="mb-4 {{ $photoPreview ? '' : 'hidden' }}">
                <div class="relative w-32 h-40 mx-auto">
                    <img id="preview-image" 
                         src="{{ $photoPreview }}" 
                         alt="Фото" 
                         class="w-full h-full object-cover rounded-lg shadow-md">
                    <button type="button"
                            onclick="removePhoto()"
                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Модальное окно для обрезки -->
            <div id="crop-modal" class="fixed inset-0 z-[9999] hidden">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black opacity-50"></div>
                    <div class="relative bg-white rounded-lg max-w-4xl w-full mx-auto z-[10000]">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold">Обрезать фото</h3>
                            <p class="text-sm text-gray-600 mt-1">Выберите область фото (пропорция 3:4)</p>
                        </div>
                        <div class="p-6">
                            <div class="max-h-[60vh] overflow-hidden">
                                <img id="crop-image" class="max-w-full">
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button type="button" 
                                    onclick="cancelCrop()"
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                Отмена
                            </button>
                            <button type="button" 
                                    onclick="saveCrop()"
                                    class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Сохранить
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Область загрузки -->
            <div id="upload-area" class="{{ $photoPreview ? 'hidden' : '' }}">
                <label for="photo-input" class="block cursor-pointer">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-semibold">Нажмите для загрузки</span> или перетащите файл
                        </p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG до 2MB (пропорция 3:4)</p>
                    </div>
                    <input id="photo-input" 
                           type="file" 
                           accept="image/png,image/jpeg,image/jpg"
                           class="hidden">
                    <!-- Скрытый input для Livewire как fallback -->
                    <input id="photo-livewire-fallback"
                           type="file"
                           wire:model="photo"
                           accept="image/png,image/jpeg,image/jpg"
                           class="hidden"
                           style="display: none !important;">
                </label>
            </div>

            <!-- Индикатор загрузки -->
            <div id="loading-indicator" class="mt-2 hidden">
                <div class="flex items-center justify-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                    <span class="text-sm text-gray-600">Обработка...</span>
                </div>
            </div>

            @error('photo') 
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            
            <!-- Скрытая кнопка для удаления фото (fallback) -->
            <button id="hidden-remove-photo-btn" 
                    wire:click="removePhoto" 
                    type="button"
                    style="display: none !important;">
            </button>
        </div>

        <!-- ФИО -->
        <div class="col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Фамилия -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Фамилия <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="last_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Иванов">
                    @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Имя -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Имя <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="first_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Иван">
                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Отчество -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Отчество
                    </label>
                    <input type="text" 
                           wire:model="middle_name" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Иванович">
                    @error('middle_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   wire:model="email" 
                   readonly
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-not-allowed">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Телефон -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Телефон <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   wire:model.lazy="phone" 
                   id="phone-input"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="+7 (___) ___-__-__">
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Пол -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Пол <span class="text-red-500">*</span>
            </label>
            <select wire:model="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Выберите пол</option>
                <option value="male">Мужской</option>
                <option value="female">Женский</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Семейное положение -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Семейное положение <span class="text-red-500">*</span>
            </label>
            <select wire:model="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Выберите семейное положение</option>
                <option value="single">Холост/Не замужем</option>
                <option value="married">Женат/Замужем</option>
                <option value="divorced">Разведен(а)</option>
                <option value="widowed">Вдовец/Вдова</option>
            </select>
            @error('marital_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Дата рождения -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Дата рождения <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   wire:model="birth_date" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Место рождения -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Место рождения <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   wire:model="birth_place" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="г. Москва">
            @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Текущий город -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Текущий город <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   wire:model="current_city" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="г. Москва">
            @error('current_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
@endif

@push('styles')
<!-- CSS уже подключен в layout -->
@endpush

@push('scripts')
<script src="https://unpkg.com/imask"></script>
<script>
// Проверяем, чтобы скрипт не выполнялся дважды
if (typeof window.candidateFormPhotoHandlerLoaded === 'undefined') {
    window.candidateFormPhotoHandlerLoaded = true;

let stepCropper = null;
let currentFile = null;
let livewireComponent = null;

// Глобальная функция для получения Livewire компонента
function getLivewireComponent() {
    if (livewireComponent) return livewireComponent;
    
    console.log('Searching for Livewire component...');
    console.log('Livewire available:', typeof Livewire !== 'undefined');
    
    try {
        // Способ 1: ищем компонент CandidateForm по имени класса и наличию нужных методов
        if (typeof Livewire !== 'undefined' && Livewire.all) {
            const allComponents = Livewire.all();
            console.log('All Livewire components:', allComponents.length);
            
            // Сначала ищем точно по имени candidate-form
            for (let component of allComponents) {
                console.log('Component:', component.name, component);
                
                if (component.name === 'candidate-form') {
                    console.log('Found exact candidate-form component:', component);
                    livewireComponent = component;
                    return livewireComponent;
                }
            }
            
            // Если не найден точный, ищем по другим критериям
            for (let component of allComponents) {
                // Проверяем наличие нужных методов/свойств для работы с фото
                const hasPhotoSupport = (component.get && 
                    (component.get('photo') !== undefined || 
                     component.get('photoPreview') !== undefined ||
                     component.get('currentStep') !== undefined)) ||
                    (component.call && typeof component.upload === 'function');
                
                // Ищем компонент по имени или по наличию нужных свойств
                if (hasPhotoSupport && (
                    component.name === 'CandidateForm' ||
                    component.name.includes('candidate-form') ||
                    component.name.includes('CandidateForm') ||
                    component.name.includes('candidate') ||
                    // Исключаем navigation-menu и другие системные компоненты
                    (!component.name.includes('navigation') && !component.name.includes('menu')))) {
                    console.log('Found CandidateForm component with photo support:', component);
                    livewireComponent = component;
                    return livewireComponent;
                }
            }
        }
        
        // Способ 1.5: ищем компонент по специфичным элементам
        const stepElement = document.querySelector('#photo-input, #photo-preview, #crop-modal');
        if (stepElement) {
            let currentElement = stepElement;
            while (currentElement && currentElement !== document.body) {
                if (currentElement.hasAttribute('wire:id')) {
                    const wireId = currentElement.getAttribute('wire:id');
                    console.log('Found wire:id via step element:', wireId);
                    
                    if (typeof Livewire !== 'undefined' && Livewire.find) {
                        const component = Livewire.find(wireId);
                        if (component) {
                            console.log('Found component via step element:', component);
                            livewireComponent = component;
                            return livewireComponent;
                        }
                    }
                }
                currentElement = currentElement.parentElement;
            }
        }
        
        // Способ 2: ищем по wire:id только внутри формы
        const formElement = document.querySelector('form[wire\\:submit]');
        if (formElement) {
            const wireElements = formElement.querySelectorAll('[wire\\:id]');
            console.log('Found wire:id elements in form:', wireElements.length);
            
            if (wireElements.length > 0) {
                for (let element of wireElements) {
                    const wireId = element.getAttribute('wire:id');
                    console.log('Trying wire:id in form:', wireId);
                    
                    if (typeof Livewire !== 'undefined' && Livewire.find) {
                        const component = Livewire.find(wireId);
                        if (component) {
                            console.log('Found component in form via wire:id:', wireId, component);
                            livewireComponent = component;
                            return livewireComponent;
                        }
                    }
                }
            }
        }
        
        // Способ 3: ищем по wire:id в candidate-form элементах
        const candidateFormElements = document.querySelectorAll('[wire\\:id]');
        console.log('Found wire:id elements:', candidateFormElements.length);
        
        if (candidateFormElements.length > 0) {
            for (let element of candidateFormElements) {
                const wireId = element.getAttribute('wire:id');
                console.log('Trying wire:id:', wireId);
                
                if (typeof Livewire !== 'undefined' && Livewire.find) {
                    const component = Livewire.find(wireId);
                    if (component && component.name && 
                        (component.name.includes('candidate') || component.name.includes('form'))) {
                        console.log('Found candidate component via wire:id:', wireId, component);
                        livewireComponent = component;
                        return livewireComponent;
                    }
                }
            }
        }
        
        console.error('CandidateForm component not found');
        return null;
    } catch (error) {
        console.error('Error getting Livewire component:', error);
        return null;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing photo upload...');
    
    // Ждем полной загрузки Livewire с таймаутом
    function waitForLivewire() {
        const checkLivewire = setInterval(() => {
            console.log('Checking for Livewire components...');
            
            // Выводим все доступные компоненты для отладки
            if (typeof Livewire !== 'undefined' && Livewire.all) {
                const allComponents = Livewire.all();
                console.log('Available components:');
                allComponents.forEach((comp, index) => {
                    console.log(`${index}: ${comp.name}`, comp);
                });
            }
            
            const component = getLivewireComponent();
            if (component) {
                clearInterval(checkLivewire);
                console.log('Livewire component ready:', component.name, component);
                livewireComponent = component;
            }
        }, 500); // Увеличил интервал до 500ms
        
        // Максимум ждем 15 секунд
        setTimeout(() => {
            clearInterval(checkLivewire);
            if (!livewireComponent) {
                console.warn('CandidateForm component not found after 15 seconds');
                // Показываем все доступные компоненты для диагностики
                if (typeof Livewire !== 'undefined' && Livewire.all) {
                    const allComponents = Livewire.all();
                    console.log('Final available components:', allComponents.map(c => c.name));
                }
            }
        }, 15000);
    }
    
    // Слушаем стандартное событие Livewire
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized');
        waitForLivewire();
    });
    
    // Также пробуем сразу, если Livewire уже загружен
    if (typeof Livewire !== 'undefined') {
        console.log('Livewire already available');
        waitForLivewire();
    }
    
    // Инициализация IMask для телефона
    const phoneInput = document.getElementById('phone-input');
    if (phoneInput) {
        const phoneMask = IMask(phoneInput, {
            mask: '+7 (000) 000-00-00',
            lazy: false // показывать маску сразу
        });

        // Синхронизация с Livewire
        phoneMask.on('accept', function() {
            // Отправляем значение в Livewire компонент
            phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // Если в поле уже есть значение, применяем маску
        if (phoneInput.value) {
            phoneMask.value = phoneInput.value;
        }

        // Слушаем обновления от Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', (message, component) => {
                if (phoneInput.value && phoneMask) {
                    phoneMask.value = phoneInput.value;
                }
            });
        }
    }

    // Обработка загрузки фото
    const photoInput = document.getElementById('photo-input');
    const uploadArea = document.getElementById('upload-area');
    const photoPreview = document.getElementById('photo-preview');
    const previewImage = document.getElementById('preview-image');
    const cropModal = document.getElementById('crop-modal');
    const cropImage = document.getElementById('crop-image');
    const loadingIndicator = document.getElementById('loading-indicator');

    // Обработка выбора файла
    if (photoInput) {
        photoInput.addEventListener('change', handleFileSelect);
    }

    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            console.log('File selected:', file.name, file.size, file.type);
            
            // Проверяем размер файла (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('Размер файла не должен превышать 2MB');
                photoInput.value = '';
                return;
            }
            
            // Проверяем тип файла
            if (!file.type.match(/image\/(jpeg|jpg|png)/)) {
                alert('Загружаемый файл должен быть изображением (JPG, JPEG, PNG)');
                photoInput.value = '';
                return;
            }
            
            currentFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('File loaded, setting image source');
                cropImage.src = e.target.result;
                cropModal.classList.remove('hidden');
                console.log('Modal should be visible now');
                
                // Инициализация Cropper
                if (stepCropper) {
                    stepCropper.destroy();
                }
                
                // Даем время для отображения модального окна
                setTimeout(() => {
                    console.log('Initializing Cropper.js');
                    if (typeof Cropper === 'undefined') {
                        console.error('Cropper.js is not loaded!');
                        alert('Ошибка: библиотека обрезки не загружена');
                        return;
                    }
                    
                    stepCropper = new Cropper(cropImage, {
                        aspectRatio: 3 / 4, // Пропорция 3:4
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                    console.log('Cropper initialized:', stepCropper);
                }, 100);
            };
            reader.readAsDataURL(file);
        }
    }

    // Drag and drop функциональность
    if (uploadArea) {
        const dropZone = uploadArea.querySelector('label > div');
        
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    const file = files[0];
                    
                    // Проверяем размер файла
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Размер файла не должен превышать 2MB');
                        return;
                    }
                    
                    // Проверяем тип файла
                    if (!file.type.match(/image\/(jpeg|jpg|png)/)) {
                        alert('Загружаемый файл должен быть изображением (JPG, JPEG, PNG)');
                        return;
                    }
                    
                    currentFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        cropImage.src = e.target.result;
                        cropModal.classList.remove('hidden');
                        
                        if (stepCropper) {
                            stepCropper.destroy();
                        }
                        
                        // Даем время для отображения модального окна
                        setTimeout(() => {
                            stepCropper = new Cropper(cropImage, {
                                aspectRatio: 3 / 4,
                                viewMode: 1,
                                dragMode: 'move',
                                autoCropArea: 1,
                                restore: false,
                                guides: true,
                                center: true,
                                highlight: false,
                                cropBoxMovable: true,
                                cropBoxResizable: true,
                                toggleDragModeOnDblclick: false,
                            });
                        }, 100);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    }

    // Слушаем события Livewire об обновлении фото
    if (typeof Livewire !== 'undefined') {
        Livewire.on('photoUploaded', () => {
            const preview = document.getElementById('photo-preview');
            const upload = document.getElementById('upload-area');
            const loading = document.getElementById('loading-indicator');
            if (loading) loading.classList.add('hidden');
            if (preview && upload) {
                preview.classList.remove('hidden');
                upload.classList.add('hidden');
            }
        });
        
        Livewire.on('photoRemoved', () => {
            const preview = document.getElementById('photo-preview');
            const upload = document.getElementById('upload-area');
            if (preview && upload) {
                preview.classList.add('hidden');
                upload.classList.remove('hidden');
            }
        });
    }
});

// Функция сохранения обрезанного изображения
function saveCrop() {
    const component = getLivewireComponent();
    
    if (stepCropper && component) {
        // Проверяем, что у компонента есть метод upload
        if (typeof component.upload !== 'function') {
            console.error('Component does not have upload method:', component.name);
            console.log('Available methods:', Object.getOwnPropertyNames(component));
            
            // Альтернативный способ: используем canvas и Livewire wire:model
            saveCropViaLivewire();
            return;
        }
        
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        
        // Получаем обрезанное изображение
        stepCropper.getCroppedCanvas({
            width: 300,  // ширина результата
            height: 400, // высота результата (пропорция 3:4)
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
            // Создаем File объект из blob
            const file = new File([blob], currentFile.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Отправляем файл в Livewire
            component.upload('photo', file, 
                (uploadedFilename) => {
                    // Успешная загрузка
                    console.log('Uploaded:', uploadedFilename);
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }, 
                () => {
                    // Ошибка загрузки
                    console.error('Upload error');
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    alert('Ошибка загрузки файла');
                }, 
                (event) => {
                    // Прогресс загрузки
                    console.log('Progress:', event.detail.progress + '%');
                }
            );
            
            // Закрываем модальное окно
            cancelCrop();
        }, 'image/jpeg', 0.9);
    } else {
        console.error('Cropper or Livewire component not available');
        console.log('Cropper available:', !!stepCropper);
        console.log('Livewire component available:', !!component);
        
        // Fallback: используем простой способ
        saveCropViaLivewire();
    }
}

// Альтернативная функция сохранения через Livewire wire:model
function saveCropViaLivewire() {
    if (!stepCropper) {
        alert('Ошибка: обрезка не инициализирована');
        return;
    }
    
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
    
    // Получаем обрезанное изображение как canvas
    stepCropper.getCroppedCanvas({
        width: 300,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    }).toBlob(function(blob) {
        // Создаем File объект
        const file = new File([blob], currentFile.name, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });
        
        // Используем скрытый input с wire:model
        const fallbackInput = document.getElementById('photo-livewire-fallback');
        if (fallbackInput) {
            // Создаем FileList и присваиваем файл
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fallbackInput.files = dataTransfer.files;
            
            // Триггерим событие change для Livewire
            fallbackInput.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Закрываем модальное окно
            cancelCrop();
            
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            console.log('Cropped photo uploaded via Livewire wire:model');
        } else {
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            alert('Ошибка: не удается загрузить обрезанное фото');
        }
    }, 'image/jpeg', 0.9);
}

// Функция отмены обрезки
function cancelCrop() {
    const cropModal = document.getElementById('crop-modal');
    const photoInput = document.getElementById('photo-input');
    
    if (stepCropper) {
        stepCropper.destroy();
        stepCropper = null;
    }
    
    if (cropModal) cropModal.classList.add('hidden');
    if (photoInput) photoInput.value = '';
    currentFile = null;
}

// Функция удаления фото
function removePhoto() {
    const component = getLivewireComponent();
    
    console.log('removePhoto called, component:', component);
    
    // Способ 1: через метод call (если доступен)
    if (component && typeof component.call === 'function') {
        console.log('Using component.call method');
        component.call('removePhoto');
    } 
    // Способ 2: через прямой вызов метода (если доступен)
    else if (component && typeof component.removePhoto === 'function') {
        console.log('Using direct method call');
        component.removePhoto();
    }
    // Способ 3: через Livewire.dispatch/emit
    else if (typeof Livewire !== 'undefined') {
        console.log('Using Livewire.dispatch fallback');
        // Пробуем разные способы отправки события
        if (typeof Livewire.dispatch === 'function') {
            Livewire.dispatch('removePhoto');
        } else if (typeof Livewire.emit === 'function') {
            Livewire.emit('removePhoto');
        } else if (component && typeof component.$dispatch === 'function') {
            component.$dispatch('removePhoto');
        }
    }
    // Способ 4: через wire:click эмуляцию
    else {
        console.log('Using wire:click fallback');
        // Создаем скрытую кнопку с wire:click и кликаем по ней
        let hiddenButton = document.getElementById('hidden-remove-photo-btn');
        if (!hiddenButton) {
            hiddenButton = document.createElement('button');
            hiddenButton.id = 'hidden-remove-photo-btn';
            hiddenButton.setAttribute('wire:click', 'removePhoto');
            hiddenButton.style.display = 'none';
            document.body.appendChild(hiddenButton);
        }
        hiddenButton.click();
    }
    
    // Обновляем UI немедленно (оптимистичное обновление)
    const preview = document.getElementById('photo-preview');
    const upload = document.getElementById('upload-area');
    const input = document.getElementById('photo-input');
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    
    if (preview && upload) {
        preview.classList.add('hidden');
        upload.classList.remove('hidden');
    }
    
    if (input) {
        input.value = '';
    }
    
    if (fallbackInput) {
        fallbackInput.value = '';
    }
    
    console.log('Photo removal UI updated');
}
} // Конец проверки window.candidateFormPhotoHandlerLoaded
</script>
@endpush 