<?php

namespace App\Filament\Resources\ProductServiceDetailResource\Pages;

use App\Filament\Resources\ProductServiceDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductServiceDetails extends ListRecords
{
    protected static string $resource = ProductServiceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
