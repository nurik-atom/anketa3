<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Тест Гарднера') }}
        </h2>
    </x-slot>

    @livewire('gardner-test')
</x-app-layout> 