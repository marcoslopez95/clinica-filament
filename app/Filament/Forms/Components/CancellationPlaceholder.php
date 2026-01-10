<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Placeholder;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class CancellationPlaceholder
{
    public static function make(): Placeholder
    {
        return Placeholder::make('cancellation_reason')
            ->label('Motivo de anulación')
            ->content(fn (?Invoice $record)
                : string => $record->cancellation_reason ?? '-')
            ->visible(fn ($get) 
                => $get('status') == InvoiceStatus::CANCELLED->value);
    }
}
