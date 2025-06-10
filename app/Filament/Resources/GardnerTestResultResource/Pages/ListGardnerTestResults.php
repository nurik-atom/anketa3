<?php

namespace App\Filament\Resources\GardnerTestResultResource\Pages;

use App\Filament\Resources\GardnerTestResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGardnerTestResults extends ListRecords
{
    protected static string $resource = GardnerTestResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
