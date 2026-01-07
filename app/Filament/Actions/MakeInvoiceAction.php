<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Action as FormAction;
use App\Models\Invoice;
use App\Enums\InvoiceType;

class MakeInvoiceAction
{
    public static function makeTable(): TableAction
    {
        return TableAction::make('make_invoice')
            ->label('Facturar')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'invoice_type' => InvoiceType::DEFAULT->value,
            ]))
            ->hidden(fn (Invoice $record) => $record->invoice_type === InvoiceType::DEFAULT->value);
    }

    public static function makeForm(): FormAction
    {
        return FormAction::make('make_invoice')
            ->label('Facturar')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'invoice_type' => InvoiceType::DEFAULT->value,
            ]))
            ->hidden(fn (Invoice $record) => $record->invoice_type === InvoiceType::DEFAULT->value);
    }
}
