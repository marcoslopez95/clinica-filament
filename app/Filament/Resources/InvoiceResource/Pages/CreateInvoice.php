<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions here
        ];
    }

    protected function afterCreate():void
    {
        $this->getRecord()->updateStatusIfPaid();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoiceable_type'] = Patient::class;
        $data['invoice_type'] = InvoiceType::DEFAULT->value;
        return $data;
    }
}
