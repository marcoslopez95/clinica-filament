<?php

namespace App\Filament\Resources\HozpitaliacionesResource\Pages;

use App\Enums\InvoiceType;
use App\Filament\Resources\HozpitaliacionesResource;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateHozpitaliacion extends CreateRecord
{
    protected static string $resource = HozpitaliacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterCreate(): void
    {
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::HOSPITALIZATION->value;
        return $data;
    }
}
