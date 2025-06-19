<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Тест Гарднера - Множественные интеллекты') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Все {{ config('app.gardner_questions_count', 44) }} вопросов на одной странице</h3>
                            <p class="text-sm text-gray-600">Отвечайте на вопросы в любом порядке</p>
                        </div>
                    </div>
                    
                    @livewire('gardner-test-all-questions')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 