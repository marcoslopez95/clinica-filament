<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Filament\Resources\ModoInventariResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('modo_inventario')
                ->label('Modo inventario')
                ->url(ModoInventariResource::getUrl('index')),

            CreateAction::make()->label('Crear ' . InventoryResource::getModelLabel()),
        ];
    }
}
