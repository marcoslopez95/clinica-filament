<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use App\Enums\InvoiceType;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateCotizacion extends CreateRecord
{
    protected static string $resource = CotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::COTIZACION->value;
        return $data;
    }
}
