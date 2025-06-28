<?php

namespace App\Filament\Resources\GallupReportSheetResource\Pages;

use App\Filament\Resources\GallupReportSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGallupReportSheets extends ListRecords
{
    protected static string $resource = GallupReportSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Report Sheet'),
        ];
    }
}
