<?php

namespace App\Filament\Resources\ConsultationResource\Pages;

use App\Enums\InvoiceType;
use App\Filament\Resources\ConsultationResource;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateConsultation extends CreateRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions here
        ];
    }

    protected function afterCreate(): void {}

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::CONSULT->value;
        return $data;
    }
}
