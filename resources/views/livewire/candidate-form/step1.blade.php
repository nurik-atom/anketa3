@if($currentStep === 1)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Основная информация</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Фото -->
        <div class="col-span-1 lg:col-span-2">
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
        <div class="col-span-1 lg:col-span-2">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Фамилия -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Фамилия <span class="text-gray-500">(на кириллице)</span><span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="last-name-input"
                           wire:model="last_name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Иванов">
                    @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Имя -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Имя <span class="text-gray-500">(на кириллице)</span><span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="first-name-input"
                           wire:model="first_name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Иван">
                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Отчество -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Отчество <span class="text-gray-500">(на кириллице)</span>
                    </label>
                    <input type="text"
                           id="middle-name-input"
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
                <option value="Мужской">Мужской</option>
                <option value="Женский">Женский</option>
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
                <option value="Холост/Не замужем">Холост/Не замужем</option>
                <option value="Женат/Замужем">Женат/Замужем</option>
                <option value="Разведен(а)">Разведен(а)</option>
                <option value="Вдовец/Вдова">Вдовец/Вдова</option>
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
                Место рождения <span class="text-gray-500">(на кириллице)</span><span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="birth-place-input"
                   wire:model="birth_place"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="г. Москва">
            @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Текущий город -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Текущий город <span class="text-gray-500">(на кириллице)</span><span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="current-city-input"
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
<style>
/* Стили для ошибок кириллицы */
.cyrillic-error {
    font-weight: 500;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Стили для полей с ошибкой кириллицы */
.border-red-500 {
    border-color: #ef4444 !important;
}

.focus\:border-red-500:focus {
    border-color: #ef4444 !important;
}

.focus\:ring-red-500:focus {
    --tw-ring-color: #ef4444 !important;
}

/* Добавляем небольшое расстояние между ошибками */
.cyrillic-error + .text-red-500 {
    margin-top: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
// Простая проверка загрузки - fallback код убран так как основной JavaScript работает
console.log('Step1 template loaded - cyrillic validation handled by candidate-form.js');

// Проверяем доступность функций для отладки
console.log('JavaScript functions available:', {
    initCyrillicValidation: typeof window.initCyrillicValidation,
    isCyrillic: typeof window.isCyrillic
});
</script>
@endpush
