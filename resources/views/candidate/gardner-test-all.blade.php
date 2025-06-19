<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Тест Гарднера - Все вопросы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Все 56 вопросов на одной странице</h3>
                            <p class="text-sm text-gray-600">Отвечайте на вопросы в любом порядке</p>
                        </div>
                        <a href="{{ route('gardner-test') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                            ← К постраничному режиму
                        </a>
                    </div>
                    
                    @livewire('gardner-test-all-questions')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 