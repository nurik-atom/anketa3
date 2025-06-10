<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @livewireStyles
        @filamentStyles
        
        <!-- Croppie CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">

        <!-- Cropper CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        <!-- Scripts -->
        @livewireScripts
        
        <!-- Cropper CSS и JS должны быть загружены перед пользовательскими скриптами -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        
        <!-- Croppie JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>

        @stack('scripts')

        <!-- Toast Notification System -->
        <div x-data="{ 
                get visible() { 
                    return Alpine.store && Alpine.store('toast') ? Alpine.store('toast').visible : false; 
                },
                get message() { 
                    return Alpine.store && Alpine.store('toast') ? Alpine.store('toast').message : ''; 
                },
                get type() { 
                    return Alpine.store && Alpine.store('toast') ? Alpine.store('toast').type : 'success'; 
                }
             }"
             x-show="visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-4 right-4 px-4 py-2 rounded-lg text-white"
             :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
             style="z-index: 50;">
            <span x-text="message"></span>
        </div>

        <script>
            // Глобальная переменная для отслеживания инициализации
            window.alpineStoresInitialized = false;
            
            // Функция инициализации Alpine stores
            function initializeAlpineStores() {
                if (window.alpineStoresInitialized || typeof Alpine === 'undefined') return;
                
                try {
                    Alpine.store('toast', {
                        visible: false,
                        message: '',
                        type: 'success',
                        timeoutId: null,

                        show(type, message) {
                            this.visible = true;
                            this.message = message;
                            this.type = type;

                            if (this.timeoutId) {
                                clearTimeout(this.timeoutId);
                            }

                            this.timeoutId = setTimeout(() => {
                                this.visible = false;
                            }, 3000);
                        }
                    });

                    Alpine.data('toast', () => ({
                        get visible() {
                            return Alpine.store('toast').visible;
                        },
                        get message() {
                            return Alpine.store('toast').message;
                        },
                        get type() {
                            return Alpine.store('toast').type;
                        }
                    }));
                    
                    window.alpineStoresInitialized = true;
                    console.log('Alpine stores initialized');
                } catch (error) {
                    console.error('Error initializing Alpine stores:', error);
                }
            }
            
            // Разные способы инициализации в зависимости от загрузки Alpine
            document.addEventListener('DOMContentLoaded', function() {
                // Проверяем каждые 100ms если Alpine загружен
                const checkAlpine = setInterval(() => {
                    if (typeof Alpine !== 'undefined') {
                        clearInterval(checkAlpine);
                        initializeAlpineStores();
                    }
                }, 100);
                
                // Максимум ждем 5 секунд
                setTimeout(() => {
                    clearInterval(checkAlpine);
                    if (!window.alpineStoresInitialized) {
                        console.warn('Alpine.js not found after 5 seconds');
                    }
                }, 5000);
            });
            
            // Также слушаем стандартное событие Alpine
            document.addEventListener('alpine:init', initializeAlpineStores);

            // Безопасная функция для показа toast
            function showToast(type, message) {
                if (typeof Alpine !== 'undefined' && Alpine.store && Alpine.store('toast')) {
                    Alpine.store('toast').show(type, message);
                } else {
                    console.log(`Toast: [${type}] ${message}`);
                }
            }

            // Слушаем события от Livewire
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.on('photo-updated', event => {
                        showToast('success', event.message || 'Фото обновлено');
                    });
                    
                    Livewire.on('photo-error', event => {
                        showToast('error', event.message || 'Ошибка загрузки фото');
                    });
                    
                    Livewire.on('photo-removed', event => {
                        showToast('success', event.message || 'Фото удалено');
                    });
                    
                    Livewire.on('photoUploaded', event => {
                        showToast('success', 'Фото успешно загружено');
                    });
                    
                    Livewire.on('photoRemoved', event => {
                        showToast('success', 'Фото удалено');
                    });
                }
            });
        </script>
    </body>
</html>
