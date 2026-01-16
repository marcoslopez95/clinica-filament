<?php

namespace App\Filament\Resources\OperatingRoomResource\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Filament\Resources\OperatingRoomResource;
use App\Models\Invoice;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = OperatingRoomResource::class;

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
        $data['invoice_type'] = InvoiceType::DEFAULT->value;
        return $data;
    }
}
