<?php

namespace App\Filament\Resources\GallupReportSheetResource\Pages;

use App\Filament\Resources\GallupReportSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGallupReportSheet extends CreateRecord
{
    protected static string $resource = GallupReportSheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
