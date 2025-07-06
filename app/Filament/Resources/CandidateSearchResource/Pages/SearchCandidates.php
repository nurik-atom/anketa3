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
            Forms\Components\Section::make('Параметры поиска кандидатов')
                ->schema([
                    Forms\Components\Select::make('report_type')
                        ->label('Тип отчета')
                        ->options([
                            'DPs' => 'DPs - Диагностический психологический скрининг',
                            'DPT' => 'DPT - Диагностический психологический тест', 
                            'FMD' => 'FMD - Функциональная медицинская диагностика',
                        ])
                        ->default('DPs')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('characteristics', []);
                        })
                        ->required(),

                    Forms\Components\CheckboxList::make('characteristics')
                        ->label('Характеристики для поиска')
                        ->options(function (callable $get) {
                            $reportType = $get('report_type') ?? 'DPs';
                            
                            // Получаем все доступные характеристики для выбранного типа отчета из индексов
                            $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                            if (!$sheet) {
                                return [];
                            }
                            
                            $characteristics = GallupReportSheetIndex::where('gallup_report_sheet_id', $sheet->id)
                                ->get(['type', 'name'])
                                ->groupBy('type');
                            
                            $options = [];
                            foreach ($characteristics as $type => $items) {
                                foreach ($items as $item) {
                                    $key = trim($type) . '|' . $item->name;
                                    $options[$key] = trim($type) . ': ' . $item->name;
                                }
                            }
                            
                            return $options;
                        })
                        ->reactive()
                        ->columns(2)
                        ->required()
                        ->minItems(1),

                    Forms\Components\TextInput::make('min_value')
                        ->label('Минимальное значение')
                        ->numeric()
                        ->default(70)
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->required(),
                ])
                ->columns(1),
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
                    // Сохраняем параметры поиска в сессии
                    session(['candidate_search' => $data]);
                    
                    // Показываем уведомление
                    $characteristicsCount = count($data['characteristics'] ?? []);
                    
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