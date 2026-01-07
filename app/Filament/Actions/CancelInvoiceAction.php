<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Action as FormAction;
use Filament\Forms\Components\Textarea;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Filament\Notifications\Notification;

class CancelInvoiceAction
{
    public static function makeTable(): TableAction
    {
        return TableAction::make('cancel_table')
            ->label('Anular')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->color('warning')
            ->modalHeading('Confirmar anulación')
            ->modalDescription('
                Esta acción no se puede revertir. Una vez anulada,
                la factura quedará registrada como cancelada permanentemente.'
            )
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->form([
                Textarea::make('cancellation_reason')
                    ->label('Motivo de anulación')
                    ->required()
                    ->rows(1),
            ])
            ->action(fn (Invoice $record, array $data) => self::handleCancel($record, $data))
            ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED);
    }

    public static function makeForm(): FormAction
    {
        return FormAction::make('cancel_form')
            ->label('Anular')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->color('warning')
            ->modalHeading('Confirmar anulación')
            ->modalDescription(
                'Esta acción no se puede revertir. Una vez anulada, 
                la factura quedará registrada como cancelada permanentemente.'
            )
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->form([
                Textarea::make('cancellation_reason')
                    ->label('Motivo de anulación')
                    ->required()
                    ->rows(1),
            ])
            ->action(fn (Invoice $record, array $data) => self::handleCancel($record, $data))
            ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED);
    }

    private static function handleCancel(Invoice $record, array $data): void
    {
        $record->update([
            'status' => InvoiceStatus::CANCELLED,
            'cancellation_reason' => $data['cancellation_reason'],
        ]);

        Notification::make()
            ->title('Factura anulada correctamente')
            ->success()
            ->send();
    }
}
