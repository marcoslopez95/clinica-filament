<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource;

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
            Action::make('Cancelar')
                ->label('Cancelar')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update(['status' => InvoiceStatus::CANCELLED]))
                ->hidden(fn () => $this->record->status === InvoiceStatus::CANCELLED),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave():void
    {
        $this->refreshTotal();
        $this->getRecord()->updateStatusIfPaid();
    }
}
