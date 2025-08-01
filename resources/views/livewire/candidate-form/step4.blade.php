@if($currentStep === 4)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Тесты и подтверждение</h2>

    <!-- Контейнер для PDF и MBTI в одну строку -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8 mb-8">
        <!-- Gallup тест - левая половина -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Gallup PDF</h3>
            <div class="w-full" x-data="fileUpload()">
                <!-- Область загрузки файла -->
                <div x-show="!fileUploaded">
                    <label class="group flex justify-center w-full h-32 px-4 py-6 transition-all duration-300 ease-in-out bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer focus:outline-none transform hover:scale-[1.02] hover:shadow-lg hover:border-blue-400 hover:bg-blue-50/50"
                           @dragover.prevent="isDragOver = true"
                           @dragleave.prevent="isDragOver = false"
                           @drop.prevent="handleDrop($event)"
                           :class="isDragOver ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 shadow-2xl scale-[1.02] animate-pulse ring-2 ring-blue-400 ring-opacity-50' : 'border-gray-300'">
                        <span class="flex flex-col items-center space-y-2 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 class="w-8 h-8 text-gray-600 transition-all duration-300 group-hover:text-blue-600 group-hover:scale-110 group-hover:rotate-6" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                 :class="isDragOver ? 'text-blue-600 scale-110 rotate-12 animate-bounce' : 'text-gray-600'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="font-medium text-gray-600 transition-all duration-300 group-hover:text-blue-700 text-center"
                                  :class="isDragOver ? 'text-blue-700' : 'text-gray-600'">
                                <span x-show="!isDragOver" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="block">
                                    <span class="block text-sm">Перетащите файлы или</span>
                                    <span class="text-blue-600 underline hover:text-blue-800 text-sm">выберите</span>
                                </span>
                                <span x-show="isDragOver" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="text-blue-700 font-semibold">
                                    📎 Отпустите для загрузки PDF файла
                                </span>
                            </span>
                        </span>
                        <input type="file" 
                               wire:model="gallup_pdf" 
                               class="hidden" 
                               accept=".pdf"
                               @change="handleFileChange($event)"
                               x-ref="fileInput">
                    </label>
                </div>
                @error('gallup_pdf') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Информация о загруженном файле -->
                <div x-show="fileUploaded" x-transition class="mt-4">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
                        <!-- Заголовок -->
                        <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">PDF файл загружен</h4>
                                    <p class="text-sm text-gray-500">Gallup результаты готовы к отправке</p>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="removeFile()"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Информация о файле -->
                        <div class="p-4">
                            <div class="grid grid-cols-1 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-sm text-gray-500 mb-1">Имя файла</div>
                                    <div class="font-medium text-gray-900 text-sm" x-text="fileName || 'Gallup результаты.pdf'"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-sm text-gray-500 mb-1">Размер</div>
                                        <div class="font-medium text-gray-900 text-sm" x-text="fileSize || 'Загружен'"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-sm text-gray-500 mb-1">Статус</div>
                                        <div class="font-medium text-sm" 
                                             x-bind:class="isExistingFile ? 'text-green-600' : 'text-blue-600'"
                                             x-text="isExistingFile ? '✓ Сохранен' : '📤 Загружен'"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Кнопка для скачивания/просмотра если это существующий файл -->
                            <div x-show="isExistingFile && downloadUrl" class="mt-3">
                                <a x-bind:href="downloadUrl" 
                                   target="_blank" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Скачать файл
                                </a>
                            </div>
                            
                            <!-- Информация для нового файла -->
                            <div x-show="!isExistingFile" class="mt-3">
                                <div class="text-sm text-blue-600 bg-blue-50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Файл будет сохранен при переходе к следующему шагу
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MBTI тип личности - правая половина -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Тип личности MBTI</h3>
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Выберите ваш тип MBTI</label>
                    <select wire:model="mbti_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Выберите тип MBTI</option>
                        <optgroup label="Аналитики">
                            <option value="INTJ-A">INTJ-A - Архитектор (Уверенный)</option>
                            <option value="INTJ-T">INTJ-T - Архитектор (Турбулентный)</option>
                            <option value="INTP-A">INTP-A - Мыслитель (Уверенный)</option>
                            <option value="INTP-T">INTP-T - Мыслитель (Турбулентный)</option>
                            <option value="ENTJ-A">ENTJ-A - Командир (Уверенный)</option>
                            <option value="ENTJ-T">ENTJ-T - Командир (Турбулентный)</option>
                            <option value="ENTP-A">ENTP-A - Полемист (Уверенный)</option>
                            <option value="ENTP-T">ENTP-T - Полемист (Турбулентный)</option>
                        </optgroup>
                        <optgroup label="Дипломаты">
                            <option value="INFJ-A">INFJ-A - Активист (Уверенный)</option>
                            <option value="INFJ-T">INFJ-T - Активист (Турбулентный)</option>
                            <option value="INFP-A">INFP-A - Посредник (Уверенный)</option>
                            <option value="INFP-T">INFP-T - Посредник (Турбулентный)</option>
                            <option value="ENFJ-A">ENFJ-A - Тренер (Уверенный)</option>
                            <option value="ENFJ-T">ENFJ-T - Тренер (Турбулентный)</option>
                            <option value="ENFP-A">ENFP-A - Борец (Уверенный)</option>
                            <option value="ENFP-T">ENFP-T - Борец (Турбулентный)</option>
                        </optgroup>
                        <optgroup label="Хранители">
                            <option value="ISTJ-A">ISTJ-A - Логист (Уверенный)</option>
                            <option value="ISTJ-T">ISTJ-T - Логист (Турбулентный)</option>
                            <option value="ISFJ-A">ISFJ-A - Защитник (Уверенный)</option>
                            <option value="ISFJ-T">ISFJ-T - Защитник (Турбулентный)</option>
                            <option value="ESTJ-A">ESTJ-A - Менеджер (Уверенный)</option>
                            <option value="ESTJ-T">ESTJ-T - Менеджер (Турбулентный)</option>
                            <option value="ESFJ-A">ESFJ-A - Консул (Уверенный)</option>
                            <option value="ESFJ-T">ESFJ-T - Консул (Турбулентный)</option>
                        </optgroup>
                        <optgroup label="Искатели">
                            <option value="ISTP-A">ISTP-A - Виртуоз (Уверенный)</option>
                            <option value="ISTP-T">ISTP-T - Виртуоз (Турбулентный)</option>
                            <option value="ISFP-A">ISFP-A - Авантюрист (Уверенный)</option>
                            <option value="ISFP-T">ISFP-T - Авантюрист (Турбулентный)</option>
                            <option value="ESTP-A">ESTP-A - Делец (Уверенный)</option>
                            <option value="ESTP-T">ESTP-T - Делец (Турбулентный)</option>
                            <option value="ESFP-A">ESFP-A - Развлекатель (Уверенный)</option>
                            <option value="ESFP-T">ESFP-T - Развлекатель (Турбулентный)</option>
                        </optgroup>
                    </select>
                    @error('mbti_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <!-- Дополнительная информация о MBTI -->
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">О тесте MBTI</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="mb-2">Выберите ваш тип личности согласно результатам теста Myers-Briggs Type Indicator.</p>
                                <p>Если вы еще не знаете свой тип, вы можете <a href="https://www.16personalities.com/ru/test-lichnosti" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-800 underline hover:text-blue-900">пройти бесплатный тест здесь</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function fileUpload() {
    return {
        fileUploaded: false,
        fileName: '',
        fileSize: '',
        isExistingFile: false,
        downloadUrl: '',
        isDragOver: false,
        
        init() {
            console.log('Step 4 file upload component initialized');
            
            // Проверяем, есть ли уже загруженный файл при инициализации
            setTimeout(() => {
                this.checkExistingFile();
            }, 100);
            
            // Слушаем обновления Livewire
            this.$nextTick(() => {
                window.addEventListener('livewire:updated', () => {
                    setTimeout(() => {
                        this.checkExistingFile();
                        this.checkForErrors();
                    }, 100);
                });

                // Слушаем событие сброса файла
                this.$wire.on('gallup-file-reset', () => {
                    console.log('Gallup file reset event received');
                    this.resetFileState();
                });
            });
        },

        checkExistingFile() {
            console.log('Checking for existing Gallup file...');
            
            // Проверяем есть ли файл в Livewire компоненте
            if (typeof @this !== 'undefined') {
                // Получаем информацию о файле из Livewire
                const livewireGallupPdf = @this.get('gallup_pdf');
                const candidateGallupPdf = @this.get('candidate.gallup_pdf');
                
                console.log('Livewire gallup_pdf:', livewireGallupPdf);
                console.log('Candidate gallup_pdf:', candidateGallupPdf);
                
                // Если есть любой файл, получаем информацию через PHP
                if (livewireGallupPdf || candidateGallupPdf) {
                    @this.call('getGallupFileInfo').then(fileInfo => {
                        if (fileInfo) {
                            this.fileUploaded = true;
                            this.isExistingFile = fileInfo.isExisting;
                            this.fileName = fileInfo.fileName;
                            this.fileSize = fileInfo.fileSize;
                            
                            console.log('File info received:', fileInfo);
                            
                            // Если это существующий файл, получаем URL для скачивания
                            if (fileInfo.isExisting) {
                                @this.call('getGallupPdfUrl').then(url => {
                                    if (url) {
                                        this.downloadUrl = url;
                                        console.log('Download URL set:', url);
                                    }
                                }).catch(error => {
                                    console.log('Error getting download URL:', error);
                                });
                            }
                            
                            return;
                        }
                    }).catch(error => {
                        console.log('Error getting file info:', error);
                    });
                    
                    return;
                }
            }
            
            console.log('No existing file found');
        },

        checkForErrors() {
            // Проверяем есть ли ошибки валидации для gallup_pdf
            if (typeof @this !== 'undefined') {
                const errors = @this.get('errors');
                if (errors && errors.gallup_pdf) {
                    console.log('Gallup PDF validation error detected, hiding file block');
                    // Скрываем блок загруженного файла при ошибке
                    this.fileUploaded = false;
                    this.isExistingFile = false;
                    this.fileName = '';
                    this.fileSize = '';
                    this.downloadUrl = '';
                    this.isDragOver = false;
                    
                    // Очищаем input
                    const fileInput = this.$el.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }
            }
        },
        
        extractFileNameFromPath(path) {
            if (!path) return 'Gallup результаты';
            
            // Извлекаем имя файла из пути
            const pathParts = path.split('/');
            const fileName = pathParts[pathParts.length - 1];
            
            // Убираем timestamp префикс если есть
            const cleanName = fileName.replace(/^\d+_/, '');
            
            return cleanName || 'Gallup результаты.pdf';
        },

        handleDrop(event) {
            this.isDragOver = false;
            
            const files = event.dataTransfer.files;
            if (!files.length) return;
            
            const file = files[0];
            
            if (file.type !== 'application/pdf') {
                alert('Пожалуйста, выберите PDF файл');
                return;
            }
            
            // Устанавливаем файл в input для Livewire
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                // Создаем новый FileList для input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                // Триггерим событие change для Livewire
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // Показываем информацию о файле
            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
            
            console.log('File dropped:', file.name, this.formatFileSize(file.size));
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) {
                this.fileUploaded = false;
                return;
            }
            
            if (file.type !== 'application/pdf') {
                alert('Пожалуйста, выберите PDF файл');
                event.target.value = '';
                return;
            }

            // Показываем информацию о новом файле
            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
            
            console.log('New file selected:', file.name, this.formatFileSize(file.size));
        },

        removeFile() {
            console.log('Removing file...');
            
            // Очищаем Livewire
            if (typeof @this !== 'undefined' && @this.set) {
                @this.set('gallup_pdf', null);
            }
            
            this.resetFileState();
        },

        resetFileState() {
            console.log('Resetting file state...');
            
            // Очищаем input
            const fileInput = this.$el.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }
            
            // Сбрасываем состояние
            this.fileUploaded = false;
            this.isExistingFile = false;
            this.fileName = '';
            this.fileSize = '';
            this.downloadUrl = '';
            this.isDragOver = false;
            
            console.log('File state reset');
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
}
</script>
@endpush 