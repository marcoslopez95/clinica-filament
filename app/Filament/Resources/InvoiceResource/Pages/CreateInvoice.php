<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function afterCreate():void
    {
        /**
         * @var $invoice Invoice
         */
        $invoice = $this->getRecord();
        if($invoice->isComplete()){
            $invoice->status = InvoiceStatus::CLOSED->value;
            $invoice->save();
        }
    }
}
