<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Доступ запрещен - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-9xl font-bold text-red-500">403</h1>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Доступ запрещен
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $exception->getMessage() ?: 'У вас нет прав для доступа к этой странице.' }}
                </p>
                
                <div class="mt-8 space-y-4">
                    <div class="text-left bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Ограничения доступа
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Вы можете редактировать только свои анкеты</li>
                                        <!-- <li>Администраторы могут редактировать любые анкеты</li> -->
                                        <!-- <li>Для получения прав администратора обратитесь к системному администратору</li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    @auth
                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Вернуться на главную
                    </a>
                    @else
                    <a href="{{ route('login') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Войти в систему
                    </a>
                    @endauth
                    
                    @if(url()->previous() !== url()->current())
                    <button onclick="history.back()" 
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Назад
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
