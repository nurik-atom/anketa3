<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GardnerTestResultResource\Pages;
use App\Filament\Resources\GardnerTestResultResource\RelationManagers;
use App\Models\GardnerTestResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GardnerTestResultResource extends Resource
{
    protected static ?string $model = GardnerTestResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $modelLabel = 'Результат теста Гарднера';
    
    protected static ?string $pluralModelLabel = 'Результаты тестов Гарднера';

    protected static ?string $navigationLabel = 'Тесты Гарднера';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\KeyValue::make('answers')
                            ->label('Ответы на вопросы')
                            ->keyLabel('Вопрос')
                            ->valueLabel('Ответ')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Результаты по типам интеллекта')
                    ->schema([
                        Forms\Components\KeyValue::make('results')
                            ->label('Баллы по типам')
                            ->keyLabel('Тип интеллекта')
                            ->valueLabel('Баллы')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state) {
                                if (!is_array($state)) return $state;
                                
                                $formatted = [];
                                foreach ($state as $key => $value) {
                                    $labels = [
                                        'linguistic' => 'Лингвистический',
                                        'logical_mathematical' => 'Логико-математический',
                                        'spatial' => 'Пространственный',
                                        'bodily_kinesthetic' => 'Телесно-кинестетический',
                                        'musical' => 'Музыкальный',
                                        'interpersonal' => 'Межличностный',
                                        'intrapersonal' => 'Внутриличностный',
                                        'naturalistic' => 'Натуралистический',
                                    ];
                                    
                                    $label = $labels[$key] ?? $key;
                                    $formatted[$label] = $value . ' из 7';
                                }
                                return $formatted;
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dominant_intelligence')
                    ->label('Доминирующий интеллект')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->results) return 'Не определен';
                        
                        $results = is_string($record->results) ? json_decode($record->results, true) : $record->results;
                        if (!is_array($results)) return 'Ошибка данных';
                        
                        $maxScore = max($results);
                        $dominantType = array_search($maxScore, $results);
                        
                        $labels = [
                            'linguistic' => 'Лингвистический',
                            'logical_mathematical' => 'Логико-математический',
                            'spatial' => 'Пространственный',
                            'bodily_kinesthetic' => 'Телесно-кинестетический',
                            'musical' => 'Музыкальный',
                            'interpersonal' => 'Межличностный',
                            'intrapersonal' => 'Внутриличностный',
                            'naturalistic' => 'Натуралистический',
                        ];
                        
                        return ($labels[$dominantType] ?? $dominantType) . " ({$maxScore}/7)";
                    })
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Прогресс')
                    ->formatStateUsing(function ($state, $record) {
                        $totalQuestions = 56; // 7 вопросов × 8 типов
                        $answeredQuestions = $record->answers ? count(json_decode($record->answers, true)) : 0;
                        $percentage = round(($answeredQuestions / $totalQuestions) * 100);
                        return $percentage . '%';
                    })
                    ->badge()
                    ->color(function ($state, $record): string {
                        $totalQuestions = 56;
                        $answeredQuestions = $record->answers ? count(json_decode($record->answers, true)) : 0;
                        $percentage = ($answeredQuestions / $totalQuestions) * 100;
                        
                        if ($percentage == 100) return 'success';
                        if ($percentage >= 75) return 'warning';
                        if ($percentage >= 50) return 'info';
                        return 'danger';
                    }),
                Tables\Columns\TextColumn::make('average_score')
                    ->label('Средний балл')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->results) return 'Н/Д';
                        
                        $results = is_string($record->results) ? json_decode($record->results, true) : $record->results;
                        if (!is_array($results)) return 'Ошибка';
                        
                        $average = round(array_sum($results) / count($results), 1);
                        return $average . '/7';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата прохождения')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('completed')
                    ->label('Завершенные тесты')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('JSON_LENGTH(answers) >= 56');
                    }),
                Tables\Filters\Filter::make('incomplete')
                    ->label('Незавершенные тесты')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('JSON_LENGTH(answers) < 56');
                    }),
                Tables\Filters\Filter::make('this_week')
                    ->label('За эту неделю')
                    ->query(function (Builder $query): Builder {
                        return $query->where('created_at', '>=', now()->startOfWeek());
                    }),
                Tables\Filters\Filter::make('this_month')
                    ->label('За этот месяц')
                    ->query(function (Builder $query): Builder {
                        return $query->whereMonth('created_at', now()->month);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_results')
                    ->label('Посмотреть результаты')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->url(function (GardnerTestResult $record): string {
                        return route('candidate.test') . '?user=' . $record->user_id;
                    }),
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                Tables\Actions\DeleteAction::make()
                    ->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListGardnerTestResults::route('/'),
            'create' => Pages\CreateGardnerTestResult::route('/create'),
            'edit' => Pages\EditGardnerTestResult::route('/{record}/edit'),
        ];
    }
}
