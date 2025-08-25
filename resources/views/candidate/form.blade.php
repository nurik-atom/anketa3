<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Анкета кандидата') }}
                @if($candidateId)
                    <span class="text-sm font-normal text-gray-600 ml-2">(Редактирование)</span>
                @endif
            </h2>
            
            @if(auth()->user()->is_admin)
                <a href="/admin/candidates" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Назад в админку
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <livewire:candidate-form :candidate-id="$candidateId" />
    </div>
</x-app-layout> 