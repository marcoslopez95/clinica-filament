<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\EntryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditEntry extends EditRecord
{
    protected static string $resource = EntryResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['total'] = $this->record->details()->sum('subtotal');

        return $data;
    }

    protected function afterSave(): void
    {
        $this->refreshTotal();
    }
}
