<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Action as FormAction;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class CancelInvoiceAction
{

    public static function makeTable(): TableAction
    {
        return TableAction::make('cancel')
            ->label('Anular')
            ->icon('heroicon-o-x-circle')
            ->color('warning')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'status' => InvoiceStatus::CANCELLED,
            ]))
            ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED);
    }

    public static function makeForm(): FormAction
    {
        return FormAction::make('cancel')
            ->label('Anular')
            ->icon('heroicon-o-x-circle')
            ->color('warning')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'status' => InvoiceStatus::CANCELLED,
            ]))
            ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED);
    }
}