<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Actions\CancelInvoiceAction;
use \App\Filament\Actions\MakeInvoiceAction;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected $listeners = [
        'refreshTotal' => 'refreshTotal',
    ];

    public function refreshTotal(): void
    {
        $this->record->refresh();
        $total = $this->record->details()->sum('subtotal');
        $this->record->update(['total' => $total]);
        $this->data['total'] = $total;
    }

    protected function getHeaderActions(): array
    {
        return [
            CancelInvoiceAction::makeForm(),
            MakeInvoiceAction::makeForm(),
        ];
    }

    protected function afterSave():void
    {
        $this->refreshTotal();
    }
}
