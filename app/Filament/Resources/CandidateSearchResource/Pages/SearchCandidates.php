<?php

namespace App\Filament\Resources\CandidateSearchResource\Pages;

use App\Filament\Resources\CandidateSearchResource;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetValue;
use App\Models\GallupReportSheetIndex;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class SearchCandidates extends ListRecords
{
    protected static string $resource = CandidateSearchResource::class;

    protected function getSearchFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Условия поиска')
                ->schema([
                    Forms\Components\Repeater::make('conditions')
                        ->label('Условия')
                        ->schema([
                            Forms\Components\Select::make('characteristic')
                                ->label('Характеристика')
                                ->options(function () {
                                    // Получаем все характеристики из всех типов отчетов
                                    $allSheets = GallupReportSheet::all();
                                    $options = [];
                                    
                                    foreach ($allSheets as $sheet) {
                                        $reportType = $sheet->name_report;
                                        
                                        $characteristics = GallupReportSheetIndex::where('gallup_report_sheet_id', $sheet->id)
                                            ->get(['type', 'name'])
                                            ->groupBy('type');
                                        
                                        foreach ($characteristics as $type => $items) {
                                            $typeName = trim($type);
                                            
                                            foreach ($items as $item) {
                                                $key = $reportType . '|' . trim($type) . '|' . trim($item->name);
                                                $options[$key] = $reportType . ' - ' . $typeName . ': ' . trim($item->name);
                                            }
                                        }
                                    }
                                    
                                    return $options;
                                })
                                ->searchable()
                                ->required()
                                ->columnSpan(2),
                                
                            Forms\Components\Select::make('operator')
                                ->label('Операция')
                                ->options([
                                    '>=' => 'Больше или равно (≥)',
                                    '<=' => 'Меньше или равно (≤)',
                                ])
                                ->required()
                                ->default('>=')
                                ->columnSpan(1),
                                
                            Forms\Components\TextInput::make('value')
                                ->label('Значение')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->addActionLabel('Добавить условие')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(function (array $state): ?string {
                            if (!isset($state['characteristic'], $state['operator'], $state['value'])) {
                                return 'Новое условие';
                            }
                            
                            // Парсим название характеристики для краткого отображения
                            $parts = explode('|', $state['characteristic']);
                            if (count($parts) >= 3) {
                                $reportType = $parts[0];
                                $name = $parts[2];
                                return "{$reportType}: {$name} {$state['operator']} {$state['value']}%";
                            }
                            
                            return "Условие: {$state['operator']} {$state['value']}%";
                        }),
                ])
                ->columns(1),
                
            Forms\Components\Section::make('Фильтр по возрасту')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('min_age')
                                ->label('Минимальный возраст')
                                ->numeric()
                                ->minValue(16)
                                ->maxValue(100)
                                ->suffix('лет')
                                ->placeholder('Например: 25'),
                                
                            Forms\Components\TextInput::make('max_age')
                                ->label('Максимальный возраст')
                                ->numeric()
                                ->minValue(16)
                                ->maxValue(100)
                                ->suffix('лет')
                                ->placeholder('Например: 35'),
                        ])
                ])
                ->collapsible()
                ->collapsed(),
                
            Forms\Components\Section::make('Поиск по должности')
                ->schema([
                    Forms\Components\TextInput::make('desired_position')
                        ->label('Желаемая должность')
                        ->placeholder('Введите часть названия должности (например: "менеджер", "разработчик")')
                        ->helperText('Поиск будет осуществляться по частичному совпадению текста')
                        ->maxLength(255),
                ])
                ->collapsible()
                ->collapsed(),
                
            Forms\Components\Section::make('Поиск по городу')
                ->schema([
                    Forms\Components\Select::make('cities')
                        ->label('Город проживания')
                        ->multiple()
                        ->options(function () {
                            // Получаем уникальные города из таблицы кандидатов
                            return \App\Models\Candidate::whereNotNull('current_city')
                                ->where('current_city', '!=', '')
                                ->distinct()
                                ->orderBy('current_city')
                                ->pluck('current_city', 'current_city')
                                ->toArray();
                        })
                        ->searchable()
                        ->placeholder('Выберите один или несколько городов')
                        ->helperText('Можно выбрать несколько городов. Будут найдены кандидаты из любого из выбранных городов.')
                        ->maxItems(10),
                ])
                ->collapsible()
                ->collapsed(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('search')
                ->label('Найти кандидатов')
                ->color('primary')
                ->icon('heroicon-o-magnifying-glass')
                ->form($this->getSearchFormSchema())
                ->fillForm(function () {
                    // Предзаполняем форму сохраненными условиями
                    $search = session('candidate_search', []);
                    $formData = [];
                    
                    if (!empty($search['conditions'])) {
                        $formData['conditions'] = $search['conditions'];
                    } else {
                        $formData['conditions'] = [];
                    }
                    
                    if (isset($search['min_age'])) {
                        $formData['min_age'] = $search['min_age'];
                    }
                    
                    if (isset($search['max_age'])) {
                        $formData['max_age'] = $search['max_age'];
                    }
                    
                    if (isset($search['desired_position'])) {
                        $formData['desired_position'] = $search['desired_position'];
                    }
                    
                    if (isset($search['cities'])) {
                        $formData['cities'] = $search['cities'];
                    }
                    
                    return $formData;
                })
                ->action(function (array $data) {
                    // Проверяем, что есть хотя бы одно условие поиска
                    $hasConditions = !empty($data['conditions']);
                    $hasAgeFilter = !empty($data['min_age']) || !empty($data['max_age']);
                    $hasPositionFilter = !empty($data['desired_position']);
                    $hasCityFilter = !empty($data['cities']);
                    
                    if (!$hasConditions && !$hasAgeFilter && !$hasPositionFilter && !$hasCityFilter) {
                        Notification::make()
                            ->title('Ошибка')
                            ->body('Добавьте хотя бы одно условие поиска: характеристики, возраст, должность или город')
                            ->danger()
                            ->send();
                        return;
                    }
                    
                    // Валидируем условия (если есть)
                    $validConditions = [];
                    if (!empty($data['conditions'])) {
                        foreach ($data['conditions'] as $condition) {
                            if (isset($condition['characteristic']) && 
                                isset($condition['operator']) && 
                                isset($condition['value']) &&
                                !empty($condition['characteristic']) &&
                                !empty($condition['operator']) &&
                                is_numeric($condition['value'])) {
                                $validConditions[] = $condition;
                            }
                        }
                    }
                    
                    // Подготавливаем данные для сохранения
                    $searchData = ['conditions' => $validConditions];
                    
                    // Добавляем возрастные фильтры если указаны
                    if (!empty($data['min_age']) && is_numeric($data['min_age'])) {
                        $searchData['min_age'] = (int)$data['min_age'];
                    }
                    
                    if (!empty($data['max_age']) && is_numeric($data['max_age'])) {
                        $searchData['max_age'] = (int)$data['max_age'];
                    }
                    
                    // Добавляем фильтр по должности если указан
                    if (!empty($data['desired_position'])) {
                        $searchData['desired_position'] = trim($data['desired_position']);
                    }
                    
                    // Добавляем фильтр по городам если указан
                    if (!empty($data['cities']) && is_array($data['cities'])) {
                        $searchData['cities'] = array_filter($data['cities']);
                    }
                    
                    // Валидация возрастного диапазона
                    if (isset($searchData['min_age']) && isset($searchData['max_age'])) {
                        if ($searchData['min_age'] > $searchData['max_age']) {
                            Notification::make()
                                ->title('Ошибка')
                                ->body('Минимальный возраст не может быть больше максимального')
                                ->danger()
                                ->send();
                            return;
                        }
                    }
                    
                    // Сохраняем условия поиска в сессии
                    session(['candidate_search' => $searchData]);
                    
                    // Формируем описание для уведомления
                    $searchDescription = [];
                    
                    if (!empty($validConditions)) {
                        $conditionsText = [];
                        foreach ($validConditions as $condition) {
                            $parts = explode('|', $condition['characteristic']);
                            if (count($parts) >= 3) {
                                $reportType = $parts[0];
                                $name = $parts[2];
                                $conditionsText[] = "{$reportType}: {$name} {$condition['operator']} {$condition['value']}%";
                            }
                        }
                        if (!empty($conditionsText)) {
                            $searchDescription[] = 'Условия: ' . implode(', ', $conditionsText);
                        }
                    }
                    
                    if (!empty($searchData['min_age']) || !empty($searchData['max_age'])) {
                        $ageText = 'Возраст: ';
                        if (!empty($searchData['min_age']) && !empty($searchData['max_age'])) {
                            $ageText .= "от {$searchData['min_age']} до {$searchData['max_age']} лет";
                        } elseif (!empty($searchData['min_age'])) {
                            $ageText .= "от {$searchData['min_age']} лет";
                        } else {
                            $ageText .= "до {$searchData['max_age']} лет";
                        }
                        $searchDescription[] = $ageText;
                    }
                    
                    if (!empty($searchData['desired_position'])) {
                        $searchDescription[] = 'Должность: "' . $searchData['desired_position'] . '"';
                    }
                    
                    if (!empty($searchData['cities'])) {
                        $citiesCount = count($searchData['cities']);
                        if ($citiesCount == 1) {
                            $searchDescription[] = 'Город: ' . $searchData['cities'][0];
                        } else {
                            $searchDescription[] = 'Города: ' . implode(', ', $searchData['cities']) . " ({$citiesCount} городов)";
                        }
                    }
                    
                    Notification::make()
                        ->title('Поиск выполнен')
                        ->body(implode('. ', $searchDescription))
                        ->success()
                        ->send();
                        
                    // Обновляем таблицу без редиректа
                    $this->dispatch('$refresh');
                }),
            
            \Filament\Actions\Action::make('clear')
                ->label('Очистить поиск')
                ->color('gray')
                ->icon('heroicon-o-x-mark')
                ->action(function () {
                    session()->forget('candidate_search');
                    
                    Notification::make()
                        ->title('Поиск очищен')
                        ->body('Параметры поиска были сброшены')
                        ->info()
                        ->send();
                        
                    // Обновляем таблицу без редиректа
                    $this->dispatch('$refresh');
                }),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }
} 