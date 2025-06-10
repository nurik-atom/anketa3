<?php
// resources/views/livewire/candidate-form.blade.php
?>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <!-- Step Navigation -->
        <div class="flex items-center justify-between mb-6 px-6 pt-6">
            <div class="flex items-center space-x-2 w-full">
                <!-- Step 1 -->
                <div class="flex items-center flex-1">
                    <div class="flex items-center {{ $currentStep >= 1 ? 'text-blue-600' : 'text-gray-500' }}">
                        <div class="flex-shrink-0">
                            @if($currentStep > 1)
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-6 h-6 border-2 {{ $currentStep === 1 ? 'border-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-sm {{ $currentStep === 1 ? 'text-blue-600' : 'text-gray-500' }}">1</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <button type="button" wire:click="$set('currentStep', 1)" class="text-sm font-medium {{ $currentStep >= 1 ? 'text-blue-600' : 'text-gray-500' }}">
                                Основная информация
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 px-6">
                        <div class="h-0.5 {{ $currentStep > 1 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-center flex-1">
                    <div class="flex items-center {{ $currentStep >= 2 ? 'text-blue-600' : 'text-gray-500' }}">
                        <div class="flex-shrink-0">
                            @if($currentStep > 2)
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-6 h-6 border-2 {{ $currentStep === 2 ? 'border-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-sm {{ $currentStep === 2 ? 'text-blue-600' : 'text-gray-500' }}">2</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <button type="button" wire:click="$set('currentStep', 2)" class="text-sm font-medium {{ $currentStep >= 2 ? 'text-blue-600' : 'text-gray-500' }}">
                                Дополнительная информация
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 px-6">
                        <div class="h-0.5 {{ $currentStep > 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-center flex-1">
                    <div class="flex items-center {{ $currentStep >= 3 ? 'text-blue-600' : 'text-gray-500' }}">
                        <div class="flex-shrink-0">
                            @if($currentStep > 3)
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-6 h-6 border-2 {{ $currentStep === 3 ? 'border-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-sm {{ $currentStep === 3 ? 'text-blue-600' : 'text-gray-500' }}">3</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <button type="button" wire:click="$set('currentStep', 3)" class="text-sm font-medium {{ $currentStep >= 3 ? 'text-blue-600' : 'text-gray-500' }}">
                                Образование и работа
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 px-6">
                        <div class="h-0.5 {{ $currentStep > 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex items-center flex-1">
                    <div class="flex items-center {{ $currentStep >= 4 ? 'text-blue-600' : 'text-gray-500' }}">
                        <div class="flex-shrink-0">
                            @if($currentStep > 4)
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-6 h-6 border-2 {{ $currentStep === 4 ? 'border-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-sm {{ $currentStep === 4 ? 'text-blue-600' : 'text-gray-500' }}">4</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <button type="button" wire:click="$set('currentStep', 4)" class="text-sm font-medium {{ $currentStep >= 4 ? 'text-blue-600' : 'text-gray-500' }}">
                                Тесты
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remove old progress bar -->

        <form wire:submit.prevent="submit" class="p-6 space-y-6">
            <!-- Step Content -->
            @include('livewire.candidate-form.step1')
            @include('livewire.candidate-form.step2')
            @include('livewire.candidate-form.step3')
            @include('livewire.candidate-form.step4')

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8">
                @if ($currentStep > 1)
                    <button type="button"
                            wire:click="previousStep"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        Назад
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button"
                            wire:click="nextStep"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        Сохранить и далее
                    </button>
                @else
                    <div class="flex space-x-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                            Сохранить и завершить
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div> 