<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Forms\Components\TextInput::make('books_per_year')
                            ->label('Книг в год')
                            ->numeric()
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
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Полное имя')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('desired_position')
                    ->label('Желаемая должность')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('total_experience_years')
                    ->label('Стаж (лет)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_salary')
                    ->label('Зарплата')
                    ->numeric()
                    ->sortable()
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
                Tables\Columns\IconColumn::make('has_driving_license')
                    ->label('Права')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
