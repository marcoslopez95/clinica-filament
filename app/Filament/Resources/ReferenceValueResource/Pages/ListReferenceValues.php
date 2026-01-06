<?php

namespace App\Filament\Resources\ReferenceValueResource\Pages;

use App\Filament\Resources\ReferenceValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferenceValues extends ListRecords
{
    protected static string $resource = ReferenceValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear ' . ReferenceValueResource::getModelLabel()),
        ];
    }
}
