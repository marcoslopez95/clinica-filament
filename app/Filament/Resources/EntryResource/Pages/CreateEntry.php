<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Enums\InvoiceType;
use App\Filament\Resources\EntryResource;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function afterCreate(): void
    {
        
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Supplier::class;
        $data['invoice_type'] = InvoiceType::INVENTORY->value;
        return $data;
    }
}
