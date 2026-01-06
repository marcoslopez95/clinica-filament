<?php

namespace App\Filament\Resources\LaboratorioResource\Pages;

use App\Enums\InvoiceType;
use App\Filament\Resources\LaboratorioResource;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateLaboratorio extends CreateRecord
{
    protected static string $resource = LaboratorioResource::class;

    protected function afterCreate(): void
    {
        $this->getRecord()->updateStatusIfPaid();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::LABORATORY->value;
        return $data;
    }
}
