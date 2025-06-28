<?php

namespace App\Filament\Resources\GallupReportSheetResource\Pages;

use App\Filament\Resources\GallupReportSheetResource;
use App\Models\GallupReportSheet;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGallupReportSheet extends EditRecord
{
    protected static string $resource = GallupReportSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->action(function () {
                    $original = $this->record;
                    $duplicate = $original->replicate();
                    $duplicate->name_report = $original->name_report . ' (Copy)';
                    $duplicate->save();

                    // Duplicate indices
                    foreach ($original->indices as $index) {
                        $duplicateIndex = $index->replicate();
                        $duplicateIndex->gallup_report_sheet_id = $duplicate->id;
                        $duplicateIndex->save();
                    }

                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $duplicate]));
                })
                ->requiresConfirmation()
                ->modalDescription('This will create a copy of this report sheet with all its indices.'),
            Actions\DeleteAction::make(),
        ];
    }
}
