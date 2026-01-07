<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Actions\CancelInvoiceAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditCotizacion extends EditRecord
{
    protected static string $resource = CotizacionResource::class;

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
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave():void
    {
        $this->refreshTotal();
    }
}
