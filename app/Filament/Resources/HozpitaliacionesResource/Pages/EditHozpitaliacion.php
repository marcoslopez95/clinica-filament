<?php

namespace App\Filament\Resources\HozpitaliacionesResource\Pages;

use App\Filament\Resources\HozpitaliacionesResource;
use App\Filament\Actions\CancelInvoiceAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHozpitaliacion extends EditRecord
{
    protected static string $resource = HozpitaliacionesResource::class;

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
        ];
    }

    protected function afterSave(): void
    {
        $this->refreshTotal();
    }
}
