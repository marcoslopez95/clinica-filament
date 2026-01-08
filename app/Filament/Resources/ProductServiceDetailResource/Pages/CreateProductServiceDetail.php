<?php

namespace App\Filament\Resources\ProductServiceDetailResource\Pages;

use App\Filament\Resources\ProductServiceDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductServiceDetail extends CreateRecord
{
    protected static string $resource = ProductServiceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
