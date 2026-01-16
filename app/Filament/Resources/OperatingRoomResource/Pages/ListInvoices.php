<?php

namespace App\Filament\Resources\OperatingRoomResource\Pages;

use App\Filament\Resources\OperatingRoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = OperatingRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
