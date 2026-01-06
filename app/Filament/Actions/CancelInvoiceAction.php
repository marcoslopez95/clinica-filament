<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class CancelInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('Cancel')
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'status' => InvoiceStatus::CANCELLED,
            ]))
            ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED);
    }
}