<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Enums\InvoiceType;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions here
        ];
    }

    protected function afterCreate():void
    {
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::COTIZACION->value;
        return $data;
    }
}
