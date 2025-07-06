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
            Forms\Components\Select::make('report_type')
                ->label('Тип отчета')
                ->options([
                    'DPs' => 'DPs - Диагностический психологический скрининг',
                    'DPT' => 'DPT - Диагностический психологический тест', 
                    'FMD' => 'FMD - Функциональная медицинская диагностика',
                ])
                ->default(session('candidate_search.report_type', 'DPs'))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    // При смене типа отчета очищаем все поля характеристик
                    // (это приведет к перестройке формы с новыми полями)
                })
                ->required(),

            Forms\Components\Group::make()
                ->schema(function (callable $get) {
                    $reportType = $get('report_type') ?? 'DPs';
                    
                    // Получаем все доступные характеристики для выбранного типа отчета из индексов
                    $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                    if (!$sheet) {
                        return [];
                    }
                    
                    $characteristics = GallupReportSheetIndex::where('gallup_report_sheet_id', $sheet->id)
                        ->get(['type', 'name'])
                        ->groupBy('type');
                    
                    // Получаем сохраненные характеристики для предварительного заполнения
                    $savedCharacteristics = session('candidate_search.characteristics', []);
                    
                    $sections = [];
                    foreach ($characteristics as $type => $items) {
                        $typeName = trim($type);
                        $fieldName = 'characteristics_' . str_replace([' ', ':', '(', ')'], '_', strtolower($typeName));
                        
                        $options = [];
                        $defaultValues = [];
                        
                        foreach ($items as $item) {
                            $key = trim($type) . '|' . $item->name;
                            $options[$key] = $item->name;
                            
                            // Проверяем, была ли эта характеристика выбрана ранее
                            if (in_array($key, $savedCharacteristics)) {
                                $defaultValues[] = $key;
                            }
                        }
                        
                        if (!empty($options)) {
                            $sections[] = Forms\Components\Fieldset::make($typeName)
                                ->schema([
                                    Forms\Components\CheckboxList::make($fieldName)
                                        ->label('')
                                        ->options($options)
                                        ->columns(2)
                                        ->default($defaultValues)
                                ]);
                        }
                    }
                    
                    return $sections;
                })
                ->reactive()
                ->columns(1),

            Forms\Components\TextInput::make('min_value')
                ->label('Минимальное значение')
                ->numeric()
                ->default(session('candidate_search.min_value', 70))
                ->minValue(0)
                ->maxValue(100)
                ->suffix('%')
                ->required(),
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
                ->action(function (array $data) {
                    // Собираем все характеристики из разных полей
                    $allCharacteristics = [];
                    foreach ($data as $key => $value) {
                        if (str_starts_with($key, 'characteristics_') && is_array($value)) {
                            $allCharacteristics = array_merge($allCharacteristics, $value);
                        }
                    }
                    
                    // Проверяем, что выбрана хотя бы одна характеристика
                    if (empty($allCharacteristics)) {
                        Notification::make()
                            ->title('Ошибка')
                            ->body('Выберите хотя бы одну характеристику для поиска')
                            ->danger()
                            ->send();
                        return;
                    }
                    
                    // Подготавливаем данные для сохранения
                    $searchData = [
                        'report_type' => $data['report_type'],
                        'characteristics' => $allCharacteristics,
                        'min_value' => $data['min_value']
                    ];
                    
                    // Сохраняем параметры поиска в сессии
                    session(['candidate_search' => $searchData]);
                    
                    // Показываем уведомление
                    $characteristicsCount = count($allCharacteristics);
                    
                    Notification::make()
                        ->title('Поиск выполнен')
                        ->body("Найдено кандидатов по {$characteristicsCount} характеристикам с минимальным значением {$data['min_value']}%")
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