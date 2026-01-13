<?php

namespace App\Filament\Resources\HozpitaliacionesResource\Pages;

use App\Filament\Resources\HozpitaliacionesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHozpitaliaciones extends ListRecords
{
    protected static string $resource = HozpitaliacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
