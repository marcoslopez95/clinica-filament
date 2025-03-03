<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\NoReturn;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    #[NoReturn]
    protected function afterSave():void
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
