<?php

namespace App\Filament\Resources\ModoInventariResource\Pages;

use App\Filament\Resources\InventoryModeResource;
use App\Filament\Resources\InventoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListInventoryModes extends ListRecords
{
    protected static string $resource = InventoryModeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('volver')
                ->label('Volver a inventario')
                ->url(InventoryResource::getUrl('index')),
        ];
    }
}
