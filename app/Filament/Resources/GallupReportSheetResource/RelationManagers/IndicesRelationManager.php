<?php

namespace App\Filament\Resources\GallupReportSheetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndicesRelationManager extends RelationManager
{
    protected static string $relationship = 'indices';

    protected static ?string $title = 'Talent Indices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->label('Type')
                    ->placeholder('e.g., Профессиональные функции, Потенциал к управленчеству, Уровни мышления')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Talent Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Ремесло, Аналитика, Исследования'),
                Forms\Components\TextInput::make('index')
                    ->label('Column Index')
                    ->required()
                    ->placeholder('e.g., P90, P108')
                    ->helperText('Column position in Google Sheets'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'talent' => 'success',
                        'theme' => 'warning',
                        'category' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Talent Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('index')
                    ->label('Column Index')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'talent' => 'Talent',
                        'theme' => 'Theme',
                        'category' => 'Category',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('index');
    }
}
