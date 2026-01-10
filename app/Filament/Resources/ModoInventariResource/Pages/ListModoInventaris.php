<?php

namespace App\Filament\Resources\ModoInventariResource\Pages;

use App\Filament\Resources\ModoInventariResource;
use App\Filament\Resources\InventoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListModoInventaris extends ListRecords
{
    protected static string $resource = ModoInventariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('volver')
                ->label('Volver a inventario')
                ->url(InventoryResource::getUrl('index')),
        ];
    }
}
