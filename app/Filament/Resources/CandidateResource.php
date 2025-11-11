<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ViewCandidatePdf;
use App\Filament\Resources\CandidateResource\Pages;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Анкета кандидата';

    protected static ?string $pluralModelLabel = 'Анкеты кандидатов';

    protected static ?string $navigationLabel = 'Анкеты кандидатов';

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
                            ->default(null),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Полное имя')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Select::make('gender')
                            ->label('Пол')
                            ->options([
                                'Мужской' => 'Мужской',
                                'Женский' => 'Женский',
                            ])
                            ->default(null),
                        Forms\Components\Select::make('marital_status')
                            ->label('Семейное положение')
                            ->options([
                                'Холост/Не замужем' => 'Холост/Не замужем',
                                'Женат/Замужем' => 'Женат/Замужем',
                                'Разведен(а)' => 'Разведен(а)',
                                'Вдовец/Вдова' => 'Вдовец/Вдова',
                            ])
                            ->default(null),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Дата рождения'),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Место рождения')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('current_city')
                            ->label('Текущий город')
                            ->maxLength(255)
                            ->default(null),
                    ])->columns(2),

                Forms\Components\Section::make('Образование и опыт')
                    ->schema([
                        Forms\Components\TextInput::make('school')
                            ->label('Школа')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Textarea::make('universities')
                            ->label('Университеты')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('work_experience')
                            ->label('Опыт работы')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('total_experience_years')
                            ->label('Общий стаж (лет)')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('desired_position')
                            ->label('Желаемая должность')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('expected_salary')
                            ->label('Ожидаемая зарплата')
                            ->numeric()
                            ->default(null),
                    ])->columns(2),

                Forms\Components\Section::make('Навыки')
                    ->schema([
                        Forms\Components\Textarea::make('language_skills')
                            ->label('Языковые навыки')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('computer_skills')
                            ->label('Компьютерные навыки')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('has_driving_license')
                            ->label('Наличие водительских прав')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Личная информация')
                    ->schema([
                        Forms\Components\Textarea::make('family_members')
                            ->label('Семья')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hobbies')
                            ->label('Хобби')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('interests')
                            ->label('Интересы')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('books_per_year')
                            ->label('Книг в год')
                            ->options([
                                '0' => '0 книг',
                                '1-2' => '1-2 книги',
                                '3-5' => '3-5 книг',
                                '6-10' => '6-10 книг',
                                '11-15' => '11-15 книг',
                                '16-20' => '16-20 книг',
                                '21-30' => '21-30 книг',
                                '31-50' => '31-50 книг',
                                '50+' => 'Более 50 книг',
                            ])
                            ->default(null),
                        Forms\Components\TextInput::make('entertainment_hours_weekly')
                            ->label('Часов развлечений в неделю')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('educational_hours_weekly')
                            ->label('Часов обучения в неделю')
                            ->numeric()
                            ->default(null),
                    ])->columns(2),

                Forms\Components\Section::make('Тесты и результаты')
                    ->schema([
                        Forms\Components\TextInput::make('mbti_type')
                            ->label('Тип MBTI')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('gallup_pdf')
                            ->label('Gallup PDF')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('step')
                            ->label('Текущий шаг')
                            ->numeric()
                            ->default(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['gallupReports', 'user.gardnerTestResult']))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Полное имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('desired_position')
                    ->label('Желаемая должность')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),
                Tables\Columns\TextColumn::make('total_experience_years')
                    ->label('Стаж (лет)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_salary')
                    ->label('Ожидаемая зарплата')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', ' ') . ' ₸' : null),
                Tables\Columns\TextColumn::make('current_city')
                    ->label('Город')
                    ->searchable(),
                Tables\Columns\TextColumn::make('step')
                    ->label('Шаг')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'danger',
                        '2' => 'warning',
                        '3' => 'info',
                        '4' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('step_parse_gallup')
                    ->label('Статус Gallup')
                    ->badge()
                    ->color(fn (?string $state): string => match (true) {
                        str_contains($state ?? '', 'Ошибка') => 'danger',
                        str_contains($state ?? '', 'Завершено успешно') => 'success',
                        str_contains($state ?? '', 'Изменений не обнаружено') => 'info',
                        str_contains($state ?? '', 'Проверка файла') => 'warning',
                        str_contains($state ?? '', 'Парсинг PDF') => 'warning',
                        str_contains($state ?? '', 'Обновление') => 'info',
                        str_contains($state ?? '', 'Обработка') => 'info',
                        str_contains($state ?? '', 'Импорт') => 'info',
                        str_contains($state ?? '', 'Скачивание') => 'info',
                        default => 'gray',
                    })
                    ->limit(30)
                    ->tooltip(fn (?string $state): string => $state ?? 'Не начат')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_driving_license')
                    ->label('Вод. права')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gallup_status')
                    ->label('Gallup')
                    ->getStateUsing(function (Candidate $record): string {
                        $hasOriginal = !empty($record->gallup_pdf);
                        $reportsCount = $record->gallupReports()->count();

                        if ($hasOriginal && $reportsCount > 0) {
                            return "PDF + {$reportsCount} отчета";
                        } elseif ($hasOriginal) {
                            return 'PDF';
                        } elseif ($reportsCount > 0) {
                            return "{$reportsCount} отчета";
                        }

                        return 'Нет';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'PDF +') => 'success',
                        str_contains($state, 'PDF') => 'info',
                        str_contains($state, 'отчета') => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gardner_status')
                    ->label('Тест Гарднера')
                    ->getStateUsing(function (Candidate $record): string {
                        $testResult = $record->user?->gardnerTestResult;

                        if (!$testResult) {
                            return 'Не пройден';
                        }

                        $answers = is_string($testResult->answers) ? json_decode($testResult->answers, true) : $testResult->answers;
                        $totalQuestions = 56; // 7 вопросов × 8 типов
                        $answeredQuestions = $answers ? count($answers) : 0;
                        $percentage = round(($answeredQuestions / $totalQuestions) * 100);

                        if ($percentage == 100) {
                            return 'Завершен';
                        } else {
                            return "В процессе ({$percentage}%)";
                        }
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === 'Завершен' => 'success',
                        str_contains($state, 'В процессе') => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('step')
                    ->label('Шаг анкеты')
                    ->options([
                        1 => 'Основная информация',
                        2 => 'Дополнительная информация',
                        3 => 'Образование и работа',
                        4 => 'Тесты',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Пол')
                    ->options([
                        'Мужской' => 'Мужской',
                        'Женский' => 'Женский',
                    ]),
                Tables\Filters\Filter::make('has_experience')
                    ->label('С опытом работы')
                    ->query(fn (Builder $query): Builder => $query->where('total_experience_years', '>', 0)),
                Tables\Filters\Filter::make('created_this_month')
                    ->label('Созданы в этом месяце')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month)),
                Tables\Filters\Filter::make('has_gardner_test')
                    ->label('Прошли тест Гарднера')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereHas('user.gardnerTestResult')
                    ),
                Tables\Filters\Filter::make('gardner_test_completed')
                    ->label('Завершили тест Гарднера')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereHas('user.gardnerTestResult', function ($q) {
                            $q->whereRaw('JSON_LENGTH(answers) >= 56');
                        })
                    ),
                Tables\Filters\SelectFilter::make('step_parse_gallup')
                    ->label('Статус парсинга Gallup')
                    ->options([
                        'Проверка файла' => 'Проверка файла',
                        'Парсинг PDF' => 'Парсинг PDF',
                        'Обновление талантов' => 'Обновление талантов',
                        'Обработка отчетов' => 'Обработка отчетов',
                        'Обновление Google Sheets: FMD' => 'Обновление Google Sheets: FMD',
                        'Обновление Google Sheets: DPT' => 'Обновление Google Sheets: DPT',
                        'Обновление Google Sheets: DPs' => 'Обновление Google Sheets: DPs',
                        'Импорт формул: FMD' => 'Импорт формул: FMD',
                        'Импорт формул: DPT' => 'Импорт формул: DPT',
                        'Импорт формул: DPs' => 'Импорт формул: DPs',
                        'Скачивание PDF: FMD' => 'Скачивание PDF: FMD',
                        'Скачивание PDF: DPT' => 'Скачивание PDF: DPT',
                        'Скачивание PDF: DPs' => 'Скачивание PDF: DPs',
                        'Завершено успешно' => 'Завершено успешно',
                        'Изменений не обнаружено' => 'Изменений не обнаружено',
                    ])
                    ->searchable()
                    ->hidden(), // Скрытый фильтр, но доступный при необходимости
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Анкета полная')
                        ->label('Анкета полная')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->url(fn (Candidate $record) => ViewCandidatePdf::getUrl(['candidate' => $record->id, 'type' => 'anketa']))
                        ->modal()
                        ->visible(fn (Candidate $record): bool => 
                            $record->gallup_pdf && Storage::disk('public')->exists($record->gallup_pdf)
                        ),

                    Tables\Actions\Action::make('Анкета урезанная')
                        ->label('Анкета урезанная')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(fn (Candidate $record) => ViewCandidatePdf::getUrl(['candidate' => $record->id, 'type' => 'anketa-reduced']))
                        ->modal()
                        ->visible(fn (Candidate $record): bool => 
                            $record->gallup_pdf && Storage::disk('public')->exists($record->gallup_pdf)
                        ),

                    Tables\Actions\Action::make('downloadDPs')
                        ->label('DPs отчет')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(fn (Candidate $record) => ViewCandidatePdf::getUrl(['candidate' => $record->id, 'type' => 'DPs']))
                        ->modal()
                        ->visible(fn (Candidate $record): bool => $record->gallupReports()->where('type', 'DPs')->exists()),
                    Tables\Actions\Action::make('downloadDPT')
                        ->label('DPT отчет')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->url(fn (Candidate $record) => ViewCandidatePdf::getUrl(['candidate' => $record->id, 'type' => 'DPT']))
                        ->modal()
                        ->visible(fn (Candidate $record): bool => $record->gallupReports()->where('type', 'DPT')->exists()),
                    Tables\Actions\Action::make('downloadFMD')
                        ->label('FMD отчет')
                        ->icon('heroicon-o-document-text')
                        ->color('danger')
                        ->url(fn (Candidate $record) => ViewCandidatePdf::getUrl(['candidate' => $record->id, 'type' => 'FMD']))
                        ->modal()
                        ->visible(fn (Candidate $record): bool => $record->gallupReports()->where('type', 'FMD')->exists()),
                    
                    Tables\Actions\Action::make('refresh_gallup_report')
                        ->label('Обновить gallup Отчет')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function (Candidate $record) {
                            try {
                                app(\App\Http\Controllers\GallupController::class)->parseGallupFromCandidateFile($record);
                                Notification::make()
                                    ->title('Gallup обновлён')
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Ошибка обновления')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Candidate $record): bool => 
                            $record->gallup_pdf && Storage::disk('public')->exists($record->gallup_pdf)
                        ),
                        
                ])
                    ->label('Gallup')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->button()
                    ->visible(fn (Candidate $record): bool =>
                        ($record->gallup_pdf && Storage::disk('public')->exists($record->gallup_pdf)) || 
                        $record->gallupReports()->exists()
                    ),
                    
                // Кнопка редактирования анкеты (доступна только администраторам)
                // Перемещена на место кнопки "Удалить" для лучшей видимости
                Tables\Actions\Action::make('edit_form')
                    ->label('Редактировать')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->url(fn (Candidate $record): string => route('candidate.form', $record->id))
                    ->openUrlInNewTab(false)
                    ->visible(fn (): bool => auth()->user()->is_admin ?? false),
                    
                // Кнопка удаления временно отключена
                // Tables\Actions\DeleteAction::make()
                //     ->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Массовое удаление временно отключено
                    // Tables\Actions\DeleteBulkAction::make()
                    //     ->label('Удалить'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Candidate $record): string => route('candidate.report', $record));
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            // 'edit' => Pages\EditCandidate::route('/{record}/edit'), // Отключено - анкеты нельзя редактировать
        ];
    }
}
