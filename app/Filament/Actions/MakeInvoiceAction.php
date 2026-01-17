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
                'is_quotation' => false,
            ]))
            ->visible(fn (Invoice $record) => $record->is_quotation);
    }

    public static function makeForm(): FormAction
    {
        return FormAction::make('make_invoice')
            ->label('Facturar')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->requiresConfirmation()
            ->action(fn (Invoice $record) => $record->update([
                'is_quotation' => false,
            ]))
            ->visible(fn (Invoice $record) => $record->is_quotation);
    }
}
