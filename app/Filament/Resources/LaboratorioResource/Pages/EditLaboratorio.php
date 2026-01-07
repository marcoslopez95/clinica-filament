<?php

namespace App\Filament\Resources\LaboratorioResource\Pages;

use App\Filament\Resources\LaboratorioResource;
use App\Enums\InvoiceStatus;
use Filament\Actions\Action;
use App\Filament\Actions\CancelInvoiceAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLaboratorio extends EditRecord
{
    protected static string $resource = LaboratorioResource::class;

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

    protected function afterSave(): void
    {
        $this->refreshTotal();
    }
}
