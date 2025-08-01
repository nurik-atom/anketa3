@if($currentStep === 2)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Дополнительная информация</h2>

    <div class="space-y-6">
        
        <!-- Первые три поля в одной строке -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Водительские права -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Водительские права <span class="text-red-500">*</span>
                </label>
                <select wire:model="has_driving_license" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите ответ</option>
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>
                @error('has_driving_license') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <!-- Религия -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Религия <span class="text-red-500">*</span>
                </label>
                <select wire:model="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите религию</option>
                    @foreach($religions as $key => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Практикующий</label>
                <select wire:model="is_practicing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите ответ</option>
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>
                @error('is_practicing') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Семья -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Члены семьи</label>
            <div class="space-y-4">
                @foreach($family_members as $index => $member)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Тип родства <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="family_members.{{ $index }}.type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Выберите тип родства</option>
                                    <option value="Отец">Отец</option>
                                    <option value="Мать">Мать</option>
                                    <option value="Брат">Брат</option>
                                    <option value="Сестра">Сестра</option>
                                    <option value="Жена">Жена</option>
                                    <option value="Муж">Муж</option>
                                    <option value="Сын">Сын</option>
                                    <option value="Дочь">Дочь</option>
                                </select>
                                @error("family_members.{$index}.type") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Год рождения <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="family_members.{{ $index }}.birth_year" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Год рождения</option>
                                    @foreach($familyYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error("family_members.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Профессия <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="family_members.{{ $index }}.profession" 
                                       placeholder="Профессия"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("family_members.{$index}.profession") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeFamilyMember({{ $index }})" class="text-red-600 hover:text-red-800">
                                Удалить
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button" 
                        wire:click="addFamilyMember" 
                        class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Добавить члена семьи
                </button>
            </div>
        </div>

        <!-- Хобби и интересы -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Хобби</label>
                <textarea wire:model="hobbies" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('hobbies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Интересы</label>
                <textarea wire:model="interests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('interests') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Посещенные страны и Любимые виды спорта в одном ряду -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Посещенные страны -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Посещенные страны</label>
                <div class="mt-1">
                    <div class="flex flex-wrap gap-2 mb-2 min-h-[2.5rem] bg-gray-50 p-2 rounded-md">
                        @foreach($visited_countries as $index => $country)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                @php
                                    $countryData = collect($countries)->firstWhere('name_ru', $country);
                                @endphp
                                @if($countryData && isset($countryData['flag_url']))
                                    <img src="{{ $countryData['flag_url'] }}" 
                                         alt="flag" 
                                         class="inline w-4 h-4 mr-1 align-middle">
                                @endif
                                {{ $country }}
                                <button type="button" wire:click="removeCountry({{ $index }})" class="ml-1 text-blue-400 hover:text-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <select wire:model="newCountry" wire:change="addCountry" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Выберите страну</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['name_ru'] }}">{{ $country['name_ru'] }}</option>
                        @endforeach
                    </select>
                </div>
                @error('visited_countries') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Спорт -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Любимые виды спорта</label>
                <textarea wire:model="favorite_sports" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('favorite_sports') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Время и чтение в одном ряду -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <!-- Количество книг читаемых в год -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество книг читаемых в год</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-md">{{ $books_per_year ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="books_per_year"
                           name="books_per_year"
                           value="{{ $books_per_year ?? 0 }}"
                           min="1" 
                           max="100" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-blue-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-blue-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('books_per_year') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Развлекательные видео -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых при просмотре развлекательных видео (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-md">{{ $entertainment_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="entertainment_hours_weekly"
                           name="entertainment_hours_weekly"
                           value="{{ $entertainment_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-indigo-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-indigo-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('entertainment_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Образовательные видео -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых при просмотре образовательных видео (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-md">{{ $educational_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="educational_hours_weekly"
                           name="educational_hours_weekly"
                           value="{{ $educational_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-emerald-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-emerald-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('educational_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Социальные сети -->
            <div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 min-h-[3rem] flex items-center">Количество часов затрачиваемых на соц. сети (в неделю)</label>
                    <div class="flex justify-end">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-md">{{ $social_media_hours_weekly ?? 0 }}</span>
                    </div>
                </div>
                <div class="relative mt-2">
                    <input type="range" 
                           wire:model="social_media_hours_weekly"
                           name="social_media_hours_weekly"
                           value="{{ $social_media_hours_weekly ?? 0 }}"
                           min="0" 
                           max="168" 
                           step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer
                                  [&::-webkit-slider-thumb]:w-4
                                  [&::-webkit-slider-thumb]:h-4
                                  [&::-webkit-slider-thumb]:appearance-none
                                  [&::-webkit-slider-thumb]:bg-purple-600
                                  [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:cursor-pointer
                                  [&::-moz-range-thumb]:w-4
                                  [&::-moz-range-thumb]:h-4
                                  [&::-moz-range-thumb]:appearance-none
                                  [&::-moz-range-thumb]:bg-purple-600
                                  [&::-moz-range-thumb]:rounded-full
                                  [&::-moz-range-thumb]:cursor-pointer">
                </div>
                @error('social_media_hours_weekly') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
@endif

<!-- Удаляем дублирующиеся скрипты Tom Select --> 

 