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

        <!-- Новая структура семьи -->
        <div class="space-y-6">
            <!-- Родители -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Родители</label>
                <div class="space-y-4">
                    @foreach($parents as $index => $parent)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Родство <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="parents.{{ $index }}.relation" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Выберите</option>
                                        <option value="Отец">Отец</option>
                                        <option value="Мать">Мать</option>
                                    </select>
                                    @error("parents.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="parents.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("parents.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Профессия <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="parents.{{ $index }}.profession" 
                                           placeholder="Профессия"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("parents.{$index}.profession") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" wire:click="removeParent({{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if(count($parents) < 2)
                        <button type="button" 
                                wire:click="addParent" 
                                class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Добавить родителя
                        </button>
                    @endif
                </div>
            </div>

            <!-- Братья и сестры -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Братья и сестры</label>
                <div class="space-y-4">
                    @foreach($siblings as $index => $sibling)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Родство <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="siblings.{{ $index }}.relation" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Выберите</option>
                                        <option value="Брат">Брат</option>
                                        <option value="Сестра">Сестра</option>
                                    </select>
                                    @error("siblings.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="siblings.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("siblings.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" wire:click="removeSibling({{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" 
                            wire:click="addSibling" 
                            class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Добавить брата/сестру
                    </button>
                </div>
            </div>

            <!-- Дети -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Дети</label>
                <div class="space-y-4">
                    @foreach($children as $index => $child)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Имя ребенка <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="children.{{ $index }}.name" 
                                           placeholder="Имя ребенка"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("children.{$index}.name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="children.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("children.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" wire:click="removeChild({{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" 
                            wire:click="addChild" 
                            class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Добавить ребенка
                    </button>
                </div>
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
                    <!-- Выбранные страны -->
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
                    
                    <!-- Поиск стран -->
                    <div class="relative" x-data="countrySearch()">
                        <input 
                            type="text" 
                            x-model="search"
                            @click="showDropdown = true"
                            @keydown.escape="showDropdown = false"
                            @keydown.arrow-down.prevent="highlightNext()"
                            @keydown.arrow-up.prevent="highlightPrev()"
                            @keydown.enter.prevent="selectHighlighted()"
                            placeholder="Поиск страны..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        
                        <!-- Выпадающий список -->
                        <div x-show="showDropdown && filteredCountries.length > 0" 
                             x-transition
                             @click.away="showDropdown = false"
                             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                            <template x-for="(country, index) in filteredCountries" :key="country.name_ru">
                                <div @click="selectCountry(country)" 
                                     :class="{'bg-blue-100': index === highlightedIndex}"
                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50">
                                    <div class="flex items-center">
                                        <img :src="country.flag_url" 
                                             :alt="country.name_ru + ' flag'"
                                             class="inline w-4 h-4 mr-2 align-middle"
                                             x-show="country.flag_url">
                                        <span x-text="country.name_ru" class="font-normal block truncate"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
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

<script>
function countrySearch() {
    return {
        search: '',
        showDropdown: false,
        highlightedIndex: -1,
        countries: @json($countries ?? []),
        
        get filteredCountries() {
            if (!this.search.trim()) {
                return this.countries.slice(0, 10); // Показываем первые 10 стран по умолчанию
            }
            
            const searchTerm = this.search.toLowerCase();
            return this.countries.filter(country => 
                country.name_ru.toLowerCase().includes(searchTerm)
            ).slice(0, 20); // Ограничиваем до 20 результатов
        },
        
        selectCountry(country) {
            // Добавляем страну через Livewire
            @this.call('addCountryByName', country.name_ru);
            
            // Очищаем поиск и закрываем dropdown
            this.search = '';
            this.showDropdown = false;
            this.highlightedIndex = -1;
        },
        
        highlightNext() {
            if (this.highlightedIndex < this.filteredCountries.length - 1) {
                this.highlightedIndex++;
            }
        },
        
        highlightPrev() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },
        
        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.filteredCountries[this.highlightedIndex]) {
                this.selectCountry(this.filteredCountries[this.highlightedIndex]);
            }
        },
        
        init() {
            // Сбрасываем индекс при изменении поиска
            this.$watch('search', () => {
                this.highlightedIndex = -1;
                this.showDropdown = this.search.length > 0 || this.search === '';
            });
        }
    }
}
</script>

<!-- Удаляем дублирующиеся скрипты Tom Select --> 

 