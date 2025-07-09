<?php

namespace App\Filament\Resources;

use App\Models\Candidate;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetValue;
use App\Models\GallupReportSheetIndex;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CandidateSearchResource\Pages;

class CandidateSearchResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Поиск кандидатов';
    protected static ?string $modelLabel = 'Поиск кандидатов';
    protected static ?string $pluralModelLabel = 'Поиск кандидатов';
    protected static ?string $slug = 'candidate-search';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Поиск кандидатов')
                    ->description('Используйте кнопку "Найти кандидатов" для настройки условий поиска')
                    ->schema([
                        Forms\Components\Placeholder::make('search_info')
                            ->label('')
                            ->content('Для поиска кандидатов нажмите кнопку "Найти кандидатов" в верхней части страницы. Вы сможете настроить несколько условий поиска с разными операторами (больше, меньше, равно) для любых характеристик из всех типов отчетов.')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description(function () {
                // Получаем условия поиска из сессии
                $search = session('candidate_search', []);
                
                if (empty($search['conditions']) && empty($search['min_age']) && empty($search['max_age']) && empty($search['desired_position']) && empty($search['cities'])) {
                    return 'Условия поиска не заданы. Используйте кнопку "Найти кандидатов" для настройки поиска.';
                }
                
                $searchBadges = [];
                
                // Добавляем условия по характеристикам
                if (!empty($search['conditions'])) {
                    foreach ($search['conditions'] as $condition) {
                        if (!isset($condition['characteristic'], $condition['operator'], $condition['value'])) {
                            continue;
                        }
                        
                        $parts = explode('|', $condition['characteristic']);
                        if (count($parts) >= 3) {
                            $reportType = $parts[0];
                            $name = $parts[2];
                            $operatorText = $condition['operator'] === '>=' ? '>' : '<';
                            $conditionText = "{$reportType}: {$name} {$operatorText} {$condition['value']}%";
                            $searchBadges[] = '<span class="inline-flex items-center gap-x-1.5 rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">🎯 ' . $conditionText . '</span>';
                        }
                    }
                }
                
                // Добавляем возрастные фильтры
                if (!empty($search['min_age']) || !empty($search['max_age'])) {
                    $ageText = '';
                    if (!empty($search['min_age']) && !empty($search['max_age'])) {
                        $ageText = "от {$search['min_age']} до {$search['max_age']} лет";
                    } elseif (!empty($search['min_age'])) {
                        $ageText = "от {$search['min_age']} лет";
                    } else {
                        $ageText = "до {$search['max_age']} лет";
                    }
                    $searchBadges[] = '<span class="inline-flex items-center gap-x-1.5 rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">👤 ' . $ageText . '</span>';
                }
                
                // Добавляем фильтр по должности
                if (!empty($search['desired_position'])) {
                    $searchBadges[] = '<span class="inline-flex items-center gap-x-1.5 rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">💼 "' . $search['desired_position'] . '"</span>';
                }
                
                // Добавляем фильтр по городам
                if (!empty($search['cities'])) {
                    $citiesCount = count($search['cities']);
                    if ($citiesCount == 1) {
                        $cityText = $search['cities'][0];
                    } else {
                        $cityText = implode(', ', $search['cities']) . " ({$citiesCount} городов)";
                    }
                    $searchBadges[] = '<span class="inline-flex items-center gap-x-1.5 rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">🏙️ ' . $cityText . '</span>';
                }
                
                return new \Illuminate\Support\HtmlString(
                    '<div class="flex flex-wrap gap-2"><span class="text-sm font-medium text-gray-700">Активные условия поиска:</span> ' . 
                    implode(' ', $searchBadges) . 
                    '</div>'
                );
            })
            ->searchable(false)
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон'),

                Tables\Columns\TextColumn::make('age')
                    ->label('Возраст')
                    ->getStateUsing(function (Candidate $record) {
                        if (!$record->birth_date) {
                            return 'Не указан';
                        }
                        
                        $age = $record->birth_date->age;
                        return $age . ' лет';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) {$direction}");
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('current_city')
                    ->label('Город')
                    ->getStateUsing(function (Candidate $record) {
                        return $record->current_city ?: 'Не указан';
                    })
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('desired_position')
                    ->label('Желаемая позиция')
                    ->limit(30),

                Tables\Columns\TextColumn::make('matching_characteristics')
                    ->label('Подходящие условия')
                    ->getStateUsing(function (Candidate $record) {
                        // Получаем условия поиска из сессии
                        $search = session('candidate_search', []);
                        if (empty($search['conditions'])) {
                            return 'Не указаны условия поиска';
                        }

                        $matches = [];
                        
                        // Проверяем каждое условие
                        foreach ($search['conditions'] as $condition) {
                            if (!isset($condition['characteristic'], $condition['operator'], $condition['value'])) {
                                continue;
                            }
                            
                            $parts = explode('|', $condition['characteristic']);
                            if (count($parts) < 3) continue;
                            
                            [$reportType, $type, $name] = $parts;
                            
                            // Находим соответствующий отчет
                            $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                            if (!$sheet) continue;
                            
                            // Получаем значение характеристики для кандидата
                            $valueRecord = GallupReportSheetValue::where('gallup_report_sheet_id', $sheet->id)
                                ->where('candidate_id', $record->id)
                                ->where('type', trim($type))
                                ->where('name', trim($name))
                                ->first();
                            
                            if (!$valueRecord) continue;
                            
                            $candidateValue = $valueRecord->value;
                            $conditionValue = $condition['value'];
                            $operator = $condition['operator'];
                            
                            // Проверяем соответствие условию
                            $conditionMet = false;
                            switch ($operator) {
                                case '>=':
                                    $conditionMet = $candidateValue >= $conditionValue;
                                    break;
                                case '<=':
                                    $conditionMet = $candidateValue <= $conditionValue;
                                    break;
                            }
                            
                            if ($conditionMet) {
                                $matches[] = "{$reportType}: {$name} = {$candidateValue}% ({$operator} {$conditionValue}%)";
                            }
                        }

                        return empty($matches) ? 'Нет соответствий' : implode(', ', $matches);
                    })
                    ->wrap()
                    ->color(fn (string $state): string => $state === 'Нет соответствий' ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Просмотр')
                    ->url(fn (Candidate $record): string => 
                        route('candidate.report', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Получаем условия поиска из сессии
                $search = session('candidate_search', []);
                
                if (empty($search['conditions']) && empty($search['min_age']) && empty($search['max_age']) && empty($search['desired_position']) && empty($search['cities'])) {
                    // Если условия поиска не заданы, показываем пустую таблицу
                    return $query->whereRaw('1 = 0');
                }

                $candidateIds = collect();
                
                // Обрабатываем условия по характеристикам (если есть)
                if (!empty($search['conditions'])) {
                    foreach ($search['conditions'] as $condition) {
                        if (!isset($condition['characteristic'], $condition['operator'], $condition['value'])) {
                            continue;
                        }
                        
                        $parts = explode('|', $condition['characteristic']);
                        if (count($parts) < 3) continue;
                        
                        [$reportType, $type, $name] = $parts;
                        
                        // Находим соответствующий отчет
                        $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                        if (!$sheet) continue;
                        
                        $conditionValue = $condition['value'];
                        $operator = $condition['operator'];
                        
                        // Формируем запрос в зависимости от оператора
                        $valueQuery = GallupReportSheetValue::where('gallup_report_sheet_id', $sheet->id)
                            ->where('type', trim($type))
                            ->where('name', trim($name));
                        
                        switch ($operator) {
                            case '>=':
                                $valueQuery->where('value', '>=', $conditionValue);
                                break;
                            case '<=':
                                $valueQuery->where('value', '<=', $conditionValue);
                                break;
                            default:
                                continue 2; // Пропускаем неизвестные операторы
                        }
                        
                        $ids = $valueQuery->pluck('candidate_id');
                        $candidateIds = $candidateIds->merge($ids);
                    }
                }
                
                // Применяем возрастные фильтры
                $ageCandidateIds = collect();
                if (!empty($search['min_age']) || !empty($search['max_age'])) {
                    $ageQuery = $query->newQuery();
                    
                    if (!empty($search['min_age'])) {
                        // Кандидаты старше минимального возраста
                        $maxBirthDate = now()->subYears($search['min_age'])->format('Y-m-d');
                        $ageQuery->where('birth_date', '<=', $maxBirthDate);
                    }
                    
                    if (!empty($search['max_age'])) {
                        // Кандидаты младше максимального возраста
                        $minBirthDate = now()->subYears($search['max_age'] + 1)->addDay()->format('Y-m-d');
                        $ageQuery->where('birth_date', '>=', $minBirthDate);
                    }
                    
                    // Добавляем условие что birth_date не null
                    $ageQuery->whereNotNull('birth_date');
                    
                    $ageCandidateIds = $ageQuery->pluck('id');
                }
                
                // Применяем фильтр по должности
                $positionCandidateIds = collect();
                if (!empty($search['desired_position'])) {
                    $positionQuery = $query->newQuery();
                    $positionQuery->where('desired_position', 'LIKE', '%' . $search['desired_position'] . '%')
                                  ->whereNotNull('desired_position');
                    
                    $positionCandidateIds = $positionQuery->pluck('id');
                }
                
                // Применяем фильтр по городам
                $cityCandidateIds = collect();
                if (!empty($search['cities']) && is_array($search['cities'])) {
                    $cityQuery = $query->newQuery();
                    $cityQuery->whereIn('current_city', $search['cities'])
                              ->whereNotNull('current_city');
                    
                    $cityCandidateIds = $cityQuery->pluck('id');
                }
                
                // Комбинируем результаты
                $finalCandidateIds = collect();
                
                // Список всех типов фильтров
                $hasCharacteristicFilter = !empty($search['conditions']) && !$candidateIds->isEmpty();
                $hasAgeFilter = (!empty($search['min_age']) || !empty($search['max_age'])) && !$ageCandidateIds->isEmpty();
                $hasPositionFilter = !empty($search['desired_position']) && !$positionCandidateIds->isEmpty();
                $hasCityFilter = !empty($search['cities']) && !$cityCandidateIds->isEmpty();
                
                // Считаем количество активных фильтров
                $activeFiltersCount = ($hasCharacteristicFilter ? 1 : 0) + ($hasAgeFilter ? 1 : 0) + ($hasPositionFilter ? 1 : 0) + ($hasCityFilter ? 1 : 0);
                
                // Если есть несколько типов фильтров - берем пересечение
                if ($activeFiltersCount > 1) {
                    $finalCandidateIds = collect();
                    
                    if ($hasCharacteristicFilter) {
                        $finalCandidateIds = $candidateIds->unique();
                    }
                    
                    if ($hasAgeFilter) {
                        if ($finalCandidateIds->isEmpty()) {
                            $finalCandidateIds = $ageCandidateIds;
                        } else {
                            $finalCandidateIds = $finalCandidateIds->intersect($ageCandidateIds);
                        }
                    }
                    
                    if ($hasPositionFilter) {
                        if ($finalCandidateIds->isEmpty()) {
                            $finalCandidateIds = $positionCandidateIds;
                        } else {
                            $finalCandidateIds = $finalCandidateIds->intersect($positionCandidateIds);
                        }
                    }
                    
                    if ($hasCityFilter) {
                        if ($finalCandidateIds->isEmpty()) {
                            $finalCandidateIds = $cityCandidateIds;
                        } else {
                            $finalCandidateIds = $finalCandidateIds->intersect($cityCandidateIds);
                        }
                    }
                } else {
                    // Если только один тип фильтра - используем его результаты
                    if ($hasCharacteristicFilter) {
                        $finalCandidateIds = $candidateIds->unique();
                    } elseif ($hasAgeFilter) {
                        $finalCandidateIds = $ageCandidateIds;
                    } elseif ($hasPositionFilter) {
                        $finalCandidateIds = $positionCandidateIds;
                    } elseif ($hasCityFilter) {
                        $finalCandidateIds = $cityCandidateIds;
                    }
                }
                
                if ($finalCandidateIds->isEmpty()) {
                    return $query->whereRaw('1 = 0');
                }
                
                return $query->whereIn('id', $finalCandidateIds);
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\SearchCandidates::route('/'),
        ];
    }
} 