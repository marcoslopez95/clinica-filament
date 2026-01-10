<?php

namespace App\Filament\Resources\ModoInventariResource\Pages;

use App\Filament\Resources\ModoInventariResource;
use Filament\Resources\Pages\ListRecords;

class ListModoInventaris extends ListRecords
{
    protected static string $resource = ModoInventariResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
