<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Тест Гарднера - Множественные интеллекты') }}
        </h2>
    </x-slot>

    @livewire('gardner-test-all-questions')
</x-app-layout> 