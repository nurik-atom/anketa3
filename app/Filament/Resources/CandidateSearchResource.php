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
                Forms\Components\Section::make('Параметры поиска')
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->label('Тип отчета')
                            ->options([
                                'DPs' => 'DPs',
                                'DPT' => 'DPT', 
                                'FMD' => 'FMD',
                            ])
                            ->default('DPs')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('characteristics', [])),

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
                            ->columns(2),

                        Forms\Components\TextInput::make('min_value')
                            ->label('Минимальное значение')
                            ->numeric()
                            ->default(70)
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                Tables\Columns\TextColumn::make('desired_position')
                    ->label('Желаемая позиция')
                    ->limit(30),

                Tables\Columns\TextColumn::make('matching_characteristics')
                    ->label('Подходящие характеристики')
                    ->getStateUsing(function (Candidate $record) {
                        // Получаем параметры поиска из сессии или формы
                        $search = session('candidate_search', []);
                        if (empty($search['characteristics'])) {
                            return 'Не указаны параметры поиска';
                        }

                        $minValue = $search['min_value'] ?? 70;
                        $reportType = $search['report_type'] ?? 'DPs';
                        $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                        
                        if (!$sheet) {
                            return 'Отчет не найден';
                        }

                        $matches = [];
                        foreach ($search['characteristics'] as $characteristic) {
                            if (strpos($characteristic, '|') === false) continue;
                            
                            [$type, $name] = explode('|', $characteristic, 2);
                            
                            $value = GallupReportSheetValue::where('gallup_report_sheet_id', $sheet->id)
                                ->where('candidate_id', $record->id)
                                ->where('type', trim($type))
                                ->where('name', $name)
                                ->where('value', '>=', $minValue)
                                ->first();
                            
                            if ($value) {
                                $matches[] = $name . ': ' . $value->value . '%';
                            }
                        }

                        return empty($matches) ? 'Нет совпадений' : implode(', ', $matches);
                    })
                    ->wrap()
                    ->color(fn (string $state): string => $state === 'Нет совпадений' ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('report_type')
                    ->label('Тип отчета')
                    ->getStateUsing(function () {
                        return session('candidate_search.report_type', 'DPs');
                    })
                    ->badge()
                    ->color('primary'),
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
                // Получаем параметры поиска из сессии
                $search = session('candidate_search', []);
                
                if (empty($search['characteristics'])) {
                    // Если параметры поиска не заданы, показываем пустую таблицу
                    return $query->whereRaw('1 = 0');
                }

                $minValue = $search['min_value'] ?? 70;
                $reportType = $search['report_type'] ?? 'DPs';
                $sheet = GallupReportSheet::where('name_report', $reportType)->first();
                
                if (!$sheet) {
                    return $query->whereRaw('1 = 0');
                }

                // Собираем ID кандидатов, которые соответствуют хотя бы одной характеристике
                $allCandidateIds = collect();
                
                foreach ($search['characteristics'] as $characteristic) {
                    if (strpos($characteristic, '|') === false) continue;
                    
                    [$type, $name] = explode('|', $characteristic, 2);
                    
                    $ids = GallupReportSheetValue::where('gallup_report_sheet_id', $sheet->id)
                        ->where('type', trim($type))
                        ->where('name', $name)
                        ->where('value', '>=', $minValue)
                        ->pluck('candidate_id');
                    
                    $allCandidateIds = $allCandidateIds->merge($ids);
                }

                $uniqueCandidateIds = $allCandidateIds->unique();

                if ($uniqueCandidateIds->isEmpty()) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('id', $uniqueCandidateIds->toArray());
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